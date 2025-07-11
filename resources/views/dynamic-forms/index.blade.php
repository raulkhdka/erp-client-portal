@extends('layouts.app')

@section('title', 'Dynamic Forms')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-clipboard-list me-2"></i>Dynamic Forms</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('dynamic-forms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Form
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($forms->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Form Name</th>
                        <th>Description</th>
                        <th>Fields</th>
                        <th>Status</th>
                        <th>Responses</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms as $form)
                    <tr>
                        <td>
                            <strong>{{ $form->name }}</strong>
                        </td>
                        <td>{{ $form->description ?? 'No description' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $form->fields->count() }} fields</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $form->is_active ? 'success' : 'secondary' }}">
                                {{ $form->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $form->responses->count() }} responses</td>
                        <td>{{ $form->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('dynamic-forms.show', $form->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('forms.show', $form->id) }}" class="btn btn-sm btn-outline-success" title="Public Link" target="_blank">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $forms->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No forms created yet</h4>
            <p class="text-muted">Create your first dynamic form to collect data from clients.</p>
            <a href="{{ route('dynamic-forms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create First Form
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
