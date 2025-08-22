<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\ClientEmployeeAccess;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with(['user', 'phones', 'assignedEmployees', 'services', 'emails']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('phones', function ($q) use ($search) {
                      $q->where('phone', 'like', "%{$search}%");
                  });
            });
        }

        $clients = $query->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'clients' => $clients,
                'pagination' => (string) $clients->appends(request()->query())->links()
            ]);
        }

        return view('admin.clients.index', compact('clients'));
    }

    public function export(Request $request)
    {
        $query = Client::with(['user', 'phones', 'emails', 'services', 'assignedEmployees']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('phones', function ($q) use ($search) {
                      $q->where('phone', 'like', "%{$search}%");
                  });
            });
        }

        $clients = $query->get();
        $pdf = Pdf::loadView('admin.clients.pdf', compact('clients'));
        return $pdf->download('clients.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Client::with(['user', 'phones', 'emails', 'services', 'assignedEmployees']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('phones', function ($q) use ($search) {
                      $q->where('phone', 'like', "%{$search}%");
                  });
            });
        }

        $clients = $query->get();
        return Excel::download(new ClientsExport($clients), 'clients.xlsx');
    }

    public function updateStatus(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found.'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $client->status = $request->status;
        $client->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully.'
        ]);
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
            'client_name' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*.email' => 'nullable|email',
            'emails.*.type' => 'nullable|string|max:50',
            'password' => 'required|min:8',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'business_license' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'assigned_employee' => 'nullable|exists:employees,id',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'nullable|string|max:20',
            'phones.*.type' => 'nullable|string|max:50',
        ]);

        // Validate that the first email is provided and unique
        if (empty($request->emails[0]['email']) || !filter_var($request->emails[0]['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'The first email address is required and must be a valid email.'], 422);
        }
        $userEmail = $request->emails[0]['email'];
        $user = User::where('email', $userEmail)->first();
        if ($user) {
            return response()->json(['error' => 'The first email address has already been taken.'], 422);
        }

        try {
            DB::beginTransaction();

            // Generate username from client_name
            $cleanName = str_replace(' ', '', $request->client_name);
            $username = substr($cleanName, 0, 5) . mt_rand(10, 99);

            // Create user with the first email
            $user = User::create([
                'name' => $username,
                'email' => $userEmail,
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

            // Create additional email addresses (skip the first email)
            if ($request->emails) {
                foreach ($request->emails as $index => $emailData) {
                    if ($index > 0 && !empty($emailData['email'])) {
                        $client->emails()->create([
                            'email' => $emailData['email'],
                            'type' => $emailData['type'] ?? 'primary',
                        ]);
                    }
                }
            }

            // Assign employee if selected
            if ($request->assigned_employee) {
                ClientEmployeeAccess::create([
                    'client_id' => $client->id,
                    'employee_id' => $request->assigned_employee,
                    'permissions' => ['view_basic_info'],
                    'access_granted_date' => now(),
                    'is_active' => true,
                ]);
            }

            // Assign services if any
            if ($request->services) {
                $client->services()->sync(array_map(function ($serviceId) {
                    return [
                        'service_id' => $serviceId,
                        'status' => 'active',
                        'description' => null,
                        'assigned_by' => Auth::id(),
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

            DB::commit();
            return response()->json(['message' => 'Client created successfully.', 'redirect' => route('admin.clients.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create client: ' . $e->getMessage()], 500);
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
            'client_name' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*.email' => 'nullable|email',
            'emails.*.type' => 'nullable|string|max:50',
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'business_license' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'assigned_employee' => 'nullable|exists:employees,id',
            'phones' => 'nullable|array',
            'phones.*.phone' => 'nullable|string|max:20',
            'phones.*.type' => 'nullable|string|max:50',
        ]);

        // Validate that the first email is provided and unique
        if (empty($request->emails[0]['email']) || !filter_var($request->emails[0]['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'The first email address is required and must be a valid email.'], 422);
        }
        $userEmail = $request->emails[0]['email'];
        $user = User::where('email', $userEmail)->where('id', '!=', $client->user_id)->first();
        if ($user) {
            return response()->json(['error' => 'The first email address has already been taken.'], 422);
        }

        try {
            DB::beginTransaction();

            // Update user (only email, username remains unchanged)
            $client->user->update([
                'email' => $userEmail,
            ]);

            // Update client
            $client->update([
                'name' => $request->client_name,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'tax_id' => $request->tax_id,
                'business_license' => $request->business_license,
                'notes' => $request->notes,
            ]);

            // Update additional email addresses (skip the first email)
            $client->emails()->delete();
            if ($request->emails) {
                foreach ($request->emails as $index => $emailData) {
                    if ($index > 0 && !empty($emailData['email'])) {
                        $client->emails()->create([
                            'email' => $emailData['email'],
                            'type' => $emailData['type'] ?? 'primary',
                        ]);
                    }
                }
            }

            // Update employee assignment
            ClientEmployeeAccess::where('client_id', $client->id)->delete();
            if ($request->assigned_employee) {
                ClientEmployeeAccess::create([
                    'client_id' => $client->id,
                    'employee_id' => $request->assigned_employee,
                    'permissions' => ['view_basic_info'],
                    'access_granted_date' => now(),
                    'is_active' => true,
                ]);
            }

            // Update services
            if ($request->has('services')) {
                $services = array_unique($request->services);
                $syncData = [];
                foreach ($services as $serviceId) {
                    $syncData[$serviceId] = [
                        'status' => 'active',
                        'description' => null,
                        'assigned_by' => Auth::id(),
                    ];
                }
                $client->services()->syncWithoutDetaching($syncData);
            } else {
                $client->services()->detach();
            }

            // Update phone numbers
            $client->phones()->delete();
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

            DB::commit();
            return response()->json(['message' => 'Client updated successfully.', 'redirect' => route('admin.clients.index')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update client: ' . $e->getMessage()], 500);
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

            $client->user->delete();

            DB::commit();
            return response()->json(['message' => 'Client deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to delete client: ' . $e->getMessage()], 500);
        }
    }
}