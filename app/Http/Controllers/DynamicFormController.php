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
            return redirect()->route('dynamic-forms.index')->with('success', 'Form created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error for debugging
            Log::error('Failed to create dynamic form: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create form. Please try again. ' . $e->getMessage()]);
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
        $form = DynamicForm::with('fields')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'fields' => 'required|array|min:1',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,email,number,date,select,checkbox,radio,textarea,file',
            'fields.*.is_required' => 'sometimes|boolean',
            'fields.*.sort_order' => 'required|integer|min:0',
            'fields.*.field_options' => 'nullable|string',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.help_text' => 'nullable|string',
            // Note: field_name for existing fields could be hidden input or derived.
            // For simplicity, we'll re-slug from label if not provided or provided as empty.
            'fields.*.field_id' => 'nullable|integer|exists:dynamic_form_fields,id', // For identifying existing fields
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

            $form->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'settings' => $request->settings ?? null,
            ]);

            $existingFieldIds = $form->fields->pluck('id')->toArray();
            $submittedFieldIds = [];

            foreach ($request->fields as $fieldData) {
                $fieldName = Str::slug($fieldData['field_label']); // Always re-slug for consistency

                $fieldAttrs = [
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
                ];

                if (isset($fieldData['field_id']) && $fieldData['field_id']) {
                    // Update existing field
                    $field = DynamicFormField::find($fieldData['field_id']);
                    if ($field && $field->dynamic_form_id === $form->id) {
                        $field->update($fieldAttrs);
                        $submittedFieldIds[] = $field->id;
                    } else {
                        // If field_id is invalid or belongs to another form, create new (shouldn't happen with 'exists' validation)
                        $newField = DynamicFormField::create($fieldAttrs);
                        $submittedFieldIds[] = $newField->id;
                    }
                } else {
                    // Create new field
                    $newField = DynamicFormField::create($fieldAttrs);
                    $submittedFieldIds[] = $newField->id;
                }
            }

            // Delete fields that were not in the submitted request
            $fieldsToDelete = array_diff($existingFieldIds, $submittedFieldIds);
            DynamicFormField::whereIn('id', $fieldsToDelete)->delete();


            DB::commit();
            return redirect()->route('dynamic-forms.show', $form->id)->with('success', 'Form updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update dynamic form: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update form. Please try again. ' . $e->getMessage()]);
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
        $form = DynamicForm::with('fields')->where('is_active', true)->findOrFail($id);

        $rules = [];
        $messages = [];
        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
                $messages[$field->field_name . '.required'] = $field->field_label . ' is required.';
            }

            // Add specific validation rules based on field type
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
                    // Add more cases for specific validation needs (e.g., min/max for text/number)
            }

            // Merge dynamic validation rules from the field's validation_rules JSON column
            if ($field->validation_rules && is_array($field->validation_rules)) {
                $fieldRules = array_merge($fieldRules, $field->validation_rules);
            }

            $rules[$field->field_name] = implode('|', array_unique($fieldRules));
        }

        $validatedData = $request->validate($rules, $messages);

        $responseData = [];
        foreach ($form->fields as $field) {
            $fieldName = $field->field_name;

            if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                // Store file and save its path
                $path = $request->file($fieldName)->store('dynamic_form_uploads');
                $responseData[$fieldName] = $path;
            } else if (isset($validatedData[$fieldName])) {
                // Get validated data, including potential checkboxes/radios as arrays if multiple values
                $value = $validatedData[$fieldName];
                // For checkbox/radio, if options are stored as array, join them for display if it's multiple
                if (($field->field_type === 'checkbox' || $field->field_type === 'radio') && is_array($value)) {
                    $responseData[$fieldName] = implode(', ', $value);
                } else {
                    $responseData[$fieldName] = $value;
                }
            } else {
                // Handle cases where a non-required field might not be present in the request
                $responseData[$fieldName] = null;
            }
        }

        // Try to associate client (if an email field is present and matches a user)
        $client = null;
        $emailField = $form->fields->firstWhere('field_type', 'email'); // Find the first field of type 'email'

        if ($emailField && !empty($validatedData[$emailField->field_name])) {
            $email = $validatedData[$emailField->field_name];
            $user = User::where('email', $email)->first();
            if ($user && $user->client) { // Assuming User has a 'client' relationship
                $client = $user->client;
            }
        }

        DynamicFormResponse::create([
            'dynamic_form_id' => $form->id,
            'client_id' => $client ? $client->id : null, // client_id is nullable if not found
            'response_data' => $responseData,
            'submitted_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Your form has been submitted successfully.');
    }
}
