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

@section('styles')
  {{-- Tom Select CSS --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">

  <style>
    /* Page background */
    body {
      background: #f1f5f9; /* light grey background */
    }
    .modal-content{
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

    /* Password strength */
    .strength {
      height: 8px;
      border-radius: 999px;
      overflow: hidden;
      background: #e5e7eb;
    }
    .strength-bar { height: 100%; width: 0%; transition: width .25s ease; }
    .strength-weak { background: #ef4444; }
    .strength-mid  { background: #f59e0b; }
    .strength-strong { background: #10b981; }

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

    /* Tom Select theming to match cards/inputs */
    .ts-wrapper.form-select .ts-control,
    .ts-wrapper .ts-control {
      border-radius: 12px;
      border: 2px solid #eef2f6;
      background: #f8fafc;
      min-height: calc(1.5em + .75rem + 2px);
      padding-block: .25rem;
      padding-inline: .5rem;
    }
    .ts-wrapper.single.input-active .ts-control,
    .ts-wrapper.multi.input-active .ts-control,
    .ts-wrapper .ts-control:focus {
      border-color: #10b981;
      box-shadow: 0 0 0 .2rem rgba(16,185,129,.15);
    }
    .ts-dropdown {
      border: 2px solid #eef2f6;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
      overflow: hidden;
      background: #f8fafc;
    }
    .ts-dropdown .active {
      background: #f0fdf4;
      color: #065f46;
    }
    .ts-control .item {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      color: #065f46;
      border-radius: 10px;
      padding: .25rem .5rem;
      margin: .125rem .125rem;
    }
    .ts-control .remove {
      color: #047857;
      opacity: .8;
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
              <form method="POST" action="{{ route('admin.employees.store') }}">
                @csrf

                {{-- Employee Creation --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-user-plus"></i></div>
                    <div>
                      <div>Employee Creation</div>
                      <div class="section-subtext">Create the employee profile and login credentials.</div>
                    </div>
                  </div>

                  <div class="subcard">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="employee_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                          <input type="text" class="form-control @error('employee_name') is-invalid @enderror"
                            id="employee_name" name="employee_name" value="{{ old('employee_name') }}" required placeholder="e.g. Jane Cooper">
                          @error('employee_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="user_name" class="form-label">Username (Auto-generated)</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-user"></i></span>
                          <input type="text" class="form-control" id="user_name" name="user_name" readonly placeholder="Will generate from name">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-at"></i></span>
                          <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required placeholder="e.g. jane@company.com">
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
                            id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. +1234567890">
                          @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-lock"></i></span>
                          <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required placeholder="Choose a secure password">
                          <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button class="btn btn-outline-primary" type="button" id="generatePassword" tabindex="-1" title="Generate Random Password">
                            <i class="fas fa-magic"></i>
                          </button>
                          @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2">
                          <div class="strength flex-grow-1" aria-hidden="true">
                            <div id="passwordStrengthBar" class="strength-bar"></div>
                          </div>
                          <div id="passwordStrengthText" class="text-muted">Strength</div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                          <input type="text" class="form-control @error('employee_id') is-invalid @enderror"
                            id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required placeholder="e.g. EMP123">
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
                            id="position" name="position" value="{{ old('position') }}" required placeholder="e.g. Software Engineer">
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
                            <option value="Accounting" {{ old('department') === 'Accounting' ? 'selected' : '' }}>Accounting</option>
                            <option value="HR" {{ old('department') === 'HR' ? 'selected' : '' }}>Human Resources</option>
                            <option value="IT" {{ old('department') === 'IT' ? 'selected' : '' }}>Information Technology</option>
                            <option value="Sales" {{ old('department') === 'Sales' ? 'selected' : '' }}>Sales</option>
                            <option value="Marketing" {{ old('department') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Operations" {{ old('department') === 'Operations' ? 'selected' : '' }}>Operations</option>
                            <option value="Finance" {{ old('department') === 'Finance' ? 'selected' : '' }}>Finance</option>
                          </select>
                          @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <input type="text" class="form-control nepali-date @error('hire_date') is-invalid @enderror"
                            id="hire_date" name="hire_date" data-mode="dark" value="{{ old('hire_date') }}" required placeholder="Select date" autocomplete="off">
                          <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                          @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="salary" class="form-label">Salary</label>
                        <div class="input-group">
                          <span class="input-group-text"><span>&#x0930;&#x0941;</span></span>
                          <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror"
                            id="salary" name="salary" value="{{ old('salary') }}" placeholder="0.00">
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
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="view_accounting" id="view_accounting">
                          <label class="form-check-label" for="view_accounting">View Accounting</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="edit_payroll" id="edit_payroll">
                          <label class="form-check-label" for="edit_payroll">Edit Payroll</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="view_documents" id="view_documents">
                          <label class="form-check-label" for="view_documents">View Documents</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_clients" id="manage_clients">
                          <label class="form-check-label" for="manage_clients">Manage Clients</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="view_reports" id="view_reports">
                          <label class="form-check-label" for="view_reports">View Reports</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="admin_access" id="admin_access">
                          <label class="form-check-label" for="admin_access">Administrative Access</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2">
                  <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Employee
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

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Username generation
      var employeeNameInput = document.getElementById('employee_name');
      var usernameInput = document.getElementById('user_name');

      function generateUsername() {
        var name = employeeNameInput.value;
        if (name) {
          var cleanName = name.replace(/\s/g, '');
          var baseName = cleanName.substring(0, 5);
          var randomNumbers = Math.floor(Math.random() * 90 + 10); // 2-digit
          usernameInput.value = baseName + randomNumbers;
        } else {
          usernameInput.value = '';
        }
      }

      employeeNameInput.addEventListener('input', generateUsername);
      employeeNameInput.addEventListener('change', generateUsername);

      // Password show/hide and generator
      var passwordInput = document.getElementById('password');
      var togglePasswordBtn = document.getElementById('togglePassword');
      var generatePasswordBtn = document.getElementById('generatePassword');
      var strengthBar = document.getElementById('passwordStrengthBar');
      var strengthText = document.getElementById('passwordStrengthText');

      function togglePassword() {
        var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        togglePasswordBtn.innerHTML = type === 'password'
          ? '<i class="fas fa-eye"></i>'
          : '<i class="fas fa-eye-slash"></i>';
      }

      function generateStrongPassword() {
        var length = 12;
        var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{};:,.?';
        var pwd = '';
        for (var i = 0; i < length; i++) {
          pwd += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordInput.value = pwd;
        passwordInput.dispatchEvent(new Event('input'));
      }

      function calcStrength(value) {
        var score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        return score; // 0..5
      }

      function updateStrength() {
        var v = passwordInput.value || '';
        var score = calcStrength(v);
        var pct = 0, cls = 'strength-weak', label = 'Weak';
        if (score <= 2) { pct = 30; cls = 'strength-weak'; label = 'Weak'; }
        else if (score === 3) { pct = 60; cls = 'strength-mid'; label = 'Medium'; }
        else if (score >= 4) { pct = 100; cls = 'strength-strong'; label = 'Strong'; }
        strengthBar.className = 'strength-bar ' + cls;
        strengthBar.style.width = pct + '%';
        strengthText.textContent = label;
      }

      togglePasswordBtn.addEventListener('click', togglePassword);
      generatePasswordBtn.addEventListener('click', generateStrongPassword);
      passwordInput.addEventListener('input', updateStrength);
      updateStrength();

      // Generate username on page load (e.g., after validation errors)
      generateUsername();
    });

    // document.querySelectorAll('.hire-select').forEach(select => {
    //                 if (!select.tomselect) {
    //                     new TomSelect(select, {
    //                         create: false,
    //                         placeholder: 'Select date',
    //                         allowEmptyOption: true,
    //                         render: {
    //                             option: function(item, escape) {
    //                                 return '<div>' + escape(item.text) + '</div>';
    //                             },
    //                             item: function(item, escape) {
    //                                 return '<div>' + escape(item.text) + '</div>';
    //                             }
    //                         }
    //                     });
    //                 }
    //             });

  </script>
@endpush