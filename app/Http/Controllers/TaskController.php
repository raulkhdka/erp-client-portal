<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Client;
use App\Models\Employee;
use App\Models\CallLog;
use App\Services\ClientCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['client', 'assignedTo', 'createdBy', 'callLog'])
            ->orderBy('created_at', 'desc');

        // Filter by employee role - employees only see their assigned tasks
        if (Auth::user()->isEmployee()) {
            $query->where('assigned_to', Auth::user()->employee->id);
        }

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

        // Filter by assigned employee (admin only)
        if ($request->filled('assigned_to') && Auth::user()->isAdmin()) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->paginate(16)->withQueryString();
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();

        // Count tasks by status
        $tasksCount = [
            'total' => Task::count(),
            'pending' => Task::where('status', 1)->count(), // Assuming 1 is Pending
            'in_progress' => Task::where('status', 2)->count(), // Assuming 2 is In Progress
            'completed' => Task::whereIn('status', [7, 8])->count(), // Assuming 7 is Completed, 8 is Resolved
        ];

        return view('admin.tasks.index', compact('tasks', 'clients', 'employees', 'tasksCount'));
    }

    /**
     * Display employee's assigned tasks (My Tasks)
     */
    public function myTasks(Request $request)
    {
        if (!Auth::user()->isEmployee()) {
            return redirect()->route('admin.tasks.index');
        }

        $query = Task::with(['client', 'assignedTo', 'createdBy', 'callLog'])
            ->where('assigned_to', Auth::user()->employee->id)
            ->orderBy('created_at', 'desc');

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

        $tasks = $query->paginate(15)->withQueryString();
        $clients = ClientCacheService::getClientsWithUser();

        return view('admin.tasks.my-tasks', compact('tasks', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();
        $callLogs = CallLog::latest()->get();

        // Auto-select client if coming from client view
        $selectedClientId = $request->get('client_id');

        return view('admin.tasks.create', compact('clients', 'employees', 'selectedClientId', 'callLogs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'call_log_id' => 'nullable|exists:call_logs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|integer|min:1|max:9',
            'due_date' => 'nullable|date',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $validated['call_log_id'] = null;

        // ✅ Set the creator of the task (user_id)
        $validated['created_by'] = Auth::id();

        // ✅ Optionally assign to self if admin and not specified
        if (Auth::user()->isAdmin() && !$validated['assigned_to'] && Auth::user()->employee) {
            $validated['assigned_to'] = Auth::user()->employee->id;
        }

        $task = Task::create($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Load relationships
        $task->load(['client', 'assignedTo.user', 'createdBy', 'callLog']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
                'statusOptions' => Task::getStatusOptions(), // Include statusOptions for frontend
                'status_label' => $task->status_label,
                'priority_label' => ucfirst($task->priority),
            ], 200);
        }

        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        try {
            // Load relationships
            $task->load(['client', 'assignedTo', 'callLog', 'adminCreator']);

            // Get clients
            $clients = Client::select('id', 'company_name', 'name')
                ->orderBy('company_name')
                ->get();

            // Get employees
            $employees = Employee::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->name,
                    ];
                });

            // Get recent call logs (limit to avoid performance issues)
            $callLogs = CallLog::select('id', 'subject', 'notes', 'call_date')
                ->latest()
                ->limit(100)
                ->get();

            // Get status options from the model
            $statusOptions = Task::getStatusOptions();

            // Return JSON response for AJAX requests
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'notes' => $task->notes,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'due_date' => $task->due_date ? Carbon::parse($task->due_date)->toISOString() : null,
                        'started_at' => $task->started_at ? Carbon::parse($task->started_at)->toISOString() : null,
                        'completed_at' => $task->completed_at ? Carbon::parse($task->completed_at)->toISOString() : null,
                        'client_id' => $task->client_id,
                        'call_log_id' => $task->call_log_id,
                        'assigned_to' => $task->assigned_to, // Ensure consistency with update method
                        // Include relationship data
                        'client' => $task->client,
                        'assignedTo' => $task->assignedTo,
                        'callLog' => $task->callLog,
                        'created_by' => $task->adminCreator,
                    ],
                    'clients' => $clients,
                    'employees' => $employees,
                    'callLogs' => $callLogs->map(function ($log) {
                        return [
                            'id' => $log->id,
                            'subject' => $log->subject,
                            'notes' => $log->notes,
                            'call_date' => $log->call_date ? Carbon::parse($log->call_date)->toISOString() : null,
                        ];
                    }),
                    'statusOptions' => $statusOptions
                ]);
            }

            // Return view for regular requests
            return view('admin.tasks.edit', compact('task', 'clients', 'employees', 'callLogs', 'statusOptions'));
        } catch (\Exception $e) {
            Log::error('Task edit error: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load task for editing.'
                ], 500);
            }

            return redirect()->route('admin.tasks.index')
                ->with('error', 'Failed to load task for editing.');
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            // Log incoming request data for debugging
            Log::debug('Task update request received:', [
                'task_id' => $task->id,
                'input' => $request->all()
            ]);

            // Validation rules using model constants
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'notes' => 'nullable|string',
                'priority' => ['required', Rule::in([
                    Task::PRIORITY_LOW,
                    Task::PRIORITY_MEDIUM,
                    Task::PRIORITY_HIGH,
                    Task::PRIORITY_URGENT
                ])],
                'status' => ['required', 'integer', Rule::in(array_keys(Task::getStatusOptions()))],
                'due_date' => 'nullable|date',
                'started_at' => 'nullable|date',
                'completed_at' => 'nullable|date',
                'client_id' => 'required|exists:clients,id',
                'assigned_to' => 'nullable|exists:employees,id',
                'call_log_id' => 'nullable|exists:call_logs,id',
                'estimated_hours' => 'nullable|integer|min:0',
                'actual_hours' => 'nullable|integer|min:0',
            ];

            // Custom validation messages
            $messages = [
                'title.required' => 'Task title is required.',
                'description.required' => 'Task description is required.',
                'priority.required' => 'Priority is required.',
                'priority.in' => 'Invalid priority selected.',
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status selected.',
                'client_id.required' => 'Client selection is required.',
                'client_id.exists' => 'Selected client does not exist.',
                'assigned_to.exists' => 'Selected employee does not exist.',
                'call_log_id.exists' => 'Selected call log does not exist.',
                'estimated_hours.integer' => 'Estimated hours must be a number.',
                'actual_hours.integer' => 'Actual hours must be a number.',
            ];

            // Validate the request
            $validatedData = $request->validate($rules, $messages);

            // Log validated data
            Log::debug('Validated data for task update:', [
                'task_id' => $task->id,
                'validated' => $validatedData
            ]);

            // Convert status to integer
            $validatedData['status'] = (int) $validatedData['status'];

            // Handle status-based datetime fields
            $status = $validatedData['status'];

            // Handle started_at based on status
            if ($status < Task::STATUS_IN_PROGRESS) {
                $validatedData['started_at'] = null;
            } elseif ($status >= Task::STATUS_IN_PROGRESS && !$task->started_at && empty($validatedData['started_at'])) {
                $validatedData['started_at'] = now()->toISOString();
            }

            // Handle completed_at based on status
            if (!in_array($status, [Task::STATUS_COMPLETED, Task::STATUS_RESOLVED])) {
                $validatedData['completed_at'] = null;
            } elseif (in_array($status, [Task::STATUS_COMPLETED, Task::STATUS_RESOLVED]) && !$task->completed_at && empty($validatedData['completed_at'])) {
                $validatedData['completed_at'] = now()->toISOString();
            }

            // Update the task
            $task->update($validatedData);

            // Log the update
            Log::info('Task updated', [
                'task_id' => $task->id,
                'updated_by' => Auth::id(),
                'changes' => $task->getChanges()
            ]);

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                $task->refresh(); // Refresh the task to get updated relationships
                return response()->json([
                    'success' => true,
                    'message' => 'Task updated successfully!',
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'notes' => $task->notes,
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'due_date' => $task->due_date ? Carbon::parse($task->due_date)->toISOString() : null,
                        'started_at' => $task->started_at ? Carbon::parse($task->started_at)->toISOString() : null,
                        'completed_at' => $task->completed_at ? Carbon::parse($task->completed_at)->toISOString() : null,
                        'client_id' => $task->client_id,
                        'call_log_id' => $task->call_log_id,
                        'assigned_to' => $task->assigned_to,
                        'client' => $task->client,
                        'assignedTo' => $task->assignedTo,
                        'callLog' => $task->callLog,
                        'created_by' => $task->adminCreator,
                    ],
                    'redirect' => route('admin.tasks.index')
                ]);
            }

            return redirect()->route('admin.tasks.index')
                ->with('success', 'Task updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Task update validation failed:', [
                'task_id' => $task->id,
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Task update error: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update task. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update task. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (!Auth::user()->isAdmin()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Task deleted successfully.'
            ], 200);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Show all the tasks created
     */
    public function showAll(Request $request, Client $client)
    {
        $query = Task::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->with(['client', 'createdBy', 'assignedTo.user', 'callLog'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tasks.all', compact('tasks'));
    }

    /**
     * Update task status via AJAX
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Ensure the user is an admin
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'message' => 'You are not authorized to update this task.'
            ], 403);
        }

        // Validate the status input
        $validatedData = $request->validate([
            'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9',
        ]);

        // Auto-set timestamps based on status
        $updateData = [
            'status' => $validatedData['status'],
        ];

        // Set started_at when status changes to "In Progress" (2)
        if ($validatedData['status'] == 2 && !$task->started_at) {
            $updateData['started_at'] = now();
        }

        // Set completed_at when status changes to "Completed" (7) or "Resolved" (8)
        if (in_array($validatedData['status'], [7, 8]) && !$task->completed_at) {
            $updateData['completed_at'] = now();
        }

        // Clear completed_at if status is changed back from completed/resolved
        if (!in_array($validatedData['status'], [7, 8]) && $task->completed_at) {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        // Log for debugging
        Log::info('Task status updated', [
            'task_id' => $task->id,
            'status' => $task->status,
            'status_label' => $task->status_label
        ]);

        return response()->json([
            'status_label' => $task->status_label,
            'message' => 'Task status updated successfully.'
        ], 200);
    }

    /**
     * Update task status from "All Tasks" page
     */
    public function updateStatusAll(Request $request, Task $task)
    {
        // Ensure the user is an admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'You are not authorized to update this task.');
        }

        // Validate the status input
        $validatedData = $request->validate([
            'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9',
        ]);

        // Auto-set timestamps based on status
        $updateData = [
            'status' => $validatedData['status'],
        ];

        // Set started_at when status changes to "In Progress" (2)
        if ($validatedData['status'] == 2 && !$task->started_at) {
            $updateData['started_at'] = now();
        }

        // Set completed_at when status changes to "Completed" (7) or "Resolved" (8)
        if (in_array($validatedData['status'], [7, 8]) && !$task->completed_at) {
            $updateData['completed_at'] = now();
        }

        // Clear completed_at if status is changed back from completed/resolved
        if (!in_array($validatedData['status'], [7, 8]) && $task->completed_at) {
            $updateData['completed_at'] = null;
        }

        $task->update($updateData);

        return redirect()->route('admin.tasks.all')
            ->with('success', 'Task status updated successfully.');
    }
}
