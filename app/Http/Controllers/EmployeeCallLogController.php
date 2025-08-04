<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ClientCacheService;
use App\Models\Employee;

class EmployeeCallLogController extends Controller
{
    /**
     * Display a listing of the employee's call logs.
     */
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Employee profile not found. Please contact support.');
        }

        $query = $employee->callLogs()->with(['client', 'employee.user'])
            ->orderBy('created_at', 'desc');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        $callLogs = $query->paginate(15)->withQueryString();

        // Fetch accessible clients for the filter dropdown
        $clients = $employee->accessibleClients()->where('is_active', true)->get();

        return view('employees.call-logs.index', compact('callLogs', 'clients'));
    }

    /**
     * Display the specified call log.
     */
    public function show($id)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Log::info('Employee not found for user ID: ' . (Auth::id() ?? 'null'));
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $callLog = $employee->callLogs()->with(['client', 'employee.user'])->findOrFail($id);
        return view('employees.call-logs.show', compact('callLog'));
    }

    /**
     * Show the form for creating a new call log.
     */
    public function create()
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Log::info('Employee not found for user ID: ' . (Auth::id() ?? 'null'));
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $clients = $employee->accessibleClients()->where('is_active', true)->get();
        if ($clients->isEmpty()) {
            Log::warning('No accessible clients found for employee ID: ' . $employee->id);
            return redirect()->route('employees.call-logs.index')->with('error', 'No clients found for this employee. Please contact support.');
        }

        $employees = \App\Models\Employee::with('user')->orderBy('id')->get();
        return view('employees.call-logs.create', compact('clients', 'employees'));
    }

    /**
     * Store a newly created call log in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'call_type' => 'required|string|in:incoming,outgoing',
            'call_date' => 'required|date',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'duration_minutes' => 'nullable|integer|min:0',
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|integer',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'create_task' => 'nullable|boolean',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $employee = Auth::user()->employee;
        $callLog = $employee->callLogs()->create([
            'client_id' => $validated['client_id'],
            'call_type' => $validated['call_type'],
            'call_date' => $validated['call_date'],
            'caller_name' => $validated['caller_name'],
            'caller_phone' => $validated['caller_phone'],
            'duration_minutes' => $validated['duration_minutes'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'notes' => $validated['notes'],
            'follow_up_required' => $validated['follow_up_required'],
            'follow_up_date' => $validated['follow_up_date'],
            'create_task' => $validated['create_task'],
            'employee_id' => $employee->id, // Override nullable employee_id with current employee
        ]);

        return redirect()->route('employees.call-logs.index')->with('success', 'Call log created successfully.');
    }

    /**
     * Show the form for editing the specified call log.
     */
    public function edit($id)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Log::info('Employee not found for user ID: ' . (Auth::id() ?? 'null'));
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $callLog = $employee->callLogs()->with(['client', 'employee.user'])->findOrFail($id);
        $clients = $employee->accessibleClients()->where('is_active', true)->get();
        $employees = \App\Models\Employee::with('user')->orderBy('id')->get();

        return view('employees.call-logs.edit', compact('callLog', 'clients', 'employees'));
    }

    /**
     * Update the specified call log in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Log::info('Employee not found for user ID: ' . (Auth::id() ?? 'null'));
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $callLog = $employee->callLogs()->findOrFail($id);

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'call_type' => 'required|string|in:incoming,outgoing',
            'call_date' => 'required|date',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'duration_minutes' => 'nullable|integer|min:0',
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|integer',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'create_task' => 'nullable|boolean',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $callLog->update([
            'client_id' => $validated['client_id'],
            'call_type' => $validated['call_type'],
            'call_date' => $validated['call_date'],
            'caller_name' => $validated['caller_name'],
            'caller_phone' => $validated['caller_phone'],
            'duration_minutes' => $validated['duration_minutes'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'notes' => $validated['notes'],
            'follow_up_required' => $validated['follow_up_required'],
            'follow_up_date' => $validated['follow_up_date'],
            'create_task' => $validated['create_task'],
            'employee_id' => $employee->id, // Ensure employee_id remains the current employee
        ]);

        return redirect()->route('employees.call-logs.index')->with('success', 'Call log updated successfully.');
    }

    /**
     * Remove the specified call log from storage.
     */
    public function destroy($id)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Log::info('Employee not found for user ID: ' . (Auth::id() ?? 'null'));
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $callLog = $employee->callLogs()->findOrFail($id);

        $callLog->delete();

        return redirect()->route('employees.call-logs.index')->with('success', 'Call log deleted successfully.');
    }
}