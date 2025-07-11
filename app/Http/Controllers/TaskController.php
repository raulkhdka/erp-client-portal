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
        $query = Task::with(['client', 'assignedEmployee.user', 'creator.user', 'callLog'])
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

        return view('tasks.index', compact('tasks', 'clients', 'employees'));
    }

    /**
     * Display employee's assigned tasks (My Tasks)
     */
    public function myTasks(Request $request)
    {
        if (!Auth::user()->isEmployee()) {
            return redirect()->route('tasks.index');
        }

        $query = Task::with(['client', 'assignedEmployee.user', 'creator.user', 'callLog'])
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

        return view('tasks.my-tasks', compact('tasks', 'clients'));
    }

    /**
     * Show the form for creating a new resource.
     */    public function create(Request $request)
    {
        $clients = ClientCacheService::getClientsCollection();
        $employees = Employee::with('user')->orderBy('id')->get();

        // Auto-select client if coming from client view
        $selectedClientId = $request->get('client_id');

        return view('tasks.create', compact('clients', 'employees', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|integer|min:1|max:9',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        // Set call_log_id to null since we're creating standalone tasks
        $validated['call_log_id'] = null;

        // Set created_by to current logged in employee
        if (Auth::user()->isEmployee()) {
            $validated['created_by'] = Auth::user()->employee->id;
        } elseif (Auth::user()->isAdmin()) {
            // Admin creating task - if no assignment specified, assign to self if they're also an employee
            if (!$validated['assigned_to'] && Auth::user()->employee) {
                $validated['assigned_to'] = Auth::user()->employee->id;
            }
            $validated['created_by'] = Auth::user()->employee ? Auth::user()->employee->id : null;
        }

        $task = Task::create($validated);

        return redirect()->route('tasks.index')
                        ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['client', 'assignedEmployee.user', 'creator.user', 'callLog']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $clients = ClientCacheService::getClientsCollection();
        $employees = Employee::with('user')->orderBy('id')->get();

        return view('tasks.edit', compact('task', 'clients', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|integer|min:1|max:9',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
                        ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success', 'Task deleted successfully.');
    }
}
