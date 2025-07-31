@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->user->name)

@section('breadcrumb')
    <a href="{{ route('employees.index') }}">Employees</a>
    <a href="{{ route('employees.show', $employee->id) }}">{{ $employee->user->name }}</a>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
    <div class="btn-group me-2">
        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-outline-info">
            <i class="fas fa-eye me-2"></i>View Details
        </a>
    </div>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Employees
    </a>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $employee->user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $employee->user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Employee Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="employee_id" class="form-label">Employee ID *</label>
                            <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                                   id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                            @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position *</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                   id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                            @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-control @error('department') is-invalid @enderror" id="department" name="department">
                                <option value="">Select Department</option>
                                <option value="Accounting" {{ old('department', $employee->department) === 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                <option value="HR" {{ old('department', $employee->department) === 'HR' ? 'selected' : '' }}>Human Resources</option>
                                <option value="IT" {{ old('department', $employee->department) === 'IT' ? 'selected' : '' }}>Information Technology</option>
                                <option value="Sales" {{ old('department', $employee->department) === 'Sales' ? 'selected' : '' }}>Sales</option>
                                <option value="Marketing" {{ old('department', $employee->department) === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Operations" {{ old('department', $employee->department) === 'Operations' ? 'selected' : '' }}>Operations</option>
                                <option value="Finance" {{ old('department', $employee->department) === 'Finance' ? 'selected' : '' }}>Finance</option>
                            </select>
                            @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Employment Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hire_date" class="form-label">Hire Date *</label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                   id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
                            @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror"
                                   id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" placeholder="0.00">
                            @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Permissions</h5>
                            <div class="row">
                                @php
                                    $currentPermissions = old('permissions', $employee->permissions ?? []);
                                @endphp
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_accounting" id="view_accounting"
                                               {{ in_array('view_accounting', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="view_accounting">View Accounting</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="edit_payroll" id="edit_payroll"
                                               {{ in_array('edit_payroll', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_payroll">Edit Payroll</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_documents" id="view_documents"
                                               {{ in_array('view_documents', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="view_documents">View Documents</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_clients" id="manage_clients"
                                               {{ in_array('manage_clients', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_clients">Manage Clients</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_reports" id="view_reports"
                                               {{ in_array('view_reports', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="view_reports">View Reports</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="admin_access" id="admin_access"
                                               {{ in_array('admin_access', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="admin_access">Administrative Access</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Employee
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Employee Info -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Current Employee Info</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ $employee->user->name }}</h6>
                        <small class="text-muted">{{ $employee->position }}</small>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="h5 mb-0">{{ $employee->accessibleClients->count() }}</div>
                            <small class="text-muted">Clients</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="h5 mb-0">
                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                        <small class="text-muted">Status</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Guidelines -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Edit Guidelines</h6>
            </div>
            <div class="card-body">
                <h6>Important Notes</h6>
                <ul class="small text-muted mb-3">
                    <li>Changing email address will affect login credentials</li>
                    <li>Employee ID must remain unique across all employees</li>
                    <li>Status changes may affect system access</li>
                    <li>Salary information is optional and confidential</li>
                </ul>

                <h6>Required Fields</h6>
                <ul class="small text-muted mb-3">
                    <li>Full Name</li>
                    <li>Email Address</li>
                    <li>Employee ID</li>
                    <li>Position</li>
                    <li>Employment Status</li>
                    <li>Hire Date</li>
                </ul>

                <div class="alert alert-info small mb-0">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Tip:</strong> Use the status field to temporarily disable access without deleting the employee.
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Auto-format salary input
document.getElementById('salary').addEventListener('blur', function() {
    if (this.value) {
        this.value = parseFloat(this.value).toFixed(2);
    }
});

// Confirm status change to terminated
document.getElementById('status').addEventListener('change', function() {
    if (this.value === 'terminated') {
        if (!confirm('Are you sure you want to set this employee\'s status to terminated? This will revoke their system access.')) {
            this.value = '{{ $employee->status }}'; // Reset to original value
        }
    }
});
</script>
@endsection
@endsection
