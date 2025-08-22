<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'employees' => $employees,
                'pagination' => (string) $employees->appends(request()->query())->links()
            ]);
        }

        return view('admin.employees.index', compact('employees'));
    }

    public function export(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->get();
        // Log::info('Employees for export:', $employees->toArray()); // Debug log
        $pdf = Pdf::loadView('admin.employees.pdf', compact('employees'));
        return $pdf->download('employees.pdf');
    }

    //Export to Excel
    public function exportExcel(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->get();
        return Excel::download(new EmployeesExport($employees), 'employees.xlsx');
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
        // Clean and transform hire_date before validation
        $cleanHireDate = (int) str_replace(['-', ' '], '', $request->hire_date);
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'employee_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'employee_id' => 'required|string|unique:employees',
            'department' => 'nullable|string',
            'position' => 'required|string',
            'phone' => 'nullable|string|max:15',
            'hire_date' => 'required|string',
            'salary' => 'nullable|numeric',
            'permissions' => 'nullable|array',
        ]);

        $validated['hire_date'] = $cleanHireDate;

        try {
            DB::beginTransaction();

            // Generate username from user_name
            $cleanName = str_replace(' ', '', $request->user_name);
            $username = substr($cleanName, 0, 5) . mt_rand(10, 99);

            // Create new user
            $user = User::create([
                'name' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($request->password),
                'role' => User::ROLE_EMPLOYEE,
            ]);

            // Create employee
            Employee::create([
                'user_id' => $user->id,
                'name' => $validated['employee_name'],
                'employee_id' => $validated['employee_id'],
                'department' => $validated['department'] ?? null,
                'position' => $validated['position'],
                'phone' => $validated['phone'] ?? null,
                'hire_date' => $validated['hire_date'],
                'salary' => $validated['salary'] ?? null,
                'status' => 'active',
                'permissions' => $validated['permissions'] ?? [],
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index')->with('status_update_success', 'Employee created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['status_update_error' => 'Failed to create employee: ' . $e->getMessage()]);
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'employee_id' => 'required|string|unique:employees,employee_id,' . $id,
            'department' => 'nullable|string',
            'position' => 'required|string',
            'phone' => 'nullable|string|max:15',
            'hire_date' => 'required|string', // not `date` because it's Nepali format
            'salary' => 'nullable|numeric',
            'status' => 'nullable|in:active,inactive,terminated',
            'permissions' => 'nullable|array',
        ]);

        // Clean hire_date (remove - and spaces)
        $validated['hire_date'] = (int) str_replace(['-', ' '], '', $validated['hire_date']);

        try {
            DB::beginTransaction();

            $employee->user->update([
                'email' => $validated['email'],
            ]);

            $employee->update([
                'employee_id' => $validated['employee_id'],
                'department' => $validated['department'] ?? null,
                'position' => $validated['position'],
                'phone' => $validated['phone'] ?? null,
                'hire_date' => $validated['hire_date'],
                'salary' => $validated['salary'] ?? null,
                'status' => $validated['status'] ?? $employee->status,
                'permissions' => $validated['permissions'] ?? [],
            ]);

            DB::commit();
            return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update employee: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the status of the specified employee.
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate the status input
            $request->validate([
                'status' => 'required|in:active,inactive,terminated'
            ]);

            $employee = Employee::findOrFail($id);
            $employee->status = $request->input('status');
            $employee->save();

            // Check for AJAX request using multiple methods
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Status updated to ' . ucfirst($employee->status),
                    'new_status' => $employee->status,
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('status_update_success', 'Status updated to ' . ucfirst($employee->status));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found.',
                ], 404);
            }

            return redirect()->back()->with('status_update_error', 'Employee not found.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid status provided.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->with('status_update_error', 'Invalid status provided.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update status. Please try again.',
                ], 500);
            }

            return redirect()->back()->with('status_update_error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            DB::beginTransaction();

            // Delete the associated user, which cascades to the employee
            $employee->user->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Failed to delete employee: ' . $e->getMessage()], 500);
        }
    }
}
