<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeTaskController extends Controller
{
    public function index(Request $request)
    {

        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        Log::info('Employee ID: ' . $employee->id); // Debug log

        $query = Task::with(['client', 'assignedTo.user', 'createdBy', 'callLog'])
            ->where('assigned_to', $employee->id)
            ->orderByRaw('CASE
            WHEN status IN (7,8) THEN 2
            WHEN due_date < CURDATE() THEN 0
            ELSE 1 END')
            ->orderBy('due_date', 'asc');

        // Debug log the raw query
        Log::info('Task Query: ' . $query->toSql());
        Log::info('Query Bindings:', $query->getBindings());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->paginate(15)->withQueryString();

        // Calculate task counts for statistics
        $tasksCount = [
            'total' => Task::where('assigned_to', $employee->id)->count(),
            'pending' => Task::where('assigned_to', $employee->id)
                ->where('status', Task::STATUS_PENDING)
                ->count(),
            'in_progress' => Task::where('assigned_to', $employee->id)
                ->where('status', Task::STATUS_IN_PROGRESS)
                ->count(),
            'completed' => Task::where('assigned_to', $employee->id)
                ->whereIn('status', [Task::STATUS_COMPLETED, Task::STATUS_RESOLVED])
                ->count()
        ];

        return view('employees.tasks.index', compact('tasks', 'tasksCount'));
    }



    public function show($taskId)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        $task = Task::where('id', $taskId)
            ->where('assigned_to', $employee->id)
            ->firstOrFail();

        return view('employees.tasks.show', compact('task'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee profile not found.');
        }

        // Ensure the employee can only update their own tasks
        if ($task->assigned_to !== $employee->id) {
            return redirect()->back()->with('error', 'You are not authorized to update this task.');
        }

        $validatedData = $request->validate([
            'status' => 'required|integer|in:2,3,4,7', // Only allow In Progress, On Hold, Pending Review, and Completed
        ]);

        $task->update([
            'status' => $validatedData['status'],
            'completed_at' => $validatedData['status'] == Task::STATUS_COMPLETED ? now() : null,
        ]);

        return redirect()->route('employees.tasks.index')
            ->with('success', 'Task status updated successfully.');
    }
}
