<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicForm;
use App\Models\DynamicFormField;
use App\Models\DynamicFormResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Client; // Required for linking responses to clients
use App\Models\User;   // Required for finding clients by user email
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // For generating unique field names from labels
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DynamicFormController extends Controller
{
    /**
     * Display a listing of the dynamic forms.
     */
    public function index()
    {
        $forms = DynamicForm::with('fields')->paginate(10); // Paginate for larger lists
        return view('admin.dynamic-forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new dynamic form.
     */
    public function create()
    {
        return view('admin.dynamic-forms.create');
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
            'is_active' => 'sometimes|boolean', // 'sometimes' because checkbox sends value only if checked
            'fields' => 'required|array|min:1', // Ensure at least one field is provided
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,email,number,date,select,checkbox,radio,textarea,file',
            'fields.*.is_required' => 'sometimes|boolean',
            'fields.*.sort_order' => 'required|integer|min:0',
            'fields.*.field_options' => 'nullable|string', // Will be JSON encoded later
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
                'is_active' => $request->has('is_active'), // Checkbox value
                'settings' => $request->settings ?? null, // Ensure 'settings' is nullable or correctly handled
            ]);

            foreach ($request->fields as $index => $fieldData) {
                // Generate a field_name from the label if not provided, or normalize
                $fieldName = Str::slug($fieldData['field_label']); // Use Str::slug for consistent field names

                // Ensure uniqueness for field_name within this form (can add later as validation rule)
                // For now, simple slugging. For very strict uniqueness, you might append index or a hash.

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
                    'validation_rules' => !empty($fieldData['validation_rules']) ? json_encode($fieldData['validation_rules']) : null, // Assuming validation_rules comes as array/JSON string
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                ]);
            }

            DB::commit();
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Form created successfully!',
                    'redirect' => route('admin.dynamic-forms.index'), // Provide the redirect URL
                ]);
            } else {
                return redirect()->route('admin.dynamic-forms.index')->with('success', 'Form created successfully.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create dynamic form: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            $errorMessage = 'Failed to create form. Please try again.';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => $e->getMessage()] // Provide more specific error in dev/debug
                ], 500); // Return a 500 status code for server errors
            } else {
                return back()->withInput()->withErrors(['error' => $errorMessage . ' ' . $e->getMessage()]);
            }
        }
    }

    /**
     * Display the specified dynamic form.
     */
    public function show(string $id)
    {
        $form = DynamicForm::with(['fields', 'responses.client.user'])->findOrFail($id);
        return view('admin.dynamic-forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified dynamic form.
     */
    public function edit(string $id)
    {
        $form = DynamicForm::with('fields')->findOrFail($id);
        return view('admin.dynamic-forms.edit', compact('form'));
    }

    /**
     * Update the specified dynamic form in storage.
     */
    public function update(Request $request, string $id)
    {
        // Log incoming request data for debugging
        Log::info('Update form request data:', $request->all());

        $isAjax = $request->ajax() || $request->wantsJson();

        // Preprocess fields to ensure valid field_options before validation
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
                            Log::warning("Invalid or insufficient field_options for index {$index}, setting default", ['field_options' => $fieldData['field_options']]);
                            $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                        } else {
                            $fieldData['field_options'] = json_encode(array_filter($options, 'trim'));
                        }
                    } catch (\Exception $e) {
                        Log::warning("Error parsing field_options for index {$index}, setting default", ['error' => $e->getMessage(), 'field_options' => $fieldData['field_options']]);
                        $fieldData['field_options'] = json_encode(['Option 1', 'Option 2']);
                    }
                }
            }
        }
        unset($fieldData); // Unset reference
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

            $form = DynamicForm::findOrFail($id);

            // Update form details
            $form->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_active' => $request->boolean('is_active'),
                'is_draft' => $request->boolean('is_draft'),
            ]);

            $existingFieldIds = $form->fields->pluck('id')->toArray();
            $fieldsToKeepIds = [];

            // Track used field names to handle duplicates
            $usedFieldNames = [];

            if (!empty($validated['fields'])) {
                foreach ($validated['fields'] as $index => $fieldData) {
                    $fieldId = $fieldData['field_id'] ?? null;

                    // Generate base field_name
                    $baseFieldName = Str::slug($fieldData['field_label']);
                    $fieldName = $baseFieldName;

                    // Handle duplicates within the current request
                    if (in_array($fieldName, $usedFieldNames)) {
                        $suffix = 1;
                        while (in_array("{$fieldName}-{$suffix}", $usedFieldNames)) {
                            $suffix++;
                        }
                        $fieldName = "{$fieldName}-{$suffix}";
                    }
                    $usedFieldNames[] = $fieldName;

                    // Prepare common field attributes
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

                    // Log attributes for debugging
                    Log::info("Field attributes for index {$index}:", $attributes);

                    if ($fieldId && in_array($fieldId, $existingFieldIds)) {
                        // Update existing field
                        $formField = DynamicFormField::find($fieldId);
                        if ($formField) {
                            $formField->update($attributes);
                            $fieldsToKeepIds[] = $fieldId;
                        }
                    } else {
                        // Create new field
                        $newField = $form->fields()->create($attributes);
                        $fieldsToKeepIds[] = $newField->id;
                    }
                }
            }

            // Delete fields that were not in the new request
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
                    'redirect' => route('admin.dynamic-forms.index'),
                ], 200);
            }

            return redirect()->route('admin.dynamic-forms.index')->with('success', $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Form update validation error:', ['error' => $e->getMessage(), 'errors' => $e->errors(), 'request' => $request->all()]);

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your inputs.',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Form update general error:', ['error' => $e->getMessage(), 'exception' => $e, 'request' => $request->all()]);

            $errorMessage = 'An unexpected error occurred while updating the form.';
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => $e->getMessage()]
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Show the form for sharing the dynamic form with clients.
     */
    public function share(DynamicForm $form)
    {
        try {
            // Eager-load clients with their emails
            $clients = Client::with('emails')->select('id', 'name')->get();

            // Verify view exists
            if (!view()->exists('admin.dynamic-forms.share')) {
                throw new \Exception('View dynamic-forms.share does not exist.');
            }

            return view('admin.dynamic-forms.share', compact('form', 'clients'));
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in share method: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.dynamic-forms.index')->withErrors(['error' => 'Database error occurred. Please contact support.']);
        } catch (\Exception $e) {
            Log::error('Failed to load share view for dynamic form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.dynamic-forms.index')->withErrors(['error' => 'Failed to load share page: ' . $e->getMessage()]);
        }
    }
    /**
     * Share the dynamic form with a client and save to dynamic_form_client table.
     */
    public function send(Request $request, DynamicForm $form)
    {
        $request->validate([
            'user_id' => 'required|exists:clients,id',
            'message' => 'nullable|string',
        ]);

        try {
            $client = Client::findOrFail($request->user_id);

            // Save the form-client relationship in dynamic_form_client table
            DB::table('dynamic_form_client')->updateOrInsert(   //Create or update the record in the table, preventing duplicate entries for the same client 'client_id' and dynamic_form_id.
                [
                    'client_id' => $client->id,
                    'dynamic_form_id' => $form->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Prepare the share link (public form URL)
            $shareLink = route('admin.dynamic-forms.public-show', $form->id);

            // Example: Send an email (implement Mail class if using)
            // Mail::to($client->email)->send(new ShareFormMail($form, $shareLink, $request->message));

            return response()->json([
                'success' => true,
                'message' => 'Form shared successfully with ' . $client->name . '!',
                'redirect' => route('admin.dynamic-forms.index'),
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
            $form = DynamicForm::findOrFail($id);
            $form->delete(); // This will cascade delete fields and responses due to onDelete('cascade')

            return redirect()->route('admin.dynamic-forms.index')->with('success', 'Form deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete dynamic form: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete form. Please try again.']);
        }
    }

    /**
     * Show the public-facing version of the dynamic form for submission.
     */
    public function showPublicForm($form)
    {
        try {
            // Load the form with its fields and clients
            $form = DynamicForm::with(['fields', 'clients'])->findOrFail($form);

            // Check if user is authenticated
            if (!Auth::check()) {
                Log::warning('Unauthenticated user attempted to access public form', [
                    'form_id' => $form->id,
                ]);
                abort(403, 'You must be logged in to access this form.');
            }

            // Get the authenticated user
            $user = Auth::user();

            // Check if the user is an admin
            $isAdmin = $user->role === 'admin'; // Adjust this based on your admin identification logic

            // Initialize variables
            $client = null;
            $hasSubmitted = false;

            if (!$isAdmin) {
                // For non-admins, check for a client profile
                $client = $user->client()->first();
                if (!$client) {
                    Log::warning('No client record found for authenticated user', [
                        'form_id' => $form->id,
                        'user_id' => $user->id,
                    ]);
                    return redirect()->route('admin.clients.create')->with('error', 'Please create a client profile to access this form.');
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
            }

            // Prepare view data
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
                'user_id' => Auth::id() ?? 'unauthenticated',
            ]);
            abort(404, 'Form not found.');
        } catch (\Exception $e) {
            Log::error('Error displaying public form', [
                'form_id' => $form,
                'user_id' => Auth::id() ?? 'unauthenticated',
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
            // Verify form is active
            if (!$form->is_active) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'This form is not active.'],
                ], 403);
            }

            // Determine client_id and check for duplicates
            $clientId = null;
            if (Auth::check() && Auth::user()) {
                $user = Auth::user();
                if ($user->isClient()) {
                    $client = $user->client()->first();
                    if ($client) {
                        $clientId = $client->id;
                        // Check for existing submission
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
                } else {
                    Log::info('Non-client user attempted form submission', [
                        'user_id' => $user->id,
                        'role' => $user->role,
                    ]);
                    // Non-clients submit with client_id = null
                }
            }

            // Start transaction
            DB::beginTransaction();

            // Build validation rules
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

            // Validate request
            $validatedData = $request->validate($rules, $messages);

            // Process response data
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

            // Log form data for debugging
            Log::debug('Form submission data', [
                'form_id' => $form->id,
                'response_data' => $responseData,
                'user_id' => Auth::check() ? Auth::id() : 'unauthenticated',
                'client_id' => $clientId,
                'role' => Auth::check() ? Auth::user()->role : 'none',
            ]);

            // Save the response
            $response = DynamicFormResponse::create([
                'dynamic_form_id' => $form->id,
                'client_id' => $clientId,
                'response_data' => json_encode($responseData),
                'submitted_at' => now(),
            ]);

            // Log successful save
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
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit public form: ' . $e->getMessage(), [
                'form_id' => $form->id,
                'user_id' => Auth::check() ? Auth::id() : 'unauthenticated',
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'An unexpected error occurred: ' . $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Display a listing of all dynamic form responses.
     */
    public function responses()
    {
        // Log::info('Responses method accessed', ['user' => Auth::user() ? Auth::user()->toArray() : 'Guest']);
        try {
            $responses = DynamicFormResponse::with(['dynamicForm', 'client'])->paginate(10);
            $forms = DynamicForm::select('id', 'name')->get();
            return view('admin.dynamic-forms.responses', compact('responses', 'forms'));
        } catch (\Exception $e) {
            // Log::error('Error in responses method', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

        }
    }
}
