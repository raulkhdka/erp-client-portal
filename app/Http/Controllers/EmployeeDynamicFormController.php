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
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;

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

        // Log the entire request data
        Log::info('Store form request data:', $request->all());

        $rules = [
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
        ];

        $messages = [
            'fields.required' => 'At least one field is required for the form.',
            'fields.min' => 'You must provide at least one field for the form.',
            'fields.*.field_label.required' => 'Field label is required for all fields.',
            'fields.*.field_type.required' => 'Field type is required for all fields.',
            'fields.*.field_type.in' => 'Invalid field type selected.',
            'fields.*.sort_order.required' => 'Field order is required for all fields.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Custom validation for field_options
        $fields = $request->input('fields', []);
        foreach ($fields as $index => $field) {
            if (in_array($field['field_type'] ?? null, ['select', 'radio', 'checkbox'])) {
                $fieldOptions = $field['field_options'] ?? '';
                Log::info("Validating field_options for field {$index}:", ['field_options' => $fieldOptions]);
                if (empty($fieldOptions)) {
                    $validator->errors()->add("fields.$index.field_options", "The field_options field is required for select, radio, or checkbox fields.");
                } else {
                    $options = array_filter(array_map('trim', explode("\n", str_replace(["\r", "\n\n"], ["", "\n"], $fieldOptions))), 'strlen');
                    Log::info("Parsed options for field {$index}:", ['options' => $options, 'count' => count($options)]);
                    if (count($options) < 2) {
                        $validator->errors()->add("fields.$index.field_options", "The field_options field must contain at least 2 valid options.");
                    }
                }
            }
        }

        if ($validator->fails()) {
            Log::error('Form store validation failed:', ['errors' => $validator->errors()->toArray()]);
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            $form = DynamicForm::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'is_draft' => $request->boolean('is_draft'),
                'settings' => $request->settings ?? null,
                'employee_id' => Auth::id(),
            ]);

            foreach ($request->fields as $index => $fieldData) {
                $fieldName = Str::slug($fieldData['field_label']);
                $options = !empty($fieldData['field_options'])
                    ? json_encode(array_map('trim', explode("\n", str_replace(["\r", "\n\n"], ["", "\n"], $fieldData['field_options']))))
                    : null;
                Log::info("Creating field {$index} for form {$form->id}:", [
                    'field_name' => $fieldName,
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => $options,
                ]);

                DynamicFormField::create([
                    'dynamic_form_id' => $form->id,
                    'field_name' => $fieldName,
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => $options,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $fieldData['sort_order'],
                    'validation_rules' => !empty($fieldData['validation_rules']) ? json_encode($fieldData['validation_rules']) : null,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                ]);
            }

            DB::commit();
            Log::info('Form created successfully:', ['form_id' => $form->id, 'employee_id' => Auth::id()]);
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
            Log::error('Failed to create dynamic form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
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

        $fields = $request->input('fields', []);
        $completeFields = array_filter($fields, function ($field) {
            return !empty($field['field_label']) && !empty($field['field_type']) && isset($field['sort_order']);
        });
        $request->merge(['fields' => array_values($completeFields)]);

        $fields = $request->input('fields', []);
        foreach ($fields as $index => &$fieldData) {
            if (in_array($fieldData['field_type'] ?? '', ['select', 'radio', 'checkbox'])) {
                $options = $fieldData['field_options'] ?? '';
                Log::info("Processing field_options for index {$index}:", ['raw_options' => $options]);
                if (!$options) {
                    Log::warning("Missing field_options for index {$index}, setting default");
                    $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                } else {
                    $cleanedOptions = str_replace(["\r", "\n\n"], ["", "\n"], $options);
                    $optionsArray = array_filter(array_map('trim', explode("\n", $cleanedOptions)), 'strlen');
                    Log::info("Parsed options for index {$index}:", ['options' => $optionsArray]);
                    if (count($optionsArray) < 2) {
                        Log::warning("Insufficient field_options for index {$index}, setting default");
                        $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                    } else {
                        $fieldData['field_options'] = json_encode($optionsArray);
                    }
                }
            }
        }
        unset($fieldData);
        $request->merge(['fields' => $fields]);

        $rules = [
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
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.help_text' => 'nullable|string',
        ];

        $messages = [
            'fields.required' => 'At least one field is required for the form.',
            'fields.min' => 'You must provide at least one field for the form.',
            'fields.*.field_label.required' => 'Field label is required for all fields.',
            'fields.*.field_type.required' => 'Field type is required for all fields.',
            'fields.*.field_type.in' => 'Invalid field type selected.',
            'fields.*.sort_order.required' => 'Field order is required for all fields.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        foreach ($fields as $index => $field) {
            if (in_array($field['field_type'] ?? null, ['select', 'radio', 'checkbox'])) {
                $fieldOptions = $field['field_options'] ?? '';
                Log::info("Validating field_options for index $index: ", ['raw_options' => $fieldOptions]);
                if (empty($fieldOptions)) {
                    $validator->errors()->add("fields.$index.field_options", "The field_options field is required for select, radio, or checkbox fields.");
                } else {
                    try {
                        $options = json_decode($fieldOptions, true);
                        if (!is_array($options)) {
                            $options = array_filter(array_map('trim', explode("\n", str_replace(["\r", "\n\n"], ["", "\n"], $fieldOptions))), 'strlen');
                        }
                        Log::info("Validated options for index $index: ", ['options' => $options, 'count' => count($options)]);
                        if (count($options) < 2) {
                            $validator->errors()->add("fields.$index.field_options", "The field_options field must contain at least 2 valid options.");
                        }
                    } catch (\Exception $e) {
                        $validator->errors()->add("fields.$index.field_options", "The field_options field has an invalid format.");
                    }
                }
            }
        }

        if ($validator->fails()) {
            Log::error('Form update validation error:', ['errors' => $validator->errors()->toArray()]);
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            $employeeId = Auth::id();
            $form = DynamicForm::where('employee_id', $employeeId)->findOrFail($id);

            $form->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_draft' => $request->boolean('is_draft'),
                'employee_id' => $employeeId,
            ]);

            $existingFieldIds = $form->fields->pluck('id')->toArray();
            $fieldsToKeepIds = [];
            $usedFieldNames = [];

            foreach ($request->fields as $index => $fieldData) {
                $fieldName = Str::slug($fieldData['field_label']);
                if (in_array($fieldName, $usedFieldNames)) {
                    $fieldName .= '_' . $index;
                }
                $usedFieldNames[] = $fieldName;

                $options = !empty($fieldData['field_options'])
                    ? (is_array($fieldData['field_options'])
                        ? json_encode($fieldData['field_options'])
                        : $fieldData['field_options'])
                    : null;

                $fieldAttributes = [
                    'dynamic_form_id' => $form->id,
                    'field_name' => $fieldName,
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => $options,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $fieldData['sort_order'],
                    'validation_rules' => !empty($fieldData['validation_rules']) ? json_encode($fieldData['validation_rules']) : null,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                ];

                Log::info("Updating/creating field {$index} for form {$form->id}:", [
                    'field_name' => $fieldName,
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => $options,
                ]);

                if (!empty($fieldData['field_id']) && in_array($fieldData['field_id'], $existingFieldIds)) {
                    $field = DynamicFormField::find($fieldData['field_id']);
                    $field->update($fieldAttributes);
                    $fieldsToKeepIds[] = $fieldData['field_id'];
                } else {
                    $field = DynamicFormField::create($fieldAttributes);
                    $fieldsToKeepIds[] = $field->id;
                }
            }

            // Delete fields that are no longer in the request
            DynamicFormField::where('dynamic_form_id', $form->id)
                ->whereNotIn('id', $fieldsToKeepIds)
                ->delete();

            DB::commit();
            Log::info('Form updated successfully:', ['form_id' => $form->id, 'employee_id' => $employeeId]);
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully!',
                    'redirect' => route('employees.dynamic-forms.index'),
                ]);
            }
            return redirect()->route('employees.dynamic-forms.index')->with('success', $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update dynamic form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            $errorMessage = 'Failed to update form. Please try again.';
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

            $employee = Employee::where('user_id', $employeeId)->first();
            if (!$employee) {
                Log::error('Employee record not found for authenticated user', [
                    'form_id' => $form->id,
                    'user_id' => $employeeId,
                ]);
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'Employee profile not found.');
            }

            $clients = $employee->accessibleClients()->with('emails')->select('clients.id', 'clients.name')->get();

            Log::info('Clients fetched for sharing', [
                'form_id' => $form->id,
                'employee_id' => $employee->id,
                'client_count' => $clients->count(),
                'clients' => $clients->pluck('id', 'name')->toArray(),
            ]);

            if ($clients->isEmpty()) {
                Log::warning('No clients assigned to employee for sharing', [
                    'form_id' => $form->id,
                    'employee_id' => $employee->id,
                ]);
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'No clients are assigned to you. Please contact an admin to assign clients.');
            }

            if (!view()->exists('employees.dynamic-forms.share')) {
                throw new \Exception('View employees.dynamic-forms.share does not exist.');
            }

            return view('employees.dynamic-forms.share', compact('form', 'clients'));
        } catch (\Exception $e) {
            Log::error('Failed to load share view for dynamic form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('employees.dynamic-forms.index')
                ->withErrors(['error' => 'Failed to load share page: ' . $e->getMessage()]);
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
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
                'errors' => ['general' => 'You do not have permission to share this form.'],
            ], 403);
        }

        if ($form->is_draft) {
            Log::warning('Attempted to share draft form', [
                'form_id' => $form->id,
                'employee_id' => $employeeId,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Cannot share draft form.',
                'errors' => ['general' => 'This form is a draft and cannot be shared until published.'],
            ], 422);
        }

        $employee = Employee::where('user_id', $employeeId)->first();
        if (!$employee) {
            Log::error('Employee record not found for authenticated user', [
                'form_id' => $form->id,
                'user_id' => $employeeId,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Employee profile not found.',
                'errors' => ['general' => 'Employee profile not found.'],
            ], 404);
        }

        Log::info('Share form request data:', $request->all());

        $request->validate([
            'user_id' => [
                'required',
                'exists:clients,id',
                function ($attribute, $value, $fail) use ($employee) {
                    if (!$employee->accessibleClients()->where('clients.id', $value)->exists()) {
                        Log::warning('Client not assigned to employee', [
                            'client_id' => $value,
                            'employee_id' => $employee->id,
                        ]);
                        $fail('You can only share forms with your assigned clients.');
                    }
                },
            ],
            'message' => 'nullable|string',
        ]);

        try {
            $client = Client::findOrFail($request->user_id);

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

            $shareLink = route('employees.dynamic-forms.preview', $form->id);

            // Example: Send email (uncomment and configure if needed)
            // Mail::to($client->email)->send(new ShareFormMail($form, $shareLink, $request->message));

            Log::info('Form shared successfully', [
                'form_id' => $form->id,
                'client_id' => $client->id,
                'employee_id' => $employee->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Form shared successfully with ' . $client->name . '!',
                'redirect' => route('employees.dynamic-forms.index'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Client not found for sharing', [
                'form_id' => $form->id,
                'client_id' => $request->user_id,
                'employee_id' => $employee->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Client not found.',
                'errors' => ['general' => 'Invalid client ID.'],
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to share dynamic form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => $employee->id,
                'trace' => $e->getTraceAsString(),
            ]);
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
    public function preview($form)
    {
        try {
            $form = DynamicForm::with('fields')->findOrFail($form);

            // Check if the form is a draft
            if ($form->is_draft) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => 'Form is saved as draft. Please save it to the database.'],
                        'redirect' => route('employees.dynamic-forms.index'),
                    ], 403);
                }
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'Form is saved as draft. Please save it to the database.');
            }

            // Check if the form belongs to the authenticated employee
            $employeeId = Auth::id();
            if ($form->employee_id !== $employeeId) {
                Log::warning('Employee attempted to preview unauthorized form', [
                    'form_id' => $form->id,
                    'employee_id' => $employeeId,
                ]);
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => 'You are not authorized to preview this form.'],
                    ], 403);
                }
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'You are not authorized to preview this form.');
            }

            // Since employee_id is not in DynamicFormResponse, skip hasSubmitted check or adjust based on your logic
            $hasSubmitted = false; // Default for view compatibility

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('employees.dynamic-forms.preview', compact('form', 'hasSubmitted'))->render(),
                    'formName' => $form->name,
                ]);
            }

            return view('employees.dynamic-forms.preview', compact('form', 'hasSubmitted'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Form not found in preview', ['form_id' => $form]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Form not found.'],
                ], 404);
            }
            return redirect()->route('employees.dynamic-forms.index')
                ->with('error', 'Form not found.');
        } catch (\Exception $e) {
            Log::error('Error displaying form preview', [
                'form_id' => $form,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'An unexpected error occurred: ' . $e->getMessage()],
                ], 500);
            }
            return redirect()->route('employees.dynamic-forms.index')
                ->with('error', 'An unexpected error occurred.');
        }
    }
    /**
     * Handle the submission of the public dynamic form.
     */
    public function submitPreviewForm(Request $request, DynamicForm $form)
    {
        try {
            $employeeId = Auth::id();
            if ($form->employee_id !== $employeeId) {
                Log::warning('Employee attempted to submit unauthorized form', [
                    'form_id' => $form->id,
                    'employee_id' => $employeeId,
                ]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => 'You are not authorized to submit this form.'],
                    ], 403);
                }
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'You are not authorized to submit this form.');
            }

            if ($form->is_draft) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => 'Form is saved as draft. Please save it to the database.'],
                    ], 403);
                }
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'Form is saved as draft. Please save it to the database.');
            }

            if (!$form->is_active) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => 'This form is not active.'],
                    ], 403);
                }
                return redirect()->route('employees.dynamic-forms.index')
                    ->with('error', 'This form is not active.');
            }

            // Assuming DynamicFormResponse does not track employee submissions, we skip duplicate check
            // If needed, adjust to check based on your schema (e.g., client_id or another identifier)

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
                'employee_id' => $employeeId,
                'role' => Auth::user()->role,
            ]);

            $response = DynamicFormResponse::create([
                'dynamic_form_id' => $form->id,
                'employee_id' => $employeeId, // Adjust if employee_id is not in schema
                'response_data' => json_encode($responseData),
                'submitted_at' => now(),
            ]);

            Log::info('Form response saved', [
                'response_id' => $response->id,
                'form_id' => $form->id,
                'employee_id' => $employeeId,
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Form submitted successfully',
                    'redirect' => route('employees.dynamic-forms.index'),
                ]);
            }

            return redirect()->route('employees.dynamic-forms.index')
                ->with('success', 'Form submitted successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed for form submission', [
                'form_id' => $form->id,
                'errors' => $e->errors(),
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'employee_id' => $employeeId ?? 'unauthenticated',
                'trace' => $e->getTraceAsString(),
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'An unexpected error occurred: ' . $e->getMessage()],
                ], 500);
            }
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
