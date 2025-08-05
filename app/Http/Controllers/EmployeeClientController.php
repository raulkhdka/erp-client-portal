<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class EmployeeClientController extends Controller
{
    /**
     * Display a listing of the employee's clients.
     */
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Employee profile not found. Please contact support.');
        }
        $query = $employee->accessibleClients()->with(['user', 'callLogs']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('client_id')) {
            $query->where('id', $request->client_id);
        }
        $clients = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        // Fetch accessible clients for the filter dropdown
        $accessibleClients = $employee->accessibleClients()->where('is_active', true)->get();
        return view('employees.clients.index', compact('clients', 'accessibleClients'));
    }

    /**
     * Display the specified client.
     */
    public function show($id)
    {
        $employee = Auth::user()->employee;
        if(!$employee) {
            return redirect()->route('login')->with('error', 'Employee profile not found. Please contact support.');
        }
        $client = $employee->accessibleClients()->with(['user', 'callLogs'])->findOrFail($id);
        return view('employees.clients.show', compact('client'));
    }
}
