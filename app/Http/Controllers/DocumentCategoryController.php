<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DocumentCategory::withCount('documents')
                                    ->ordered()
                                    ->get();


        return view('admin.document-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.document-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store request Data:', $request->all());
       $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:document_categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $category = DocumentCategory::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'icon' => $validatedData['icon'],
                'color' => $validatedData['color'],
                'sort_order' => $validatedData['sort_order'],
                'is_active' => $request->has('is_active'), // Default to true if not set
            ]);

            if ($category) {
                return redirect()->route('admin.document-categories.index')
                                ->with('success', 'Document category created successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }

        return redirect()->back()->withInput()->with('error', 'Unknown error occurred.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentCategory $documentCategory)
    {
        $documentCategory->load(['documents.uploader', 'documents.client']);

        return view('admin.document-categories.show', compact('documentCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentCategory $documentCategory)
    {
        return view('admin.document-categories.edit', compact('documentCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentCategory $documentCategory)
    {
        Log::info('Update request data:', $request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:document_categories,name,' . $documentCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $documentCategory->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'icon' => $validatedData['icon'],
                'color' => $validatedData['color'],
                'sort_order' => $validatedData['sort_order'],
                'is_active' => $validatedData['is_active'] ?? $documentCategory->is_active,
            ]);

            return redirect()->route('admin.document-categories.index')
                            ->with('success', 'Document category updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentCategory $documentCategory)
    {
        // Check if category has documents
        if ($documentCategory->documents()->count() > 0) {
            return redirect()->route('admin.document-categories.index')
                           ->with('error', 'Cannot delete category that contains documents.');
        }

        $documentCategory->delete();

        return redirect()->route('admin.document-categories.index')
                        ->with('success', 'Document category deleted successfully!');
    }
}
