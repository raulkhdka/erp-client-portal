@extends('layouts.app')

@section('title', 'Employee Details - ' . $employee->user->name)

@section('breadcrumb')
    <a href="{{ route('employees.index') }}">Employees</a>
    <span class="breadcrumb-item active">{{ $employee->user->name }}</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Delete
        </button>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Employees
    </a>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Full Name:</strong>
                        <p class="text-muted">{{ $employee->user->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email Address:</strong>
                        <p class="text-muted">
                            <a href="mailto:{{ $employee->user->email }}">{{ $employee->user->email }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Employment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Employee ID:</strong>
                        <p class="text-muted">{{ $employee->employee_id }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Position:</strong>
                        <p class="text-muted">{{ $employee->position }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Department:</strong>
                        <p class="text-muted">{{ $employee->department ?: 'Not assigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="text-muted">
                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Hire Date:</strong>
                        <p class="text-muted">{{ $employee->hire_date->format('F j, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Salary:</strong>
                        <p class="text-muted">
                            @if($employee->salary)
                                ${{ number_format($employee->salary, 2) }}
                            @else
                                Not disclosed
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Permissions</h5>
            </div>
            <div class="card-body">
                @if($employee->permissions && count($employee->permissions) > 0)
                    <div class="row">
                        @foreach($employee->permissions as $permission)
                            <div class="col-md-6 mb-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No specific permissions assigned</p>
                @endif
            </div>
        </div>

        <!-- Client Access -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Client Access</h5>
            </div>
            <div class="card-body">
                @if($employee->accessibleClients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Company</th>
                                    <th>Access Granted</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->accessibleClients as $client)
                                    <tr>
                                        <td>
                                            <a href="{{ route('clients.show', $client->id) }}" class="text-decoration-none">
                                                {{ $client->user->name }}
                                            </a>
                                        </td>
                                        <td>{{ $client->company_name ?: '-' }}</td>
                                        <td>{{ $client->pivot->access_granted_date ? \Carbon\Carbon::parse($client->pivot->access_granted_date)->format('M j, Y') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $client->pivot->is_active ? 'success' : 'secondary' }}">
                                                {{ $client->pivot->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $client->pivot->access_expires_date ? \Carbon\Carbon::parse($client->pivot->access_expires_date)->format('M j, Y') : 'No expiration' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No client access assigned</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Activity Summary -->
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Activity Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Accessible Clients:</span>
                    <span class="badge bg-primary">{{ $employee->accessibleClients->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Active Since:</span>
                    <span class="text-muted">{{ $employee->hire_date->diffForHumans() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Account Created:</span>
                    <span class="text-muted">{{ $employee->user->created_at->format('M j, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Last Updated:</span>
                    <span class="text-muted">{{ $employee->updated_at->format('M j, Y') }}</span>
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
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Employee
                    </a>
                    <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <i class="fas fa-toggle-on me-2"></i>Change Status
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-history me-2"></i>View Activity Log
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $employee->user->name }}</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>This action cannot be undone and will also delete their user account.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Employee
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Change Employee Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $employee->user->name }}">
                <input type="hidden" name="email" value="{{ $employee->user->email }}">
                <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                <input type="hidden" name="department" value="{{ $employee->department }}">
                <input type="hidden" name="position" value="{{ $employee->position }}">
                <input type="hidden" name="hire_date" value="{{ $employee->hire_date->format('Y-m-d') }}">
                <input type="hidden" name="salary" value="{{ $employee->salary }}">
                @if($employee->permissions)
                    @foreach($employee->permissions as $permission)
                        <input type="hidden" name="permissions[]" value="{{ $permission }}">
                    @endforeach
                @endif

                <div class="modal-body">
                    <p>Current status: <span class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'warning' : 'danger') }}">{{ ucfirst($employee->status) }}</span></p>

                    <label for="status" class="form-label">New Status:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ $employee->status === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
