<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicForm;
use App\Models\DynamicFormField;
use App\Models\DynamicFormResponse;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DynamicFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = DynamicForm::with('fields')->paginate(15);
        return view('dynamic-forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dynamic-forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.field_name' => 'required|string',
            'fields.*.field_label' => 'required|string',
            'fields.*.field_type' => 'required|in:text,email,number,date,select,checkbox,radio,textarea,file',
        ]);

        try {
            DB::beginTransaction();

            $form = DynamicForm::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'settings' => $request->settings,
            ]);

            foreach ($request->fields as $index => $fieldData) {
                DynamicFormField::create([
                    'dynamic_form_id' => $form->id,
                    'field_name' => $fieldData['field_name'],
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_options' => $fieldData['field_options'] ?? null,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $index + 1,
                    'validation_rules' => $fieldData['validation_rules'] ?? null,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('dynamic-forms.index')->with('success', 'Form created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create form: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = DynamicForm::with(['fields', 'responses.client.user'])->findOrFail($id);
        return view('dynamic-forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $form = DynamicForm::with('fields')->findOrFail($id);
        return view('dynamic-forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $form = DynamicForm::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $form->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'settings' => $request->settings,
        ]);

        return redirect()->route('dynamic-forms.show', $form->id)->with('success', 'Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = DynamicForm::findOrFail($id);
        $form->delete();

        return redirect()->route('dynamic-forms.index')->with('success', 'Form deleted successfully.');
    }

    /**
     * Show public form for clients to fill
     */
    public function showPublicForm(string $id)
    {
        $form = DynamicForm::with('fields')->where('is_active', true)->findOrFail($id);
        return view('dynamic-forms.public', compact('form'));
    }

    /**
     * Submit public form response
     */
    public function submitPublicForm(Request $request, string $id)
    {
        $form = DynamicForm::with('fields')->findOrFail($id);

        // Validate based on form fields
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldRules = [];
            if ($field->is_required) {
                $fieldRules[] = 'required';
            }

            if ($field->field_type === 'email') {
                $fieldRules[] = 'email';
            } elseif ($field->field_type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($field->field_type === 'date') {
                $fieldRules[] = 'date';
            }

            if (!empty($fieldRules)) {
                $rules[$field->field_name] = implode('|', $fieldRules);
            }
        }

        $request->validate($rules);

        // Find or create client based on email
        $email = $request->input('email'); // Assuming email field exists
        $client = null;

        if ($email) {
            $user = \App\Models\User::where('email', $email)->first();
            if ($user && $user->client) {
                $client = $user->client;
            }
        }

        if ($client) {
            DynamicFormResponse::create([
                'dynamic_form_id' => $form->id,
                'client_id' => $client->id,
                'response_data' => $request->except(['_token', '_method']),
                'submitted_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Form submitted successfully.');
    }
}
