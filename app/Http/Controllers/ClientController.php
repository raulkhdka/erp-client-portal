<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\ClientPhone;
use App\Models\ClientEmail;
use App\Models\ClientEmployeeAccess;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::with(['user', 'phones', 'emails', 'services'])->paginate(15);
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        $services = Service::active()->orderBy('name')->get();
        return view('admin.clients.create', compact('employees', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'business_license' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'assigned_employees' => 'nullable|array',
            'assigned_employees.*' => 'exists:employees,id',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'nullable|string|max:20',
            'phones.*.type' => 'nullable|string|max:50',
            'emails' => 'nullable|array',
            'emails.*.email' => 'nullable|email',
            'emails.*.type' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_CLIENT,
            ]);

            // Create client
            $client = Client::create([
                'user_id' => $user->id,
                'name' => $request->client_name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'tax_id' => $request->tax_id,
                'business_license' => $request->business_license,
                'notes' => $request->notes,
            ]);

            // Assign employees if any
            if ($request->assigned_employees) {
                foreach ($request->assigned_employees as $employeeId) {
                    ClientEmployeeAccess::create([
                        'client_id' => $client->id,
                        'employee_id' => $employeeId,
                        'permissions' => ['view_basic_info'],
                        'access_granted_date' => now(),
                        'is_active' => true,
                    ]);
                }
            }

             // Assign services if any
             if ($request->services) {
                $client->services()->sync(array_map(function ($serviceId) {
                    return [
                        'service_id' => $serviceId,
                        'status' => 'active',
                        'description' => null, // Adjust based on your form input
                        'assigned_by' => Auth::id(), // Set the current admin/employee as assigner
                    ];
                }, $request->services));
            }

            // Create phone numbers
            if ($request->phones) {
                foreach ($request->phones as $phoneData) {
                    if (!empty($phoneData['phone'])) {
                        $client->phones()->create([
                            'phone' => $phoneData['phone'],
                            'type' => $phoneData['type'],
                        ]);
                    }
                }
            }

            // Create email addresses
            if ($request->emails) {
                foreach ($request->emails as $emailData) {
                    if (!empty($emailData['email'])) {
                        $client->emails()->create([
                            'email' => $emailData['email'],
                            'type' => $emailData['type'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create client: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::with(['user', 'phones', 'emails', 'services', 'documents', 'images', 'formResponses.dynamicForm'])
            ->findOrFail($id);
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::with(['user', 'phones', 'emails', 'assignedEmployees', 'services'])->findOrFail($id);
        $employees = Employee::with('user')->where('status', 'active')->get();
        $services = Service::active()->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'employees', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->user_id,
            'company_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'assigned_employees' => 'nullable|array',
            'assigned_employees.*' => 'exists:employees,id',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'nullable|string|max:20',
            'phones.*.type' => 'nullable|string|max:50',
            'emails' => 'nullable|array',
            'emails.*.email' => 'nullable|email',
            'emails.*.type' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $client->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update client
            $client->update([
                'company_name' => $request->company_name,
                'address' => $request->address,
                'tax_id' => $request->tax_id,
                'business_license' => $request->business_license,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Update services
            if ($request->has('services')) {
                $client->services()->sync(array_map(function ($serviceId) {
                    return [
                        'service_id' => $serviceId,
                        'status' => 'active',
                        'description' => null, // Adjust based on your form input
                        'assigned_by' => Auth::id(), // Set the current admin/employee as assigner
                    ];
                }, $request->services));
            } else {
                $client->services()->detach();
            }

            // Update employee assignments
            ClientEmployeeAccess::where('client_id', $client->id)->delete(); // Remove existing assignments
            if ($request->assigned_employees) {
                foreach ($request->assigned_employees as $employeeId) {
                    ClientEmployeeAccess::create([
                        'client_id' => $client->id,
                        'employee_id' => $employeeId,
                        'permissions' => ['view_basic_info'],
                        'access_granted_date' => now(),
                        'is_active' => true,
                    ]);
                }
            }

            // Update phone numbers
            $client->phones()->delete(); // Remove existing phones
            if ($request->phones) {
                foreach ($request->phones as $phoneData) {
                    if (!empty($phoneData['phone'])) {
                        $client->phones()->create([
                            'phone' => $phoneData['phone'],
                            'type' => $phoneData['type'],
                        ]);
                    }
                }
            }

            // Update email addresses
            $client->emails()->delete(); // Remove existing emails
            if ($request->emails) {
                foreach ($request->emails as $emailData) {
                    if (!empty($emailData['email'])) {
                        $client->emails()->create([
                            'email' => $emailData['email'],
                            'type' => $emailData['type'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update client: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);

        try {
            DB::beginTransaction();

            $client->user->delete(); // This will cascade delete the client

            DB::commit();
            return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete client: ' . $e->getMessage()]);
        }
    }

}
