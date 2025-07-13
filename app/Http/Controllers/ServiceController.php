<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::withCount('clients')
            ->orderBy('name')
            ->paginate(15);

        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.create');
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

            return redirect()->route('services.index')
                ->with('success', 'Service created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load('clients');
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'detail' => 'nullable|string|max:1000',
            'type' => 'required|integer|min:0|max:10',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Check if service has any clients
        if ($service->clients()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'Cannot delete service that is assigned to clients. Please remove all client assignments first.');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Toggle service active status
     */
    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Service {$status} successfully.");
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
