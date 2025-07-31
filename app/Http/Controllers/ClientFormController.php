<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Client; // Make sure your Client model exists
use App\Models\DynamicFormResponse; // Make sure your DynamicFormResponse model exists
use App\Models\DynamicForm; // Make sure your DynamicForm model exists

class ClientFormController extends Controller
{
    /**
     * Display a listing of forms/form responses for the authenticated client.
     */
    public function index()
    {
        Log::info('ClientFormController@index called - START');
        Log::info('ClientFormController@index called', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role ?? 'no-user',
            'is_authenticated' => Auth::check()
        ]);

        $user = Auth::user();
        Log::info('User data', ['user' => $user]);

        $client = $user->client;
        Log::info('Client data', ['client' => $client]);

        if (!$client) {
            Log::warning('Client profile not found for user', ['user_id' => Auth::id()]);
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch forms shared with this client (assuming a pivot table or a shared_with_clients relationship)
        // If you use a pivot table 'dynamic_form_client' or a relation 'sharedForms' on Client:
        $forms = DynamicForm::whereHas('sharedWithClients', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })->where('is_active', true)->with('fields')->paginate(10);

        // If you do not have such a relation, fallback to all active forms (not recommended for production)
        // $forms = DynamicForm::where('is_active', true)->with('fields')->paginate(10);

        return view('clients.forms.index', compact('client', 'forms'));
    }

    /**
     * Display the specified form response.
     */
    public function show(DynamicFormResponse $dynamicFormResponse)
    {
        $user = Auth::user();
        $client = $user->client;

        // Ensure the client has access to this form response
        if (!$client || $dynamicFormResponse->client_id !== $client->id) {
            abort(403, 'Unauthorized access to form response.');
        }

        return view('clients.forms.show', compact('client', 'dynamicFormResponse'));
    }

    public function create(DynamicForm $dynamicForm)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client || !$dynamicForm->is_active || !$dynamicForm->responses()->where('client_id', $client->id)->exists()) {
            abort(403, 'Unauthorized access to this form.');
        }

        return view('clients.forms.create', compact('client', 'dynamicForm'));
    }

    /**
     * Store a new form submission from the client.
     */
    public function store(Request $request, DynamicForm $dynamicForm)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client || !$dynamicForm->is_active || !$dynamicForm->responses()->where('client_id', $client->id)->exists()) {
            abort(403, 'Unauthorized access to this form.');
        }

        // Validate and store the form submission (similar to public submit logic)
        $rules = [];
        foreach ($dynamicForm->fields as $field) {
            $fieldRules = $field->is_required ? ['required'] : [];
            if ($field->field_type === 'email') $fieldRules[] = 'email';
            elseif ($field->field_type === 'number') $fieldRules[] = 'numeric';
            elseif ($field->field_type === 'date') $fieldRules[] = 'date';
            $rules[$field->field_name] = implode('|', $fieldRules);
        }

        $validatedData = $request->validate($rules);

        $responseData = [];
        foreach ($dynamicForm->fields as $field) {
            $responseData[$field->field_name] = $request->input($field->field_name, null);
        }

        DynamicFormResponse::create([
            'dynamic_form_id' => $dynamicForm->id,
            'client_id' => $client->id,
            'response_data' => $responseData,
            'submitted_at' => now(),
        ]);

        return redirect()->route('clients.forms.index')->with('success', 'Form submitted successfully.');
    }


    // You might also add methods here for:
    // public function create(DynamicForm $form) { ... } // To display a blank form for filling
    // public function store(Request $request, DynamicForm $form) { ... } // To save a new form submission

}
