<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicForm;
use App\Models\DynamicFormField;
use App\Models\DynamicFormResponse;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EmployeeDynamicFormController extends Controller
{
    /**
     * Display a listing of the dynamic forms created by the authenticated employee.
     */
    public function index()
    {
        $employeeId = Auth::id();
        $forms = DynamicForm::where('employee_id', $employeeId)->with('fields')->paginate(10);
        return view('employees.dynamic-forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new dynamic form.
     */
    public function create()
    {
        return view('employees.dynamic-forms.create');
    }

    /**
     * Store a newly created dynamic form in storage.
     */
    public function store(Request $request)
    {

        $isAjax = $request->ajax() || $request->wantsJson();
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_draft' => 'sometimes|boolean',
            'fields' => 'required|array|min:1',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,email,number,date,select,checkbox,radio,textarea,file',
            'fields.*.is_required' => 'sometimes|boolean',
            'fields.*.sort_order' => 'required|integer|min:0',
            'fields.*.field_options' => 'nullable|string',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.help_text' => 'nullable|string',
        ], [
            'fields.required' => 'At least one field is required for the form.',
            'fields.min' => 'You must provide at least one field for the form.',
            'fields.*.field_label.required' => 'Field label is required for all fields.',
            'fields.*.field_type.required' => 'Field type is required for all fields.',
            'fields.*.field_type.in' => 'Invalid field type selected.',
            'fields.*.sort_order.required' => 'Field order is required for all fields.',
        ]);

        try {
            DB::beginTransaction();

            $form = DynamicForm::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_draft' => $request->boolean('is_draft'),
                'settings' => $request->settings ?? null,
                'employee_id' => Auth::id(), // Assign the form to the authenticated employee
            ]);

            foreach ($request->fields as $index => $fieldData) {
                $fieldName = Str::slug($fieldData['field_label']);
                DynamicFormField::create([
                    'dynamic_form_id' => $form->id,
                    'field_name' => $fieldName,
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => !empty($fieldData['field_options'])
                        ? json_encode(array_map('trim', explode("\n", $fieldData['field_options'])))
                        : null,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $fieldData['sort_order'],
                    'validation_rules' => !empty($fieldData['validation_rules']) ? json_encode($fieldData['validation_rules']) : null,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                ]);
            }

            DB::commit();
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form created successfully!',
                    'redirect' => route('employees.dynamic-forms.index'),
                ]);
            }
            return redirect()->route('employees.dynamic-forms.index')->with('success', $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create dynamic form: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $errorMessage = 'Failed to create form. Please try again.';
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => $e->getMessage()],
                ], 500);
            }
            return back()->withInput()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Display the specified dynamic form.
     */
    public function show(string $id)
    {
        $employeeId = Auth::id();
        $form = DynamicForm::where('employee_id', $employeeId)->with(['fields', 'responses.client.user'])->findOrFail($id);
        return view('employees.dynamic-forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified dynamic form.
     */
    public function edit(string $id)
    {
        $employeeId = Auth::id();
        $form = DynamicForm::where('employee_id', $employeeId)->with('fields')->findOrFail($id);
        return view('employees.dynamic-forms.edit', compact('form'));
    }

    /**
     * Update the specified dynamic form in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Update form request data:', $request->all());
        $isAjax = $request->ajax() || $request->wantsJson();

        // Preprocess fields for select, radio, checkbox types
        $fields = $request->input('fields', []);
        foreach ($fields as $index => &$fieldData) {
            if (in_array($fieldData['field_type'] ?? '', ['select', 'radio', 'checkbox'])) {
                $options = $fieldData['field_options'] ?? '';
                if (!$options) {
                    Log::warning("Missing field_options for index {$index}, setting default");
                    $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                } else {
                    try {
                        $options = json_decode($options, true);
                        if (!is_array($options) || count(array_filter($options, 'trim')) < 2) {
                            Log::warning("Invalid or insufficient field_options for index {$index}, setting default");
                            $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                        } else {
                            $fieldData['field_options'] = json_encode(array_filter($options, 'trim'));
                        }
                    } catch (\Exception $e) {
                        Log::warning("Error parsing field_options for index {$index}, setting default", ['error' => $e->getMessage()]);
                        $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                    }
                }
            }
        }
        unset($fieldData);
        $request->merge(['fields' => $fields]);

        // Validation rules
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_draft' => 'sometimes|boolean',
            'fields' => 'required|array|min:1',
            'fields.*.field_id' => 'nullable|integer|exists:dynamic_form_fields,id',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,email,number,date,select,checkbox,radio,textarea,file',
            'fields.*.is_required' => 'sometimes|boolean',
            'fields.*.sort_order' => 'required|integer|min:0',
            'fields.*.field_options' => ['nullable', function ($attribute, $value, $fail) use ($request) {
                $index = explode('.', $attribute)[1];
                $fieldType = $request->input("fields.$index.field_type");
                if (in_array($fieldType, ['select', 'radio', 'checkbox'])) {
                    if (!$value) {
                        $fail("The $attribute field is required for $fieldType fields.");
                    }
                    try {
                        $options = json_decode($value, true);
                        if (!is_array($options) || count(array_filter($options, 'trim')) < 2) {
                            $fail("The $attribute field must contain at least 2 valid options.");
                        }
                    } catch (\Exception $e) {
                        $fail("The $attribute field must be a valid JSON array.");
                    }
                }
            }],
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.help_text' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $employeeId = Auth::id();
            $form = DynamicForm::where('employee_id', $employeeId)->findOrFail($id);

            // Update form details
            $form->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_active' => $request->boolean('is_active'),
                'is_draft' => $request->boolean('is_draft'),
                'employee_id' => $employeeId,
            ]);

            $existingFieldIds = $form->fields->pluck('id')->toArray();
            $fieldsToKeepIds = [];
            $usedFieldNames = [];

            if (!empty($validated['fields'])) {
                foreach ($validated['fields'] as $index => $fieldData) {
                    $fieldId = $fieldData['field_id'] ?? null;
                    $baseFieldName = Str::slug($fieldData['field_label']);
                    $fieldName = $baseFieldName;

                    if (in_array($fieldName, $usedFieldNames)) {
                        $suffix = 1;
                        while (in_array("{$fieldName}-{$suffix}", $usedFieldNames)) {
                            $suffix++;
                        }
                        $fieldName = "{$fieldName}-{$suffix}";
                    }
                    $usedFieldNames[] = $fieldName;

                    $attributes = [
                        'field_label' => $fieldData['field_label'],
                        'field_type' => $fieldData['field_type'],
                        'is_required' => $request->boolean("fields.{$index}.is_required"),
                        'sort_order' => $fieldData['sort_order'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'help_text' => $fieldData['help_text'] ?? null,
                        'field_options' => $fieldData['field_options'] ?? null,
                        'field_name' => $fieldName,
                    ];

                    Log::info("Field attributes for index {$index}:", $attributes);

                    if ($fieldId && in_array($fieldId, $existingFieldIds)) {
                        $formField = DynamicFormField::find($fieldId);
                        if ($formField) {
                            $formField->update($attributes);
                            $fieldsToKeepIds[] = $fieldId;
                        }
                    } else {
                        $newField = $form->fields()->create($attributes);
                        $fieldsToKeepIds[] = $newField->id;
                    }
                }
            }

            // Delete fields not in the new request
            $fieldsToDelete = array_diff($existingFieldIds, $fieldsToKeepIds);
            if (!empty($fieldsToDelete)) {
                DynamicFormField::whereIn('id', $fieldsToDelete)->delete();
                Log::info('Deleted fields with IDs:', $fieldsToDelete);
            }

            DB::commit();

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully!',
                    'redirect' => route('employees.dynamic-forms.index'),
                ], 200);
            }

            return redirect()->route('employees.dynamic-forms.index')->with('success', $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Form update validation error:', ['error' => $e->getMessage(), 'errors' => $e->errors()]);
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Form update general error:', ['error' => $e->getMessage()]);
            $errorMessage = 'An unexpected error occurred while updating the form.';
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => $e->getMessage()],
                ], 500);
            }
            return back()->withInput()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Show the form for sharing the dynamic form with assigned clients.
     */
    public function share(DynamicForm $form)
    {
        try {
            $employeeId = Auth::id();
            if ($form->employee_id !== $employeeId) {
                Log::warning('Employee attempted to access unauthorized form', [
                    'form_id' => $form->id,
                    'employee_id' => $employeeId,
                ]);
                abort(403, 'Unauthorized action.');
            }

            // Fetch clients assigned to the authenticated employee
            $clients = Client::whereHas('employees', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })->with('emails')->select('id', 'name')->get();

            if (!view()->exists('employee.dynamic-forms.share')) {
                throw new \Exception('View employee.dynamic-forms.share does not exist.');
            }

            return view('employees.dynamic-forms.share', compact('form', 'clients'));
        } catch (\Exception $e) {
            Log::error('Failed to load share view for dynamic form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => Auth::id(),
            ]);
            return redirect()->route('employees.dynamic-forms.index')->withErrors(['error' => 'Failed to load share page: ' . $e->getMessage()]);
        }
    }

    /**
     * Share the dynamic form with an assigned client.
     */
    public function send(Request $request, DynamicForm $form)
    {
        $employeeId = Auth::id();
        if ($form->employee_id !== $employeeId) {
            Log::warning('Employee attempted to share unauthorized form', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
            ]);
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => [
                'required',
                'exists:clients,id',
                function ($attribute, $value, $fail) use ($employeeId) {
                    $client = Client::find($value);
                    if (!$client || !$client->employees()->where('employee_id', $employeeId)->exists()) {
                        $fail('You can only share forms with your assigned clients.');
                    }
                },
            ],
            'message' => 'nullable|string',
        ]);

        try {
            $client = Client::findOrFail($request->user_id);

            // Save the form-client relationship
            DB::table('dynamic_form_client')->updateOrInsert(
                [
                    'client_id' => $client->id,
                    'dynamic_form_id' => $form->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $shareLink = route('admin.dynamic-forms.public-show', $form->id);

            // Example: Send email (implement Mail class if needed)
            // Mail::to($client->email)->send(new ShareFormMail($form, $shareLink, $request->message));

            return response()->json([
                'success' => true,
                'message' => 'Form shared successfully with ' . $client->name . '!',
                'redirect' => route('employees.dynamic-forms.index'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Failed to find client for sharing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Client not found.',
                'errors' => ['general' => 'Invalid client ID.'],
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to share dynamic form: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to share form.',
                'errors' => ['general' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Remove the specified dynamic form from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employeeId = Auth::id();
            $form = DynamicForm::where('employee_id', $employeeId)->findOrFail($id);

            DB::beginTransaction();

            // Delete associated fields
            $fieldCount = DynamicFormField::where('dynamic_form_id', $form->id)->delete();
            Log::info('Deleted associated fields for form', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
                'field_count' => $fieldCount,
            ]);

            // Delete associated responses
            $responseCount = DynamicFormResponse::where('dynamic_form_id', $form->id)->delete();
            Log::info('Deleted associated responses for form', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
                'response_count' => $responseCount,
            ]);

            // Delete associated client relationships
            $clientCount = DB::table('dynamic_form_client')->where('dynamic_form_id', $form->id)->delete();
            Log::info('Deleted associated client relationships for form', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
                'client_count' => $clientCount,
            ]);

            // Delete the form
            $form->delete();
            Log::info('Form deleted successfully', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
            ]);

            DB::commit();

            return redirect()->route('employees.dynamic-forms.index')->with('success', 'Form and associated data deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete dynamic form or its associated data: ' . $e->getMessage(), [
                'form_id' => $id,
                'employee_id' => $employeeId,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Failed to delete form and its associated data. Please try again.']);
        }
    }

    /**
     * Show the public-facing version of the dynamic form for submission.
     */
    public function showPublicForm($form)
    {
        try {
            $employeeId = Auth::id();
            $form = DynamicForm::where('employee_id', $employeeId)->with(['fields', 'clients'])->findOrFail($form);

            // Check if user is authenticated
            if (!Auth::check()) {
                Log::warning('Unauthenticated user attempted to access public form', [
                    'form_id' => $form->id,
                    'employee_id' => $employeeId ?? 'unauthenticated',
                ]);
                abort(403, 'You must be logged in to access this form.');
            }

            $user = Auth::user();
            $isEmployee = $user->isEmployee(); // Use User model's isEmployee() method
            $client = null;
            $hasSubmitted = false;

            if (!$isEmployee) {
                // For non-employees (e.g., clients), check for a client profile
                try {
                    $client = $user->client()->first();
                    if (!$client) {
                        Log::warning('No client record found for authenticated user', [
                            'form_id' => $form->id,
                            'user_id' => $user->id,
                        ]);
                        abort(403, 'No client profile found. Please contact an admin to set up your client profile.');
                    }

                    // Check if the form is assigned to the client
                    if (!$form->clients->contains($client->id)) {
                        Log::warning('Client not authorized to access form', [
                            'form_id' => $form->id,
                            'client_id' => $client->id,
                        ]);
                        abort(403, 'You are not authorized to access this form.');
                    }

                    // Check if the client has already submitted the form
                    $hasSubmitted = $form->responses()->where('client_id', $client->id)->exists();
                } catch (\BadMethodCallException $e) {
                    Log::error('Client relationship not defined on User model', [
                        'form_id' => $form->id,
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                    abort(500, 'Internal server error: Client relationship not configured.');
                }
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.dynamic-forms.public', compact('form', 'hasSubmitted'))->render(),
                    'formName' => $form->name,
                ]);
            }

            return view('admin.dynamic-forms.public', compact('form', 'hasSubmitted'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Form not found', [
                'form_id' => $form,
                'employee_id' => Auth::id() ?? 'unauthenticated',
            ]);
            abort(404, 'Form not found.');
        } catch (\Exception $e) {
            Log::error('Error displaying public form', [
                'form_id' => $form,
                'employee_id' => Auth::id() ?? 'unauthenticated',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'An unexpected error occurred.');
        }
    }

    /**
     * Handle the submission of the public dynamic form.
     */
    public function submitPublicForm(Request $request, DynamicForm $form)
    {
        try {
            $employeeId = Auth::id();
            if ($form->employee_id !== $employeeId) {
                Log::warning('Employee attempted to access unauthorized form submission', [
                    'form_id' => $form->id,
                    'employee_id' => $employeeId,
                ]);
                abort(403, 'Unauthorized action.');
            }

            if (!$form->is_active) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'This form is not active.'],
                ], 403);
            }

            $clientId = null;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isClient()) {
                    try {
                        $client = $user->client()->first();
                        if ($client) {
                            $clientId = $client->id;
                            $existingSubmission = DynamicFormResponse::where('dynamic_form_id', $form->id)
                                ->where('client_id', $clientId)
                                ->exists();
                            if ($existingSubmission) {
                                Log::warning('Duplicate submission attempt by client', [
                                    'form_id' => $form->id,
                                    'client_id' => $clientId,
                                    'user_id' => $user->id,
                                ]);
                                return response()->json([
                                    'success' => false,
                                    'errors' => ['general' => 'You have already submitted this form.'],
                                ], 422);
                            }
                        } else {
                            Log::warning('No client record found for user with client role', [
                                'user_id' => $user->id,
                            ]);
                            return response()->json([
                                'success' => false,
                                'errors' => ['general' => 'No client profile found for your account.'],
                            ], 403);
                        }
                    } catch (\BadMethodCallException $e) {
                        Log::error('Client relationship not defined on User model', [
                            'form_id' => $form->id,
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);
                        abort(500, 'Internal server error: Client relationship not configured.');
                    }
                }
            }

            DB::beginTransaction();

            $rules = [];
            $messages = [];
            foreach ($form->fields as $field) {
                $fieldRules = [];
                if ($field->is_required) {
                    $fieldRules[] = 'required';
                    $messages[$field->field_name . '.required'] = $field->field_label . ' is required.';
                }

                switch ($field->field_type) {
                    case 'email':
                        $fieldRules[] = 'email';
                        $messages[$field->field_name . '.email'] = 'Please enter a valid email address for ' . $field->field_label . '.';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        $messages[$field->field_name . '.numeric'] = 'Please enter a valid number for ' . $field->field_label . '.';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        $messages[$field->field_name . '.date'] = 'Please enter a valid date for ' . $field->field_label . '.';
                        break;
                    case 'file':
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'mimes:pdf,doc,docx,jpg,png|max:10240';
                        $messages[$field->field_name . '.mimes'] = 'The ' . $field->field_label . ' must be a file of type: pdf, doc, docx, jpg, png.';
                        $messages[$field->field_name . '.max'] = 'The ' . $field->field_label . ' may not be larger than 10MB.';
                        break;
                    case 'checkbox':
                        if ($field->is_required) {
                            $fieldRules[] = 'array|min:1';
                            $messages[$field->field_name . '.min'] = 'At least one option must be selected for ' . $field->field_label . '.';
                        }
                        break;
                }

                if (!empty($field->validation_rules) && is_array($field->validation_rules)) {
                    $fieldRules = array_merge($fieldRules, $field->validation_rules);
                }

                $rules[$field->field_name] = implode('|', array_unique($fieldRules));
            }

            $validatedData = $request->validate($rules, $messages);

            $responseData = [];
            foreach ($form->fields as $field) {
                $fieldName = $field->field_name;
                if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $path = $file->store('dynamic_form_uploads', 'public');
                    $responseData[$fieldName] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ];
                } elseif (isset($validatedData[$fieldName])) {
                    $value = $validatedData[$fieldName];
                    if (in_array($field->field_type, ['checkbox', 'radio']) && is_array($value)) {
                        $responseData[$fieldName] = implode(', ', $value);
                    } else {
                        $responseData[$fieldName] = $value;
                    }
                } else {
                    $responseData[$fieldName] = null;
                }
            }

            Log::debug('Form submission data', [
                'form_id' => $form->id,
                'response_data' => $responseData,
                'user_id' => Auth::check() ? Auth::id() : 'unauthenticated',
                'client_id' => $clientId,
                'role' => Auth::check() ? Auth::user()->role : 'none',
            ]);

            $response = DynamicFormResponse::create([
                'dynamic_form_id' => $form->id,
                'client_id' => $clientId,
                'response_data' => json_encode($responseData),
                'submitted_at' => now(),
            ]);

            Log::info('Form response saved', [
                'response_id' => $response->id,
                'form_id' => $form->id,
                'client_id' => $clientId,
            ]);

            DB::commit();

            return redirect()->route('clients.forms.index')->with('success', 'Form submitted successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed for public form submission', [
                'form_id' => $form->id,
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit public form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => Auth::id() ?? 'unauthenticated',
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'An unexpected error occurred: ' . $e->getMessage()],
            ], 500);
        }
    }
}
