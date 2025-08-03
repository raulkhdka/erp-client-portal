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

        $tasks = $query->paginate(15)->withQueryString();
        $clients = ClientCacheService::getClientsCollection();
        $employees = Employee::with('user')->orderBy('id')->get();

        return view('admin.tasks.index', compact('tasks', 'clients', 'employees'));
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
        $clients = ClientCacheService::getClientsCollection();

        return view('admin.tasks.my-tasks', compact('tasks', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = ClientCacheService::getClientsCollection();
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
        $task->load(['client', 'assignedTo', 'createdBy', 'callLog']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $clients = ClientCacheService::getClientsCollection();
        $employees = Employee::with('user')->orderBy('id')->get();
        $callLogs = CallLog::with('client')
            ->where('client_id', $task->client_id)
            ->latest()
            ->get();

        return view('admin.tasks.edit', compact('task', 'clients', 'employees', 'callLogs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
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

        $task->update([
            'client_id' => $validated['client_id'],
            'assigned_to' => $validated['assigned_to'],
            'call_log_id' => $validated['call_log_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'],
            'started_at' => $validated['started_at'],
            'completed_at' => $validated['completed_at'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.tasks.index', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
