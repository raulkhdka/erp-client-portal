@extends('layouts.app')

@section('title', 'Task Details')

@section('breadcrumb')
    <a href="{{ route('admin.tasks.index') }}">Tasks</a>
    <span class="breadcrumb-item active">Task #{{ $task->id }}</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit Task
        </a>
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="25%">Title:</th>
                                    <td><strong>{{ $task->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Client:</th>
                                    <td>
                                        <a href="{{ route('admin.clients.show', $task->client) }}">
                                            {{ $task->client->company_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Priority:</th>
                                    <td><span class="badge bg-{{ $task->priority_color }}">{{ ucfirst($task->priority) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span></td>
                                </tr>
                                <tr>
                                    <th>Assigned To:</th>
                                    <td>{{ $task->assignedTo->name }}</td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $task->adminCreator->name ?? 'Admin' }}</td>
                                </tr>

                                <tr>
                                    <th>Created:</th>
                                    <td>{!! $task->created_at_nepali_html !!}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            @if($task->callLog)
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-phone me-2"></i>Related Call Log
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Subject:</strong> {{ $task->callLog->subject }}</p>
                                        <p><strong>Date:</strong> {{ $task->callLog->call_date_formatted }}</p>
                                        <p><strong>Type:</strong>
                                            <span class="badge bg-{{ $task->callLog->call_type === 'incoming' ? 'success' : 'info' }}">
                                                {{ ucfirst($task->callLog->call_type) }}
                                            </span>
                                        </p>
                                        <a href="{{ route('admin.call-logs.show', $task->callLog) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View Call Log
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6>Description</h6>
                            <p class="text-muted">{{ $task->description }}</p>
                        </div>
                    </div>

                    @if($task->notes)
                        <div class="row">
                            <div class="col-12">
                                <h6>Notes</h6>
                                <p class="text-muted">{{ $task->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Status Update -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <form action="{{ route('admin.tasks.update', $task) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="status" class="form-label">Update Status</label>
                                        <select name="status" id="status" class="form-select">
                                            @foreach(\App\Models\Task::getStatusOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ $task->status == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="notes" class="form-label">Add Notes</label>
                                        <input type="text" name="notes" id="notes" class="form-control"
                                               placeholder="Add update notes...">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-1"></i>Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Time Tracking
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3 mb-2">
                                <h5 class="text-primary mb-1">{{ $task->estimated_hours ?? 0 }}</h5>
                                <small class="text-muted">Estimated Hours</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 mb-2">
                                <h5 class="text-info mb-1">{{ $task->actual_hours ?? 0 }}</h5>
                                <small class="text-muted">Actual Hours</small>
                            </div>
                        </div>
                    </div>

                    @if($task->started_at)
                        <p class="small text-muted mb-1">
                            <strong>Started:</strong> {{ $task->started_at->format('M d, Y H:i') }}
                        </p>
                    @endif

                    @if($task->completed_at)
                        <p class="small text-muted mb-0">
                            <strong>Completed:</strong> {{ $task->completed_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Task Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>Task ID:</strong> {{ $task->id }}
                    </p>
                    <p class="small text-muted mb-2">
                        <strong>Created:</strong> {{ $task->created_at->diffForHumans() }}
                    </p>
                    <p class="small text-muted mb-2">
                        <strong>Last Updated:</strong> {{ $task->updated_at->diffForHumans() }}
                    </p>
                    @if($task->callLog)
                        <p class="small text-muted mb-0">
                            <strong>Source:</strong> Auto-generated from call log
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
