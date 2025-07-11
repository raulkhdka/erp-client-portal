<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClientService::with(['client', 'assignedEmployee']);

        // Filter by client if specified
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned employee
        if ($request->filled('employee_id')) {
            $query->where('assigned_employee_id', $request->employee_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('company_name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('client-services.index', compact('services', 'clients', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::orderBy('company_name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $selectedClientId = $request->get('client_id');

        return view('client-services.create', compact('clients', 'employees', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,suspended,expired',
            'amount' => 'nullable|numeric|min:0',
            'cost_type' => 'required|in:one_time,monthly,yearly',
            'next_due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'assigned_employee_id' => 'nullable|exists:users,id',
            'service_links' => 'nullable|array',
            'service_links.*.name' => 'required_with:service_links|string',
            'service_links.*.url' => 'required_with:service_links|url',
            'service_links.*.username' => 'nullable|string',
            'service_links.*.password' => 'nullable|string',
            'service_links.*.token' => 'nullable|string',
        ]);

        $clientService = ClientService::create($validated);

        return redirect()
            ->route('client-services.show', $clientService)
            ->with('success', 'Client service created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientService $clientService)
    {
        $clientService->load(['client', 'assignedEmployee']);

        return view('client-services.show', compact('clientService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientService $clientService)
    {
        $clients = Client::orderBy('company_name')->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('client-services.edit', compact('clientService', 'clients', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientService $clientService)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,suspended,expired',
            'amount' => 'nullable|numeric|min:0',
            'cost_type' => 'required|in:one_time,monthly,yearly',
            'next_due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'assigned_employee_id' => 'nullable|exists:users,id',
            'service_links' => 'nullable|array',
            'service_links.*.name' => 'required_with:service_links|string',
            'service_links.*.url' => 'required_with:service_links|url',
            'service_links.*.username' => 'nullable|string',
            'service_links.*.password' => 'nullable|string',
            'service_links.*.token' => 'nullable|string',
        ]);

        $clientService->update($validated);

        return redirect()
            ->route('client-services.show', $clientService)
            ->with('success', 'Client service updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientService $clientService)
    {
        $clientService->delete();

        return redirect()
            ->route('client-services.index')
            ->with('success', 'Client service deleted successfully!');
    }
}
