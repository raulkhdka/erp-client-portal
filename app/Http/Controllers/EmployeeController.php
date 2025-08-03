<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name'=> 'required|string|max:255',
            'employee_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'employee_id' => 'required|string|unique:employees',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Create new user
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_EMPLOYEE,
            ]);

            // Create employee
            Employee::create([
                'user_id' => $user->id,
                'name' => $request->employee_name,
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'status' => 'active',
                'permissions' => $request->permissions ?? [],
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create employee: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['user', 'accessibleClients'])->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'employee_id' => 'required|string|unique:employees,employee_id,' . $id,
            'department' => 'nullable|string',
            'position' => 'required|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $employee->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update employee
            $employee->update([
                'employee_id' => $request->employee_id,
                'department' => $request->department,
                'position' => $request->position,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'status' => $request->status,
                'permissions' => $request->permissions ?? [],
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index', $employee->id)->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update employee: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);

        try {
            DB::beginTransaction();

            $employee->user->delete(); // This will cascade delete the employee

            DB::commit();
            return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete employee: ' . $e->getMessage()]);
        }
    }
}