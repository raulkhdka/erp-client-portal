<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch all form responses submitted by this client
        // Ensure your Client model has a 'formResponses' relationship
        $formResponses = $client->formResponses()->with('dynamicForm')->latest()->paginate(15);

        // Fetch available forms for the client to fill out
        // Adjust this query based on how you determine which forms are available to clients
        $availableForms = DynamicForm::where('is_active', true) // Only active forms
                                     ->where('is_client_facing', true) // Assuming a column to mark forms as client-facing
                                     ->get();

        return view('client.forms.index', compact('client', 'formResponses', 'availableForms'));
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

        return view('client.forms.show', compact('client', 'dynamicFormResponse'));
    }

    // You might also add methods here for:
    // public function create(DynamicForm $form) { ... } // To display a blank form for filling
    // public function store(Request $request, DynamicForm $form) { ... } // To save a new form submission
}