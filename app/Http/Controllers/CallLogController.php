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

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallLog::with(['client', 'employee', 'task'])
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
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('caller_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
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
            'employee_id' => 'nullable|exists:employees,id',
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
            'follow_up_date' => 'nullable|date|after:today',
            'create_task' => 'boolean'
        ]);

        // Set employee_id: use provided value for admins, or current user for employees
        if (Auth::user()->role === 'admin' && $request->filled('employee_id')) {
            $validated['employee_id'] = $request->employee_id;
        } else {
            $validated['employee_id'] = Auth::user()->employee->id;
        }

        DB::beginTransaction();
        try {
            // Create call log
            $callLog = CallLog::create($validated);

            // Create task if requested and status is not resolved
            if ($request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED) {
                $task = Task::create([
                    'call_log_id' => $callLog->id,
                    'client_id' => $callLog->client_id,
                    'assigned_to' => $callLog->employee_id,
                    'created_by' => $callLog->employee_id,
                    'title' => 'Follow-up: ' . $callLog->subject,
                    'description' => $callLog->description,
                    'priority' => $callLog->priority,
                    'status' => $callLog->status,
                    'due_date' => $callLog->follow_up_date ?? now()->addDays(3),
                    'notes' => $callLog->notes
                ]);

                $message = 'Call log created successfully and task #' . $task->id . ' has been assigned.';
            } else {
                $message = 'Call log created successfully.';
            }

            DB::commit();

            return redirect()->route('call-logs.index')
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withErrors(['error' => 'Failed to create call log: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog)
    {
        $callLog->load(['client', 'employee.user', 'tasks.assignedEmployee.user']);
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
        $client->load(['phones', 'user']);

        $contacts = [
            'primary_contact' => $client->user->name,
            'phones' => $client->phones->map(function($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $phone->phone,
                    'type' => $phone->type,
                    'is_primary' => $phone->is_primary
                ];
            })->toArray()
        ];

        return response()->json($contacts);
    }
}
