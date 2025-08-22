<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesExport;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::withCount('clients')->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(6);

        if ($request->ajax()) {
            return response()->json([
                'services' => $services,
                'pagination' => (string) $services->appends(request()->query())->links()
            ]);
        }

        return view('admin.services.index', compact('services'));
    }

    public function updateStatus(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $service->is_active = $request->status === 'active';
        $service->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully.'
        ]);
    }

    public function export(Request $request)
    {
        $query = Service::withCount('clients');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $services = $query->get();
        $pdf = Pdf::loadView('admin.services.pdf', compact('services'));
        return $pdf->download('services.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Service::withCount('clients');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $services = $query->get();
        return Excel::download(new ServicesExport($services), 'services.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name',
                'detail' => 'nullable|string|max:1000',
                'type' => 'required|integer|min:0|max:10',
                'is_active' => 'boolean'
            ]);

            $validated['is_active'] = $request->has('is_active');

            $service = Service::create($validated);

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'service' => $service,
                    'message' => 'Service created successfully.'
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('status_update_success', 'Service created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create service: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('status_update_error', 'Failed to create service: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // Eager load clients with their emails and user relationships
        $service->load('clients.emails', 'clients.user');
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:services,name,' . $service->id,
                'detail' => 'nullable|string|max:1000',
                'type' => 'required|integer|min:0|max:10',
                'is_active' => 'boolean'
            ]);

            $validated['is_active'] = $request->has('is_active');

            $service->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'service' => $service,
                    'message' => 'Service updated successfully.'
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('status_update_success', 'Service updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update service: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('status_update_error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Check if service has any clients
        if ($service->clients()->count() > 0) {
            return redirect()->route('admin.services.index')
                ->with('status_update_error', 'Cannot delete service that is assigned to clients. Please remove all client assignments first.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('status_update_success', 'Service deleted successfully.');
    }

    /**
     * Toggle service active status
     */
    public function toggleStatus(Service $service)
    {
        try {
            $service->update(['is_active' => !$service->is_active]);

            $status = $service->is_active ? 'activated' : 'deactivated';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Service {$status} successfully.",
                    'is_active' => $service->is_active
                ]);
            }

            return redirect()->back()
                ->with('status_update_success', "Service {$status} successfully.");
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to toggle service status: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('status_update_error', 'Failed to toggle service status: ' . $e->getMessage());
        }
    }
    // public function quickAdd(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'type' => 'required|integer|min:0|max:10',
    //         'detail' => 'nullable|string|max:1000',
    //         'is_active' => 'nullable|boolean',
    //     ]);

    //     $service = Service::create([
    //         'name' => $request->name,
    //         'type' => $request->type,
    //         'detail' => $request->detail,
    //         'is_active' => $request->boolean('is_active', true),
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'service' => $service,
    //     ]);
    // }
}
