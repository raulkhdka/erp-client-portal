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
use Illuminate\Validation\Rule;

class CallLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CallLog::with(['client', 'employee', 'tasks.assignedTo'])
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

        $callLogs = $query->paginate(10)->withQueryString();

        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();

        return view('admin.call-logs.index', compact('callLogs', 'clients', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();
        return view('admin.call-logs.create', compact('clients', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
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
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        // Determine the employee_id for the call log
        if (Auth::user()->role === 'admin') {
            if ($request->filled('employee_id')) {
                $validated['employee_id'] = $request->employee_id;
            } else {
                $currentEmployee = Auth::user()->employee;
                $validated['employee_id'] = $currentEmployee ? $currentEmployee->id : null;
            }
        } else {
            $currentEmployee = Auth::user()->employee;
            if (!$currentEmployee) {
                return redirect()->back()->withErrors(['employee_id' => 'Your account is not linked to an employee record.']);
            }
            $validated['employee_id'] = $currentEmployee->id;
        }

        // Ensure the caller_phone from select is used, falling back to direct input
        $validated['caller_phone'] = $request->input('caller_phone_select', $request->input('caller_phone'));

        DB::beginTransaction();
        try {
            // Create call log
            $callLog = CallLog::create($validated);

            // Create task if requested and status is not resolved
            if ($request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED) {
                // Use assigned_to if provided, otherwise fallback to employee_id
                $assignedTo = $request->input('assigned_to') ?: $validated['employee_id'];
                if ($assignedTo && !Employee::find($assignedTo)) {
                    throw new \Exception('Invalid employee ID for task assignment.');
                }

                // Validate created_by
                $createdBy = Auth::user()->id;
                if (!\App\Models\User::find($createdBy)) {
                    throw new \Exception('Invalid user ID for created_by.');
                }

                $task = Task::create([
                    'call_log_id' => $callLog->id,
                    'client_id' => $callLog->client_id,
                    'assigned_to' => $assignedTo,
                    'created_by' => $createdBy,
                    'title' => 'Follow-up on Call: ' . $callLog->subject,
                    'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                    'due_date' => $callLog->follow_up_date,
                    'status' => $callLog->status,
                    'priority' => $callLog->priority,
                ]);
                // Log::info('New task created for call log ' . $callLog->id, ['assigned_to' => $assignedTo, 'created_by' => $createdBy]);
            }

            DB::commit();
            return redirect()->route('admin.call-logs.index')->with('status_update_success', 'Call log recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Call log creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('status_update_error', 'Failed to record call log: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CallLog $callLog)
    {
        $callLog->load(['client', 'employee', 'tasks.assignedTo']);
        return view('admin.call-logs.show', compact('callLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CallLog $callLog)
    {
        $callLog->load('tasks');
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::orderBy('id')->get();
        return view('admin.call-logs.edit', compact('callLog', 'clients', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CallLog $callLog)
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
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        // Debug: Log the raw request data
        Log::info('Raw request data', $request->all());

        // Determine the employee_id for the call log
        $assignedEmployeeId = null;
        $currentUser = Auth::user();

        if ($currentUser->role === 'admin') {
            if ($request->filled('employee_id')) {
                $assignedEmployeeId = $request->employee_id;
            } else {
                $currentEmployee = $currentUser->employee;
                if ($currentEmployee) {
                    $assignedEmployeeId = $currentEmployee->id;
                } else {
                    return redirect()->back()->withInput()->withErrors([
                        'employee_id' => 'As an admin, you must either select an employee or ensure your account is linked to an employee record.'
                    ])->with('status_update_error', 'Failed to update call log: Admin user is not linked to an employee record.');
                }
            }
        } else {
            $currentEmployee = $currentUser->employee;
            if ($currentEmployee) {
                $assignedEmployeeId = $currentEmployee->id;
            } else {
                return redirect()->back()->withInput()->withErrors([
                    'employee_id' => 'Your user account is not linked to an employee record. Please contact support.'
                ])->with('error', 'Failed to update call log: Current user is not linked to an employee record.');
            }
        }
        $validated['employee_id'] = $assignedEmployeeId;

        // Handle caller_phone: Use the new value if provided and different, otherwise keep the existing one
        $newCallerPhone = $request->input('caller_phone_select', $request->input('caller_phone'));
        Log::info('New caller_phone', ['new' => $newCallerPhone, 'old' => $callLog->caller_phone]);
        if (!empty($newCallerPhone) && $newCallerPhone !== $callLog->caller_phone) {
            $validated['caller_phone'] = $newCallerPhone;
        } else {
            $validated['caller_phone'] = $callLog->caller_phone; // Retain existing if no valid change
        }

        DB::beginTransaction();
        try {
            // Update call log
            $callLog->update($validated);

            // Handle task creation/update/deletion
            $shouldCreateOrUpdateTask = $request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED;
            $existingTask = $callLog->tasks()->first();

            if ($shouldCreateOrUpdateTask) {
                $assignedTo = $request->input('assigned_to') ?: $assignedEmployeeId;
                if ($assignedTo && !Employee::find($assignedTo)) {
                    throw new \Exception('Invalid employee ID for task assignment.');
                }

                $createdBy = Auth::user()->id;
                if (!\App\Models\User::find($createdBy)) {
                    throw new \Exception('Invalid user ID for created_by.');
                }

                if ($existingTask) {
                    $existingTask->update([
                        'assigned_to' => $assignedTo,
                        'title' => 'Follow-up on Call: ' . $callLog->subject,
                        'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                        'due_date' => $callLog->follow_up_date,
                        'status' => $callLog->status,
                        'priority' => $callLog->priority,
                        'client_id' => $callLog->client_id,
                    ]);
                    // Log::info('Task updated for call log ' . $callLog->id, ['assigned_to' => $assignedTo, 'created_by' => $createdBy]);
                } else {
                    Task::create([
                        'call_log_id' => $callLog->id,
                        'client_id' => $callLog->client_id,
                        'assigned_to' => $assignedTo,
                        'created_by' => $createdBy,
                        'title' => 'Follow-up on Call: ' . $callLog->subject,
                        'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                        'due_date' => $callLog->follow_up_date,
                        'status' => $callLog->status,
                        'priority' => $callLog->priority,
                    ]);
                    // Log::info('New task created for call log ' . $callLog->id, ['assigned_to' => $assignedTo, 'created_by' => $createdBy]);
                }
            } else {
                if ($existingTask) {
                    $existingTask->delete();
                    // Log::info('Task deleted for call log ' . $callLog->id . ' due to unchecked checkbox or resolved status.');
                }
            }

            DB::commit();
            return redirect()->route('admin.call-logs.index')->with('status_update_success', 'Call log updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Call log update failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('status_update_error', 'Failed to update call log: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CallLog $callLog)
    {
        try {
            $callLog->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Failed to delete employee: ' . $e->getMessage()], 500);
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

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
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
            'primary_contact' => $client->name ?? $client->user->name ?? $client->contact_person ?? null,
            'phones' => $client->phones->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $phone->phone,
                    'type' => $phone->type,
                    'is_primary' => (bool)$phone->is_primary
                ];
            })->toArray()
        ];

        if (empty($contacts['phones']) && $client->main_phone_number) {
            $contacts['phones'][] = [
                'id' => null,
                'phone' => $client->main_phone_number,
                'type' => 'Main',
                'is_primary' => true
            ];
        }
        if (empty($contacts['phones']) && $client->secondary_phone_number) {
            $contacts['phones'][] = [
                'id' => null,
                'phone' => $client->secondary_phone_number,
                'type' => 'Secondary',
                'is_primary' => false
            ];
        }

        return response()->json($contacts);
    }

     /**
     * Display the call history for a specific client.
     */
        public function history(Request $request)
        {
            $query = CallLog::query();

            // Search by caller_name or caller_phone
            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('caller_name', 'like', '%' . $search . '%')
                      ->orWhere('caller_phone', 'like', '%' . $search . '%');
                });
            }

            // Filter by caller_name
            if ($callerName = $request->query('caller_name')) {
                $query->where('caller_name', $callerName);
            }

            // Filter by caller_phone
            if ($callerPhone = $request->query('caller_phone')) {
                $query->where('caller_phone', $callerPhone);
            }

            // Eager load relationships
            $callLogs = $query->with(['client', 'employee'])
                             ->orderBy('call_date', 'desc')
                             ->paginate(10);

            // Get unique caller names and phone numbers for filters
            $callerNames = CallLog::distinct()->pluck('caller_name')->filter()->sort()->values();
            $callerPhones = CallLog::distinct()->pluck('caller_phone')->filter()->sort()->values();

            return view('admin.call-logs.history', compact('callLogs', 'callerNames', 'callerPhones'));
        }

        // Other methods (index, show, etc.) remain unchanged
}
