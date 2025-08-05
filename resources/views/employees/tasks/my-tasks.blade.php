@extends('layouts.app')

@section('title', 'My Tasks')

@section('breadcrumb')
    <span class="breadcrumb-item active">My Tasks</span>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Tasks</h3>
                </div>

                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('status', 1)->count() }}</h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('status', 2)->count() }}</h4>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->where('is_overdue', true)->count() }}</h4>
                                    <small>Overdue</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $tasks->whereIn('status', [7, 8])->count() }}</h4>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($tasks->isEmpty())
                        <div class="alert alert-info">
                            No tasks assigned to you at the moment.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr class="{{ $task->is_overdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ Str::limit($task->title, 40) }}</strong>
                                                @if($task->is_overdue)
                                                    <br><small class="text-danger">⚠️ Overdue</small>
                                                @endif
                                            </td>
                                            <td>{{ $task->client->company_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $task->status_color }}">
                                                    {{ $task->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority_color }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    {{ \App\Helpers\DateHelper::formatAdToBs($task->due_date, 'M d, Y') }}
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('employees.tasks.show', $task) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection