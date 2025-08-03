@extends('layouts.app')

@section('title', 'Add New Employee')

@section('breadcrumb')
    <a href="{{ route('admin.employees.index') }}">Employees</a>
    <span class="breadcrumb-item active">Add New Employee</span>
@endsection

@section('actions')
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Employees
    </a>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employees.store') }}">
                        @csrf

                        <!-- User Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Employee User Creation</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">User Name *</label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                    id="user_name" name="user_name" value="{{ old('user_name') }}" placeholder="User name" required>
                                @error('user_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" placeholder="Enter your Email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Enter Password" required>
                                @error('password')
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
                                <label for="employee_name" class="form-label">Employee Name *</label>
                                <input type="text" class="form-control @error('employee_name') is-invalid @enderror"
                                    id="employee_name" name="employee_name" value="{{ old('employee_name') }}" placeholder="Full Name">
                                @error('employee_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Employee ID *</label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                                    id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position *</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    id="position" name="position" value="{{ old('position') }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-control @error('department') is-invalid @enderror" id="department"
                                    name="department">
                                    <option value="">Select Department</option>
                                    <option value="Accounting" {{ old('department') === 'Accounting' ? 'selected' : '' }}>
                                        Accounting</option>
                                    <option value="HR" {{ old('department') === 'HR' ? 'selected' : '' }}>Human
                                        Resources</option>
                                    <option value="IT" {{ old('department') === 'IT' ? 'selected' : '' }}>Information
                                        Technology</option>
                                    <option value="Sales" {{ old('department') === 'Sales' ? 'selected' : '' }}>Sales
                                    </option>
                                    <option value="Marketing" {{ old('department') === 'Marketing' ? 'selected' : '' }}>
                                        Marketing</option>
                                    <option value="Operations" {{ old('department') === 'Operations' ? 'selected' : '' }}>
                                        Operations</option>
                                    <option value="Finance" {{ old('department') === 'Finance' ? 'selected' : '' }}>Finance
                                    </option>
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="hire_date" class="form-label">Hire Date *</label>
                                <div class="input-group">
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                        id="hire_date" name="hire_date" value="{{ old('hire_date') }}" required
                                        placeholder="Select date" autocomplete="off">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Salary</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('salary') is-invalid @enderror" id="salary"
                                    name="salary" value="{{ old('salary') }}" placeholder="0.00">
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
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="view_accounting" id="view_accounting">
                                            <label class="form-check-label" for="view_accounting">View Accounting</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="edit_payroll" id="edit_payroll">
                                            <label class="form-check-label" for="edit_payroll">Edit Payroll</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="view_documents" id="view_documents">
                                            <label class="form-check-label" for="view_documents">View Documents</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="manage_clients" id="manage_clients">
                                            <label class="form-check-label" for="manage_clients">Manage Clients</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="view_reports" id="view_reports">
                                            <label class="form-check-label" for="view_reports">View Reports</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="admin_access" id="admin_access">
                                            <label class="form-check-label" for="admin_access">Administrative
                                                Access</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Employee
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
                </div>
                <div class="card-body">
                    <h6>Creating a New Employee</h6>
                    <p class="small text-muted mb-3">
                        Fill out this form to add a new employee to the system. The employee will receive login credentials
                        and can access assigned client data based on their permissions. Only admins can create employees.
                    </p>

                    <h6>Required Fields</h6>
                    <ul class="small text-muted mb-3">
                        <li>Full Name</li>
                        <li>Email Address</li>
                        <li>Password</li>
                        <li>Employee ID</li>
                        <li>Position</li>
                        <li>Hire Date</li>
                    </ul>

                    <h6>Permissions</h6>
                    <p class="small text-muted">
                        Select the permissions that this employee will have. You can modify these later.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
