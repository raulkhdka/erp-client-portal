@extends('layouts.app')

@section('title', 'Dynamic Form - ' . $form->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-clipboard-list me-2"></i>{{ $form->name }}
        <span class="badge bg-{{ $form->is_active ? 'success' : 'secondary' }} ms-2">
            {{ $form->is_active ? 'Active' : 'Inactive' }}
        </span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                <i class="fas fa-share me-2"></i>Share
            </button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </div>
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Form Information -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Form Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Form Name:</strong>
                        <p class="text-muted">{{ $form->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="text-muted">
                            <span class="badge bg-{{ $form->is_active ? 'success' : 'secondary' }}">
                                {{ $form->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-12">
                        <strong>Description:</strong>
                        <p class="text-muted">{{ $form->description ?: 'No description provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong>
                        <p class="text-muted">{{ $form->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Last Updated:</strong>
                        <p class="text-muted">{{ $form->updated_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Fields -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Form Fields ({{ $form->fields->count() }})</h5>
            </div>
            <div class="card-body">
                @if($form->fields->count() > 0)
                    <div class="row">
                        @foreach($form->fields as $index => $field)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $field->field_label }}</h6>
                                            @if($field->is_required)
                                                <span class="badge bg-danger">Required</span>
                                            @endif
                                        </div>
                                        <p class="small text-muted mb-1">
                                            <strong>Name:</strong> {{ $field->field_name }}
                                        </p>
                                        <p class="small text-muted mb-1">
                                            <strong>Type:</strong>
                                            <span class="badge bg-secondary">{{ ucfirst($field->field_type) }}</span>
                                        </p>
                                        @if($field->field_options)
                                            <p class="small text-muted mb-0">
                                                <strong>Options:</strong> {{ implode(', ', json_decode($field->field_options, true) ?? []) }}
                                            </p>
                                        @endif
                                        <p class="small text-muted mb-0">
                                            <strong>Order:</strong> {{ $field->field_order }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No fields defined for this form.</p>
                @endif
            </div>
        </div>

        <!-- Form Preview -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Form Preview</h5>
            </div>
            <div class="card-body">
                <form class="preview-form">
                    @foreach($form->fields->sortBy('field_order') as $field)
                        <div class="mb-3">
                            <label class="form-label">
                                {{ $field->field_label }}
                                @if($field->is_required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @switch($field->field_type)
                                @case('text')
                                @case('email')
                                @case('number')
                                @case('date')
                                    <input type="{{ $field->field_type }}" class="form-control" disabled placeholder="Sample {{ $field->field_type }} input">
                                    @break

                                @case('textarea')
                                    <textarea class="form-control" rows="3" disabled placeholder="Sample textarea input"></textarea>
                                    @break

                                @case('select')
                                    <select class="form-control" disabled>
                                        <option>-- Select an option --</option>
                                        @if($field->field_options)
                                            @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                                <option>{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @break

                                @case('checkbox')
                                    @if($field->field_options)
                                        @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled>
                                                <label class="form-check-label">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @break

                                @case('radio')
                                    @if($field->field_options)
                                        @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="{{ $field->field_name }}_preview" disabled>
                                                <label class="form-check-label">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                    @break

                                @case('file')
                                    <input type="file" class="form-control" disabled>
                                    @break
                            @endswitch
                        </div>
                    @endforeach

                    <button type="button" class="btn btn-primary" disabled>Submit (Preview)</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Form Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Form Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total Fields:</span>
                    <span class="badge bg-primary">{{ $form->fields->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Required Fields:</span>
                    <span class="badge bg-danger">{{ $form->fields->where('is_required', true)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total Responses:</span>
                    <span class="badge bg-success">{{ $form->responses->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Form Status:</span>
                    <span class="badge bg-{{ $form->is_active ? 'success' : 'secondary' }}">
                        {{ $form->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Form
                    </a>
                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fas fa-share me-2"></i>Share Form
                    </button>
                    <a href="{{ route('dynamic-forms.public', $form->id) }}" target="_blank" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-external-link-alt me-2"></i>View Public Form
                    </a>
                    <button type="button" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-download me-2"></i>Export Responses
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Share this form with clients using the public link:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="publicUrl" value="{{ route('dynamic-forms.public', $form->id) }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="copyButton">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    This link allows anyone to fill out and submit the form.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the form <strong>{{ $form->name }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone and will also delete all responses.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('dynamic-forms.destroy', $form->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Form
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Copy URL functionality
document.getElementById('copyButton').addEventListener('click', function() {
    const urlInput = document.getElementById('publicUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(urlInput.value).then(function() {
        const button = document.getElementById('copyButton');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    });
});
</script>
@endsection
@endsection
