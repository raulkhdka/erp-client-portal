@extends('layouts.app')

@section('title', 'Dynamic Form Details')

@section('breadcrumb')
    <a href="{{ route('dynamic-forms.index') }}">Dynamic Forms</a>
    <span class="breadcrumb-item active">{{ $form->name }}</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit Form
        </a>
        <a href="{{ route('dynamic-forms.public-show', $form->id) }}" target="_blank" class="btn btn-info">
            <i class="fas fa-external-link-alt me-2"></i>Public View
        </a>
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>
@endsection

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-info-circle me-2"></i>Form Details: {{ $form->name }}</h1>.app')

@section('title', 'Dynamic Form Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-info-circle me-2"></i>Form Details: {{ $form->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
        <a href="{{ route('dynamic-forms.edit', $form->id) }}" class="btn btn-warning text-white me-2">
            <i class="fas fa-edit me-2"></i>Edit Form
        </a>
        <a href="{{ route('dynamic-forms.public-show', $form->id) }}" target="_blank" class="btn btn-info me-2">
            <i class="fas fa-share-alt me-2"></i>View Public Form
        </a>
        <form action="{{ route('dynamic-forms.destroy', $form->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this form and all its associated fields and responses?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Delete Form
            </button>
        </form>
    </div>
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

<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $form->name }}</p>
                <p><strong>Description:</strong> {{ $form->description ?? 'N/A' }}</p>
                <p><strong>Status:</strong>
                    @if ($form->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
                <p><strong>Created At:</strong> {{ $form->created_at->format('M d, Y H:i A') }}</p>
                <p><strong>Last Updated:</strong> {{ $form->updated_at->format('M d, Y H:i A') }}</p>
                {{-- Add settings if you want to display them --}}
                {{-- <p><strong>Settings:</strong> {{ $form->settings ? json_encode($form->settings) : 'N/A' }}</p> --}}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Form Fields ({{ $form->fields->count() }})</h5>
            </div>
            <div class="card-body">
                @if ($form->fields->isEmpty())
                    <p class="text-muted">No fields defined for this form.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($form->fields as $field)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $field->sort_order }}. {{ $field->field_label }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Type: {{ ucfirst($field->field_type) }}
                                        @if ($field->is_required) | Required @endif
                                        @if ($field->field_options) | Options: {{ implode(', ', (array) $field->field_options) }} @endif
                                    </small>
                                </div>
                                <small><code>{{ $field->field_name }}</code></small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Form Responses ({{ $form->responses->count() }})</h5>
    </div>
    <div class="card-body">
        @if ($form->responses->isEmpty())
            <p class="text-muted">No responses received for this form yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Response ID</th>
                            <th>Submitted By (Client)</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->responses as $response)
                            <tr>
                                <td>{{ $response->id }}</td>
                                <td>
                                    @if ($response->client)
                                        <a href="{{ route('clients.show', $response->client->id) }}">{{ $response->client->user->name ?? $response->client->company_name }}</a>
                                    @else
                                        Guest
                                    @endif
                                </td>
                                <td>{{ $response->submitted_at->format('M d, Y H:i A') }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#responseModal{{ $response->id }}">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Response Modals --}}
            @foreach ($form->responses as $response)
                <div class="modal fade" id="responseModal{{ $response->id }}" tabindex="-1" aria-labelledby="responseModalLabel{{ $response->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="responseModalLabel{{ $response->id }}">Response #{{ $response->id }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Form:</strong> {{ $form->name }}</p>
                                <p><strong>Submitted By:</strong>
                                    @if ($response->client)
                                        {{ $response->client->user->name ?? $response->client->company_name }}
                                    @else
                                        Guest
                                    @endif
                                </p>
                                <p><strong>Submitted At:</strong> {{ $response->submitted_at->format('M d, Y H:i A') }}</p>
                                <hr>
                                <h6>Response Data:</h6>
                                @if ($response->response_data && is_array($response->response_data))
                                    <ul class="list-group">
                                        @foreach ($response->response_data as $key => $value)
                                            <li class="list-group-item">
                                                <strong>{{ $form->fields->firstWhere('field_name', $key)->field_label ?? Str::title(str_replace('_', ' ', $key)) }}:</strong>
                                                @if (is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @elseif (Str::startsWith($value, 'dynamic_form_uploads/'))
                                                    <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-sm btn-outline-primary">View File <i class="fas fa-download ms-1"></i></a>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No response data available.</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
