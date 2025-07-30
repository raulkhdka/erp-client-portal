<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Task;
use App\Services\ClientCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallLog::with(['client', 'employee', 'tasks'])
            ->orderBy('call_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by employee (for employee role)
        if (Auth::user()->isEmployee()) {
            $query->where('employee_id', Auth::user()->employee->id);
        } elseif ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('caller_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $callLogs = $query->paginate(15)->withQueryString();

        $clients = ClientCacheService::getClientsCollection();
        $employees = Employee::with('user')->orderBy('id')->get();

        return view('call-logs.index', compact('callLogs', 'clients', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();
        return view('call-logs.create', compact('clients', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'call_type' => 'required|string|in:incoming,outgoing',
            'call_date' => 'required|date',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'duration_minutes' => 'nullable|integer|min:0',
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|integer', // Assuming status is integer (1 for Active, etc.)
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'create_task' => 'nullable|boolean', // If it's a checkbox, it might be 'on' or null
            // employee_id is handled dynamically
            'employee_id' => 'nullable|exists:employees,id', // Add validation for employee_id if it's explicitly passed
        ]);

        // Determine the employee_id for the call log
        if (Auth::user()->role === 'admin') {
            // If admin and employee_id is provided in the request, use it.
            // Otherwise, check if the admin themselves have an employee record.
            if ($request->filled('employee_id')) {
                $validated['employee_id'] = $request->employee_id;
            } else {
                // Admin is recording for themselves, try to link to their employee record
                $currentEmployee = Auth::user()->employee; // Try to get the associated employee
                if ($currentEmployee) {
                    $validated['employee_id'] = $currentEmployee->id;
                } else {
                    // Admin user does not have an employee record, handle this case.
                    // Option 1: Log an error or throw an exception if every admin MUST be an employee.
                    // Option 2: Set employee_id to null or a default 'admin' employee_id if such exists.
                    // For now, let's set it to null if no employee record is found for the admin.
                    $validated['employee_id'] = null; // Or handle as an error: throw new \Exception("Admin user is not associated with an employee record.");
                }
            }
        } else { // This is for 'employee' role users (and potentially others if your middleware allows)
            // For non-admin roles (e.g., 'employee'), the call log is implicitly assigned to them.
            $currentEmployee = Auth::user()->employee;
            if ($currentEmployee) {
                $validated['employee_id'] = $currentEmployee->id;
            } else {
                // This case indicates a user with 'employee' role (or similar) but no linked employee record.
                // This should ideally not happen if your user/employee setup is robust.
                // You might want to throw an error or redirect back with a message.
                return redirect()->back()->withErrors(['employee_id' => 'Your user account is not linked to an employee record. Please contact support.']);
            }
        }

        // Ensure the caller_phone from select is used, falling back to direct input
        $validated['caller_phone'] = $request->input('caller_phone_select', $request->input('caller_phone'));


        DB::beginTransaction();
        try {
            // Create call log
            $callLog = CallLog::create($validated);

            // Create task if requested and status is not resolved
            if ($request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED) {

                $task = Task::create([
                    'call_log_id' => $callLog->id, // This is fine, $callLog will have an ID after creation
                    // ... rest of task creation fields based on your logic ...
                    // e.g., 'assigned_to_user_id' => $validated['employee_id'] ? Employee::find($validated['employee_id'])->user_id : Auth::id(),
                    // Or directly to the employee's user_id if that's how your tasks are assigned
                    'assigned_to' => Auth::user()->id, // Assign task to the current user who recorded the call
                    'title' => 'Follow-up on Call: ' . $callLog->subject,
                    'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                    'due_date' => $callLog->follow_up_date,
                    'status' => $callLog->status, // Or another appropriate initial status for a new task
                    'priority' => $callLog->priority,
                    'client_id' => $callLog->client_id,
                    'created_by' => Auth::user()->id,
                ]);
            }

            DB::commit();
            return redirect()->route('call-logs.index')->with('success', 'Call log recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Call log creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Failed to record call log. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog)
    {
        $callLog->load(['client', 'employee', 'tasks.assignedTo']);
        return view('call-logs.show', compact('callLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallLog $callLog)
    {
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();
        return view('call-logs.edit', compact('callLog', 'clients', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallLog $callLog)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'call_type' => 'required|in:incoming,outgoing',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|integer|min:1|max:9',
            'call_date' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:0',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:today'
        ]);

        DB::beginTransaction();
        try {
            $callLog->update($validated);

            // Update related task status if exists
            if ($callLog->task) {
                $callLog->task->update([
                    'priority' => $callLog->priority,
                    'status' => $callLog->status,
                    'due_date' => $callLog->follow_up_date ?? $callLog->task->due_date
                ]);
            }

            DB::commit();

            return redirect()->route('call-logs.index')
                ->with('success', 'Call log updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update call log: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallLog $callLog)
    {
        try {
            $callLog->delete();
            return redirect()->route('call-logs.index')
                ->with('success', 'Call log deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('call-logs.index')
                ->with('error', 'Failed to delete call log: ' . $e->getMessage());
        }
    }

    /**
     * Update status of call log
     */
    public function updateStatus(Request $request, CallLog $callLog)
    {
        $validated = $request->validate([
            'status' => 'required|integer|min:1|max:9'
        ]);

        DB::beginTransaction();
        try {
            $callLog->update(['status' => $validated['status']]);

            // Update related task status if exists
            if ($callLog->task) {
                $callLog->task->update(['status' => $validated['status']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client contact information for AJAX
     */
    public function getClientContacts(Client $client)
    {
        $client->load(['phones', 'user']); // Ensure 'user' and 'phones' are loaded

        $contacts = [
            'primary_contact' => $client->name ?? $client->user->name ?? $client->contact_person ?? null, // Added null coalescing for safety
            'phones' => $client->phones->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $phone->phone, // Assuming 'phone' is the column name for the number
                    'type' => $phone->type,
                    'is_primary' => (bool)$phone->is_primary // Cast to boolean for consistency
                ];
            })->toArray()
        ];

        // Add a check if there are no phones from the relationship, but client has a direct 'phone' field
        // This is optional, depends on your schema (e.g., if Client has a 'main_phone' column)
        if (empty($contacts['phones']) && $client->main_phone_number) { // Adjust 'main_phone_number' to your actual column name
            $contacts['phones'][] = [
                'id' => null, // No ID for direct client phone
                'phone' => $client->main_phone_number,
                'type' => 'Main', // Or 'Office', 'Default'
                'is_primary' => true
            ];
        }
        // Similarly for a secondary phone if you have one
        if (empty($contacts['phones']) && $client->secondary_phone_number) { // Adjust 'secondary_phone_number'
            $contacts['phones'][] = [
                'id' => null,
                'phone' => $client->secondary_phone_number,
                'type' => 'Secondary',
                'is_primary' => false
            ];
        }


        return response()->json($contacts);
    }
}
