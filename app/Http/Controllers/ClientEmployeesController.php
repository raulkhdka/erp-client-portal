<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client; // Make sure your Client model exists
use App\Models\Employee; // Make sure your Employee model exists

class ClientEmployeesController extends Controller
{
    /**
     * Display a list of employees assigned to the authenticated client.
     */
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // Fetch all employees assigned to this client
        // Ensure your Client model has an 'assignedEmployees' relationship
        $assignedEmployees = $client->assignedEmployees()->with('user')->paginate(10);

        return view('clients.employees.index', compact('client', 'assignedEmployees'));
    }
}