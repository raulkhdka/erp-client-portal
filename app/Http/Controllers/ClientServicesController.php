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
        $client = Auth::user()->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found.');
        }

        $assignedServices = $client->services()
            ->select('services.*')
            ->withPivot(['status', 'description', 'created_at', 'assigned_by'])
            ->paginate(15);

        $assignedByIds = $assignedServices->pluck('pivot.assigned_by')->filter()->unique();

        $assigners = \App\Models\User::whereIn('id', $assignedByIds)->get()->keyBy('id');

        return view('clients.services.index', compact('assignedServices', 'assigners'));
    }
}
