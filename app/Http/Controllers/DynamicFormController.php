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

class DynamicFormController extends Controller
{
    /**
     * Display a listing of the dynamic forms.
     */
    public function index()
    {
        $forms = DynamicForm::with('fields')->paginate(10); // Paginate for larger lists
        return view('dynamic-forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new dynamic form.
     */
    public function create()
    {
        return view('dynamic-forms.create');
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
                    'redirect' => route('dynamic-forms.index'), // Provide the redirect URL
                ]);
            } else {
                return redirect()->route('dynamic-forms.index')->with('success', 'Form created successfully.');
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
        return view('dynamic-forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified dynamic form.
     */
    public function edit(string $id)
    {
        $form = DynamicForm::with('fields')->findOrFail($id);
        return view('dynamic-forms.edit', compact('form'));
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
                    'redirect' => route('dynamic-forms.index'),
                ], 200);
            }

            return redirect()->route('dynamic-forms.index')->with('success', $request->boolean('is_draft') ? 'Form saved as draft successfully!' : 'Form updated successfully.');
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
    public function share(string $id)
    {
        try {
            $form = DynamicForm::with('fields')->findOrFail($id);
            // Fetch clients (assuming clients are users with a specific role or a related model)
            $clients = Client::all(); // Adjust this query based on your client model and relationship
            return view('dynamic-forms.share', compact('form', 'clients'));
        } catch (\Exception $e) {
            Log::error('Failed to load share view for dynamic form: ' . $e->getMessage());
            return redirect()->route('dynamic-forms.index')->withErrors(['error' => 'Failed to load share page. Please try again.']);
        }
    }

    public function send(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:clients,id',
            'message' => 'nullable|string',
        ]);

        try {
            $form = DynamicForm::findOrFail($id);
            $client = Client::findOrFail($request->user_id);

            // Prepare the share link (e.g., public form URL)
            $shareLink = route('dynamic-forms.public-show', $form->id);

            // Example: Send an email (implement Mail class if using)
            // Mail::to($client->email)->send(new ShareFormMail($form, $shareLink, $request->message));

            // For now, return a success response (replace with actual logic)
            return response()->json([
                'success' => true,
                'message' => 'Form shared successfully with ' . $client->name . '!',
                'redirect' => route('dynamic-forms.index'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Failed to find form or client for sharing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Form or client not found.',
                'errors' => ['general' => 'Invalid form or client ID.'],
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to send dynamic form: ' . $e->getMessage());
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

            return redirect()->route('dynamic-forms.index')->with('success', 'Form deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete dynamic form: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete form. Please try again.']);
        }
    }

    /**
     * Show the public-facing version of the dynamic form for submission.
     */
    public function showPublicForm(string $id)
    {
        $form = DynamicForm::with('fields')->where('is_active', true)->findOrFail($id);
        return view('dynamic-forms.public', compact('form'));
    }

    /**
     * Handle the submission of the public dynamic form.
     */
    public function submitPublicForm(Request $request, string $id)
    {
        try {
            $form = DynamicForm::with('fields')->where('is_active', true)->findOrFail($id);

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
                        // Optional: Add more specific file rules like mimes, max size if needed
                        // $fieldRules[] = 'mimes:pdf,doc,docx,jpg,png|max:10240'; // Example: max 10MB
                        $messages[$field->field_name . '.file'] = 'Please upload a valid file for ' . $field->field_label . '.';
                        break;
                }

                if ($field->validation_rules && is_array($field->validation_rules)) {
                    $fieldRules = array_merge($fieldRules, $field->validation_rules);
                }

                $rules[$field->field_name] = implode('|', array_unique($fieldRules));
            }

            // Using DB::transaction here to ensure atomicity, especially with file uploads
            // If the file upload fails AFTER the DB save, the DB save will still be there.
            // So, for file uploads, it's better to store the file first, then save to DB,
            // and if DB save fails, delete the file. Or, defer file cleanup.
            // For now, let's just wrap the DB part in a transaction.

            DB::beginTransaction();

            $validatedData = $request->validate($rules, $messages);

            $responseData = [];
            foreach ($form->fields as $field) {
                $fieldName = $field->field_name;

                if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                    // Store file and save its path
                    // Ensure 'dynamic_form_uploads' directory exists and is writable in storage/app/
                    $path = $request->file($fieldName)->store('dynamic_form_uploads');
                    $responseData[$fieldName] = $path;
                } else if (isset($validatedData[$fieldName])) {
                    $value = $validatedData[$fieldName];
                    if (($field->field_type === 'checkbox' || $field->field_type === 'radio') && is_array($value)) {
                        $responseData[$fieldName] = implode(', ', $value);
                    } else {
                        $responseData[$fieldName] = $value;
                    }
                } else {
                    $responseData[$fieldName] = null;
                }
            }

            $client = null;
            $emailField = $form->fields->firstWhere('field_type', 'email');

            if ($emailField && !empty($validatedData[$emailField->field_name])) {
                $email = $validatedData[$emailField->field_name];
                $user = User::where('email', $email)->first();
                if ($user && $user->client) {
                    $client = $user->client;
                }
            }

            DynamicFormResponse::create([
                'dynamic_form_id' => $form->id,
                'client_id' => $client ? $client->id : null,
                'response_data' => $responseData,
                'submitted_at' => now(),
            ]);

            DB::commit(); // Commit transaction only if everything above succeeds

            return redirect()->back()->with('success', 'Your form has been submitted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // This catches validation errors specifically
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollback(); // Rollback any DB changes if an error occurred after transaction started
            Log::error('Failed to submit public dynamic form: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->withErrors(['submission_error' => 'An unexpected error occurred while submitting your form. Please try again. If the problem persists, contact support.']);
        }
    }
}
