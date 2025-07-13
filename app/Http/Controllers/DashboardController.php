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
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $totalClients = Client::count();
        $totalEmployees = Employee::count();
        $activeClients = Client::where('status', 'active')->count();
        $recentClients = Client::with('user')->latest()->take(5)->get();

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
            'totalClients', 'totalEmployees', 'activeClients', 'recentClients',
            'totalCallLogs', 'pendingCallLogs', 'recentCallLogs',
            'totalTasks', 'pendingTasks', 'inProgressTasks', 'recentTasks'
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
            'employee', 'accessibleClients', 'myCallLogs', 'myTasks', 'myPendingTasks'
        ));
    }

    /**
     * Client Dashboard
     */
    public function clientDashboard()
    {
        $client = Auth::user()->client;
        $documents = $client->documents()->latest()->take(5)->get();
        $images = $client->images()->latest()->take(5)->get();
        $formResponses = $client->formResponses()->with('dynamicForm')->latest()->take(5)->get();

        // Client-specific call logs and tasks
        $myCallLogs = CallLog::where('client_id', $client->id)->latest()->take(5)->get();
        $myTasks = Task::where('client_id', $client->id)->latest()->take(5)->get();

        return view('dashboard.client', compact(
            'client', 'documents', 'images', 'formResponses', 'myCallLogs', 'myTasks'
        ));
    }
}
