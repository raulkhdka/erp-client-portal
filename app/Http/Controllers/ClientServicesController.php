<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client; // Make sure your Client model exists
use App\Models\Service; // Make sure your Service model exists

class ClientServicesController extends Controller
{
    /**
     * Display a list of all services assigned to the authenticated client.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }
        $client = $user->client;

        // Fetch all services assigned to this client
        // Ensure your Client model has a 'services' relationship
        $assignedServices = $client->services()->withPivot('status', 'description', 'created_at')->paginate(10);

        // --- NEW: Calculate statistics for assigned services ---
        $totalAssignedServices = $client->services()->count();
        $activeServices = $client->services()->wherePivot('status', 'active')->count();
        $inactiveServices = $client->services()->wherePivot('status', 'inactive')->count();
        $suspendedServices = $client->services()->wherePivot('status', 'suspended')->count();
        $expiredServices = $client->services()->wherePivot('status', 'expired')->count();
        $assignedEmployee = $client->assignedEmployee ?? null; // Assuming a belongsTo relationship
        // --- END NEW ---

        return view('client.services.index', compact(
            'client',
            'assignedServices',
            'totalAssignedServices',   // Pass these new variables to the view
            'activeServices',
            'inactiveServices',
            'suspendedServices',
            'expiredServices',
            'assignedEmployee' // Pass the assigned employee to the view
        ));

        // return view('client.services.index', compact('client', 'assignedServices'));
    }
}