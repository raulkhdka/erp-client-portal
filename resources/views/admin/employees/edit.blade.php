@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@section('breadcrumb')
  <a href="{{ route('admin.employees.index') }}">Employees</a>
  <a href="{{ route('admin.employees.show', $employee->id) }}">{{ $employee->name }}</a>
  <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
  <div class="btn-group me-2">
    <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-outline-info">
      <i class="fas fa-eye me-2"></i>View Details
    </a>
  </div>
  <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>Back to Employees
  </a>
@endsection

@section('styles')
  <style>
    /* Page background */
    body {
      background: #f1f5f9; /* light grey background */
    }
    .modal-content {
      background: #f8fafc;
    }

    /* Modern card with performant hover + slightly grey background */
    .card-modern {
      position: relative;
      border: 1px solid #eef2f6;
      border-radius: 18px;
      box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
      overflow: hidden;
      transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
      will-change: transform, box-shadow;
      backface-visibility: hidden;
      background: #f8fafc; /* slightly grey form background */
    }
    .card-modern:hover,
    .card-modern:focus-within {
      transform: translateY(-2px);
      box-shadow: 0 16px 40px rgba(2, 6, 23, 0.08);
      border-color: #e3eaf2;
    }
    .card-modern .card-body {
      padding: 1.25rem;
    }
    @media (min-width: 992px) {
      .card-modern .card-body {
        padding: 1.5rem 1.5rem;
      }
    }
    .card-modern::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      border-radius: 18px;
    }
    .card-modern:hover::before,
    .card-modern:focus-within::before {
      box-shadow: inset 0 0 0 1px rgba(16,185,129,.15);
    }

    /* Independent scroll shell for the form area (desktop) */
    .form-shell {
      --stack-offset: 160px; /* adjust to your header/breadcrumb/actions total height if needed */
    }
    @media (min-width: 992px) {
      .form-col { min-height: 0; }
      .form-shell {
        height: calc(100dvh - var(--stack-offset));
        display: flex;
        min-height: 0; /* required for child overflow */
      }
      .form-scroll {
        flex: 1 1 auto;
        height: 100%;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 1rem;
        min-height: 0; /* required for overflow */
      }
    }

    /* Section title */
    .section-title {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 0.75rem;
    }
    .section-title .icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: #eafaf3;
      color: #10b981;
      display: grid;
      place-items: center;
    }
    .section-subtext {
      color: #6b7280;
      font-size: 0.9rem;
    }

    /* Inputs with icons and focus ring */
    .input-group-text {
      background: #f8fafc;
      border: 1px solid #eef2f6;
      color: #64748b;
      min-width: 42px;
      justify-content: center;
    }
    .form-control, .form-select, textarea.form-control {
      border-radius: 12px;
      border: 2px solid #eef2f6;
      background: #f8fafc;
      transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .form-control:focus, .form-select:focus, textarea.form-control:focus {
      border-color: #10b981;
      box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
      background: #fff;
    }

    /* Sub-cards inside form */
    .subcard {
      border: 1px dashed #e5e7eb;
      border-radius: 14px;
      padding: 1rem;
      background: #f8fafc; /* slightly grey background */
    }

    /* Buttons */
    .btn-ghost-danger {
      border: 1px solid #fee2e2;
      color: #dc2626;
      background: #f8fafc;
    }
    .btn-ghost-danger:hover { background: #ffeaea; }

    .btn-primary {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
    }

    /* Form width: a bit bigger, centered */
    .form-wrap {
      margin-inline: auto;
    }
    @media (min-width: 992px) {
      .form-col {
        /* 10/12 columns (83.3333%) - wider */
        flex: 0 0 auto;
        width: 83.333333%;
      }
    }

    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce) {
      .card-modern { transition: none; }
      .form-control, .form-select, textarea.form-control { transition: none; }
    }
  </style>
@endsection

@section('content')
<div class="container-fluid form-wrap">
  <div class="row g-4 justify-content-center">
    <div class="col-12 form-col">
      <div class="form-shell">
        <div class="form-scroll">
          <div class="card card-modern">
            <div class="card-body">
              <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}">
                @csrf
                @method('PUT')

                {{-- Employee Information --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-user-edit"></i></div>
                    <div>
                      <div>Employee Information</div>
                      <div class="section-subtext">Update the employee profile details.</div>
                    </div>
                  </div>

                  <div class="subcard">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                          <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $employee->name) }}" required placeholder="e.g. Jane Cooper">
                          @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-at"></i></span>
                          <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $employee->user->email) }}" required placeholder="e.g. jane@company.com">
                          @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-phone"></i></span>
                          <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                            id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required placeholder="e.g. +1234567890">
                          @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                          <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                            id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required placeholder="e.g. EMP123">
                          @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                          <input type="text" class="form-control @error('position') is-invalid @enderror"
                            id="position" name="position" value="{{ old('position', $employee->position) }}" required placeholder="e.g. Software Engineer">
                          @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-building"></i></span>
                          <select class="form-select @error('department') is-invalid @enderror" id="department" name="department">
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
                      </div>

                      <div class="col-md-6">
                        <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                            id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required placeholder="Select date" autocomplete="off">
                          <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                          @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="salary" class="form-label">Salary</label>
                        <div class="input-group">
                          <span class="input-group-text"><strong><span>&#x0930;&#x0941;</span></strong></span>
                          <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror"
                            id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" placeholder="0.00">
                          @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Permissions --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-user-shield"></i></div>
                    <div>Permissions</div>
                  </div>
                  <div class="subcard">
                    <p class="text-muted small mb-3">Select the permissions that this employee will have.</p>
                    <div class="row g-3">
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

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2">
                  <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-secondary">Cancel</a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Employee
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-format salary input
    var salaryInput = document.getElementById('salary');
    salaryInput.addEventListener('blur', function() {
      if (this.value) {
        this.value = parseFloat(this.value).toFixed(2);
      }
    });
  });
</script>
@endsection