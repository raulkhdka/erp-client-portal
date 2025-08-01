<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\CallLog;
use App\Models\Task;

class DashboardController extends Controller
{

    /**
     * Redirects authenticated users to their specific dashboard based on role.
     */
    public function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }

        // Fallback for undefined roles: log out and redirect to login with an error
        Auth::logout();
        return redirect()->route('login')->with('error', 'Your account has an undefined role. Please contact support.');
    }


    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $totalClients = Client::count();
        $totalEmployees = Employee::count();
        $activeClients = Client::where('status', 'active')->count();
        $recentClients = Client::with('Phones')->latest()->take(5)->get();

        // Call logs statistics
        $totalCallLogs = CallLog::count();
        $pendingCallLogs = CallLog::where('status', CallLog::STATUS_PENDING)->count();
        $recentCallLogs = CallLog::with(['client', 'employee.user'])->latest()->take(5)->get();

        // Tasks statistics
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', Task::STATUS_PENDING)->count();
        $inProgressTasks = Task::where('status', Task::STATUS_IN_PROGRESS)->count();
        $recentTasks = Task::with(['client', 'assignedTo'])->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalClients',
            'totalEmployees',
            'activeClients',
            'recentClients',
            'totalCallLogs',
            'pendingCallLogs',
            'recentCallLogs',
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'recentTasks'
        ));
    }

    /**
     * Employee Dashboard
     */
    public function employeeDashboard()
    {
        $employee = Auth::user()->employee;
        $accessibleClients = $employee->accessibleClients()->where('is_active', true)->get();

        // Employee-specific call logs and tasks
        $myCallLogs = CallLog::where('employee_id', $employee->id)->latest()->take(5)->get();
        $myTasks = Task::where('assigned_to', $employee->id)->latest()->take(5)->get();
        $myPendingTasks = Task::where('assigned_to', $employee->id)
            ->where('status', Task::STATUS_PENDING)
            ->count();

        return view('dashboard.employee', compact(
            'employee',
            'accessibleClients',
            'myCallLogs',
            'myTasks',
            'myPendingTasks'
        ));
    }

    /**
     * Client Dashboard
     */
    public function clientDashboard()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Client profile not found. Please contact support.');
        }

        // --- Fetch service statistics for the authenticated client ---
        $totalDocuments = $client->documents()->count();
        $recentDocuments = $client->documents()->latest()->limit(5)->get();
        $totalAssignedServices = $client->services()->count();
        $activeServices = $client->services()->wherePivot('status', 'active')->count();
        $inactiveServices = $client->services()->wherePivot('status', 'inactive')->count();
        $suspendedServices = $client->services()->wherePivot('status', 'suspended')->count();
        $expiredServices = $client->services()->wherePivot('status', 'expired')->count();

        // --- Fetch assigned employee details for the client ---
        // Since assignedEmployees() is a belongsToMany, we fetch the first one for the card display.
        $assignedEmployee = $client->assignedEmployees()->first(); // Get the first assigned employee or null

        // Eager load the service details if client->services is a pivot table
        $clientServices = $client->services()->orderBy('pivot_created_at', 'desc')->take(5)->get();

        // If a client can have multiple employees, fetch them all or limit to 5
        $clientEmployees = $client->assignedEmployees()->orderBy('pivot_access_granted_date', 'desc')->take(5)->get();

        // Note: The recent items (documents, images, forms, call logs, tasks) are fetched here,
        // but they are NOT used in the dashboard.client.blade.php according to your latest request.
        // They are kept here in case you decide to use them in other parts of the client portal later,
        // or for future expansion. If you are absolutely sure you won't use them anywhere else
        // for the client dashboard context, you can comment out or remove these lines to optimize.
        $recentDocuments = $client->documents()->latest()->take(5)->get();
        $recentImages = $client->images()->latest()->take(5)->get();
        $recentFormResponses = $client->formResponses()->latest()->take(5)->get();
        $recentCallLogs = $client->callLogs()->latest()->take(5)->get();
        $recentTasks = $client->tasks()->latest()->take(5)->get();

        return view('dashboard.client', compact(
            'totalDocuments',
            'recentDocuments',
            'totalAssignedServices',
            'activeServices',
            'inactiveServices',
            'suspendedServices',
            'expiredServices',
            'assignedEmployee', // Only these are used in the simplified blade
            'clientServices', // Pass services data
            'clientEmployees' // Pass employees data
            // 'recentDocuments', // Not used in simplified blade
            // 'recentImages',    // Not used in simplified blade
            // 'recentFormResponses', // Not used in simplified blade
            // 'recentCallLogs',  // Not used in simplified blade
            // 'recentTasks'      // Not used in simplified blade
        ));
    }
}
