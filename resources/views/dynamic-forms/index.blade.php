@extends('layouts.app')

@section('title', 'Dynamic Forms')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-file-alt me-2"></i>Dynamic Forms</h1>
    <a href="{{ route('dynamic-forms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle me-2"></i>Create New Form
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Form Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Fields</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($forms as $form)
                    <tr>
                        <td>{{ $form->id }}</td>
                        <td>{{ $form->name }}</td>
                        <td>{{ Str::limit($form->description, 50) ?? 'N/A' }}</td>
                        <td>
                            @if ($form->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $form->fields->count() }}</td>
                        <td>{{ $form->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('dynamic-forms.show', $form->id) }}" class="btn btn-info btn-sm" title="View Form">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-warning btn-sm text-white" title="Edit Form">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('dynamic-forms.destroy', $form->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this form and all its associated fields and responses?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Form">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('dynamic-forms.public-show', $form->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="View Public Form">
                                <i class="fas fa-link"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No dynamic forms found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $forms->links() }}
        </div>
    </div>
</div>
@endsection