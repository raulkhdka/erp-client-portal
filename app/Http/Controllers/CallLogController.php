<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Task;
use App\Services\ClientCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CallLogsExport;

class CallLogController extends Controller
{
    public function index(Request $request)
    {
        $query = CallLog::with(['client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }, 'employee', 'tasks.assignedTo'])
            ->orderBy('call_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('caller_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        if (Auth::user()->isEmployee()) {
            $query->where('employee_id', Auth::user()->employee->id);
        }

        $callLogs = $query->paginate(2);

        if ($request->ajax()) {
            return response()->json([
                'callLogs' => $callLogs,
                'pagination' => (string) $callLogs->appends(request()->query())->links()
            ]);
        }

        $clients = ClientCacheService::getClientsWithUser();
        return view('admin.call-logs.index', compact('callLogs', 'clients'));
    }

    public function create()
    {
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::with('user')->orderBy('id')->get();
        return view('admin.call-logs.create', compact('clients', 'employees'));
    }

    public function store(Request $request)
    {
        // Clean and transform dates before validation
        $cleanCallDate = (int) str_replace(['-', ' '], '', $request->call_date);
        $cleanFollowUpDate = $request->follow_up_date
            ? (int) str_replace(['-', ' '], '', $request->follow_up_date)
            : null;

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'call_type' => 'required|string|in:incoming,outgoing',
            'call_date' => 'required|string',
            'call_time' => 'required|date_format:H:i',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'duration_minutes' => 'nullable|integer|min:0',
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|string',
            'follow_up_time' => 'nullable|date_format:H:i',
            'create_task' => 'nullable|boolean',
            'employee_id' => 'nullable|exists:employees,id',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        // Assign cleaned dates
        $validated['call_date'] = $cleanCallDate;
        $validated['follow_up_date'] = $cleanFollowUpDate;

        if (Auth::user()->role === 'admin') {
            $validated['employee_id'] = $request->filled('employee_id') ? $request->employee_id : (Auth::user()->employee ? Auth::user()->employee->id : null);
        } else {
            $currentEmployee = Auth::user()->employee;
            if (!$currentEmployee) {
                return redirect()->back()->withErrors(['employee_id' => 'Your account is not linked to an employee record.']);
            }
            $validated['employee_id'] = $currentEmployee->id;
        }

        $validated['caller_phone'] = $request->input('caller_phone_select', $request->input('caller_phone'));

        DB::beginTransaction();
        try {
            $callLog = CallLog::create($validated);

            if ($request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED) {
                $assignedTo = $request->input('assigned_to') ?: $validated['employee_id'];
                if ($assignedTo && !Employee::find($assignedTo)) {
                    throw new \Exception('Invalid employee ID for task assignment.');
                }

                $createdBy = Auth::user()->employee ? Auth::user()->employee->id : Auth::user()->id;
                if (!$createdBy || (!Employee::find($createdBy) && !\App\Models\User::find($createdBy))) {
                    throw new \Exception('Invalid user/employee ID for created_by.');
                }

                Task::create([
                    'call_log_id' => $callLog->id,
                    'client_id' => $callLog->client_id,
                    'assigned_to' => $assignedTo,
                    'created_by' => $createdBy,
                    'title' => 'Follow-up on Call: ' . $callLog->subject,
                    'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                    'due_date' => $callLog->follow_up_date,
                    'status' => $callLog->status,
                    'priority' => $callLog->priority,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.call-logs.index')->with('status_update_success', 'Call log recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Call log creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('status_update_error', 'Failed to record call log: ' . $e->getMessage());
        }
    }

    public function show(CallLog $callLog)
    {
        $callLog->load(['client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }, 'employee', 'tasks.assignedTo']);
        return view('admin.call-logs.show', compact('callLog'));
    }

    public function edit(CallLog $callLog)
    {
        $callLog->load(['tasks', 'client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }]);
        $clients = ClientCacheService::getClientsWithUser();
        $employees = Employee::orderBy('id')->get();
        return view('admin.call-logs.edit', compact('callLog', 'clients', 'employees'));
    }

    public function update(Request $request, CallLog $callLog)
    {
        // Clean and transform dates before validation
        $cleanCallDate = (int) str_replace(['-', ' '], '', $request->call_date);
        $cleanFollowUpDate = $request->follow_up_date
            ? (int) str_replace(['-', ' '], '', $request->follow_up_date)
            : null;

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'call_type' => 'required|string|in:incoming,outgoing',
            'call_date' => 'required|string',
            'call_time' => 'required|date_format:H:i',
            'caller_name' => 'nullable|string|max:255',
            'caller_phone' => 'nullable|string|max:20',
            'duration_minutes' => 'nullable|integer|min:0',
            'subject' => 'required|string|max:255',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'status' => 'required|integer|in:1,2,3,4,5,6,7,8,9',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|string',
            'follow_up_time' => 'nullable|date_format:H:i',
            'create_task' => 'nullable|boolean',
            'employee_id' => 'nullable|exists:employees,id',
            'assigned_to' => 'nullable|exists:employees,id',
        ]);

        // Assign cleaned dates
        $validated['call_date'] = $cleanCallDate;
        $validated['follow_up_date'] = $cleanFollowUpDate;

        if (Auth::user()->role === 'admin') {
            $validated['employee_id'] = $request->filled('employee_id') ? $request->employee_id : (Auth::user()->employee ? Auth::user()->employee->id : null);
        } else {
            $currentEmployee = Auth::user()->employee;
            if (!$currentEmployee) {
                return redirect()->back()->withInput()->withErrors(['employee_id' => 'Your account is not linked to an employee record.']);
            }
            $validated['employee_id'] = $currentEmployee->id;
        }

        $newCallerPhone = $request->input('caller_phone_select', $request->input('caller_phone'));
        if (!empty($newCallerPhone) && $newCallerPhone !== $callLog->caller_phone) {
            $validated['caller_phone'] = $newCallerPhone;
        } else {
            $validated['caller_phone'] = $callLog->caller_phone;
        }

        DB::beginTransaction();
        try {
            $callLog->update($validated);

            $shouldCreateOrUpdateTask = $request->has('create_task') && $callLog->status != CallLog::STATUS_RESOLVED;
            $existingTask = $callLog->tasks()->first();

            if ($shouldCreateOrUpdateTask) {
                $assignedTo = $request->input('assigned_to') ?: $validated['employee_id'];
                if ($assignedTo && !Employee::find($assignedTo)) {
                    throw new \Exception('Invalid employee ID for task assignment.');
                }

                $createdBy = Auth::user()->employee ? Auth::user()->employee->id : Auth::user()->id;
                if (!$createdBy || (!Employee::find($createdBy) && !\App\Models\User::find($createdBy))) {
                    throw new \Exception('Invalid user/employee ID for created_by.');
                }

                if ($existingTask) {
                    $existingTask->update([
                        'assigned_to' => $assignedTo,
                        'title' => 'Follow-up on Call: ' . $callLog->subject,
                        'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                        'due_date' => $callLog->follow_up_date,
                        'status' => $callLog->status,
                        'priority' => $callLog->priority,
                        'client_id' => $callLog->client_id,
                    ]);
                } else {
                    Task::create([
                        'call_log_id' => $callLog->id,
                        'client_id' => $callLog->client_id,
                        'assigned_to' => $assignedTo,
                        'created_by' => $createdBy,
                        'title' => 'Follow-up on Call: ' . $callLog->subject,
                        'description' => $callLog->follow_up_required ?? 'No specific follow-up description.',
                        'due_date' => $callLog->follow_up_date,
                        'status' => $callLog->status,
                        'priority' => $callLog->priority,
                    ]);
                }
            } else {
                if ($existingTask) {
                    $existingTask->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.call-logs.index')->with('status_update_success', 'Call log updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Call log update failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('status_update_error', 'Failed to update call log: ' . $e->getMessage());
        }
    }

    public function destroy(CallLog $callLog)
    {
        try {
            $callLog->delete();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to delete call log: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, CallLog $callLog)
    {
        $validated = $request->validate([
            'status' => 'required|integer|min:1|max:9'
        ]);

        DB::beginTransaction();
        try {
            $callLog->update(['status' => $validated['status']]);

            if ($callLog->task) {
                $callLog->task->update(['status' => $validated['status']]);
            }

            DB::commit();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClientContacts(Client $client)
    {
        $client->load(['phones', 'user']);

        $contacts = [
            'primary_contact' => $client->name ?? $client->user->name ?? $client->contact_person ?? null,
            'phones' => $client->phones->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $phone->phone,
                    'type' => $phone->type,
                    'is_primary' => (bool)$phone->is_primary
                ];
            })->toArray()
        ];

        if (empty($contacts['phones']) && $client->main_phone_number) {
            $contacts['phones'][] = [
                'id' => null,
                'phone' => $client->main_phone_number,
                'type' => 'Main',
                'is_primary' => true
            ];
        }
        if (empty($contacts['phones']) && $client->secondary_phone_number) {
            $contacts['phones'][] = [
                'id' => null,
                'phone' => $client->secondary_phone_number,
                'type' => 'Secondary',
                'is_primary' => false
            ];
        }

        return response()->json($contacts);
    }

    public function history(Request $request)
    {
        $query = CallLog::query()->with(['client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }, 'employee']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('caller_name', 'like', '%' . $search . '%')
                  ->orWhere('caller_phone', 'like', '%' . $search . '%');
            });
        }

        if ($callerName = $request->query('caller_name')) {
            $query->where('caller_name', $callerName);
        }

        if ($callerPhone = $request->query('caller_phone')) {
            $query->where('caller_phone', $callerPhone);
        }

        $callLogs = $query->orderBy('call_date', 'desc')->paginate(10);

        $callerNames = CallLog::distinct()->pluck('caller_name')->filter()->sort()->values();
        $callerPhones = CallLog::distinct()->pluck('caller_phone')->filter()->sort()->values();

        $employees = Employee::select('id', 'name')->get();
        $clients = Client::select('id', 'name', 'company_name')->with('user')->get();

        return view('admin.call-logs.history', compact('callLogs', 'callerNames', 'callerPhones', 'employees', 'clients'));
    }

    public function export(Request $request)
    {
        $query = CallLog::with(['client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }, 'employee']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('caller_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                          ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if (Auth::user()->isEmployee()) {
            $query->where('employee_id', Auth::user()->employee->id);
        }

        $callLogs = $query->get();
        $pdf = Pdf::loadView('admin.call-logs.pdf', compact('callLogs'));
        return $pdf->download('call_logs.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = CallLog::with(['client' => function ($query) {
            $query->withDefault(['company_name' => 'N/A']);
        }, 'employee']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('caller_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                          ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if (Auth::user()->isEmployee()) {
            $query->where('employee_id', Auth::user()->employee->id);
        }

        $callLogs = $query->get();
        return Excel::download(new CallLogsExport($callLogs), 'call_logs.xlsx');
    }
}