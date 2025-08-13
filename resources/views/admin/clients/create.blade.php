@extends('layouts.app')

@section('title', 'Add New Client')

@section('breadcrumb')
  <a href="{{ route('admin.clients.index') }}">Clients</a>
  <span class="breadcrumb-item active">Add New Client</span>
@endsection

@section('actions')
  <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>Back to Clients
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
      border: none;
      outline: none;
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
              <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf

                {{-- Client Creation --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-user-plus"></i></div>
                    <div>
                      <div>Client Creation</div>
                      <div class="section-subtext">Create the client profile and login credentials.</div>
                    </div>
                  </div>

                  <div class="subcard">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="client_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                          <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                            id="client_name" name="client_name" value="{{ old('client_name') }}" required placeholder="e.g. Jane Cooper">
                          @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="username" class="form-label">Username (Auto-generated)</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-user"></i></span>
                          <input type="text" class="form-control" id="username" readonly placeholder="Will generate from name">
                        </div>
                      </div>

                      {{-- Adjust these to change relative widths (e.g., 6/6 or 5/7) --}}
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
                          <small id="passwordStrengthText" class="text-muted">Strength</small>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-building"></i></span>
                          <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                            id="company_name" name="company_name" value="{{ old('company_name') }}" required placeholder="e.g. Acme Inc.">
                          @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="tax_id" class="form-label">Tax ID</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></span>
                          <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                            id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Optional">
                          @error('tax_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-md-6">
                        <label for="business_license" class="form-label">Business License</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                          <input type="text" class="form-control @error('business_license') is-invalid @enderror"
                            id="business_license" name="business_license" value="{{ old('business_license') }}" placeholder="Optional">
                          @error('business_license')
                            <div class="invalid-feedback">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>

                      <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                          <textarea class="form-control @error('address') is-invalid @enderror"
                            id="address" name="address" rows="3" placeholder="Street, City, State, ZIP">{{ old('address') }}</textarea>
                          @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Email Addresses --}}
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">
                      <div class="icon"><i class="fas fa-envelope"></i></div>
                      <div>Email Addresses</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addEmail">
                      <i class="fas fa-plus me-1"></i>Add Email
                    </button>
                  </div>

                  <div id="emailContainer" class="subcard">
                    <div class="row email-item align-items-end g-3 mb-2">
                      <div class="col-md-6">
                        <label class="form-label visually-hidden">Email</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-at"></i></span>
                          <input type="email" class="form-control @error('emails.0.email') is-invalid @enderror"
                            name="emails[0][email]" placeholder="Email address" value="{{ old('emails.0.email') }}" required>
                          @error('emails.0.email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label visually-hidden">Type</label>
                        <select class="form-select" name="emails[0][type]">
                          <option value="primary" {{ old('emails.0.type', 'primary') == 'primary' ? 'selected' : '' }}>Primary</option>
                          <option value="billing" {{ old('emails.0.type') == 'billing' ? 'selected' : '' }}>Billing</option>
                          <option value="support" {{ old('emails.0.type') == 'support' ? 'selected' : '' }}>Support</option>
                          <option value="personal" {{ old('emails.0.type') == 'personal' ? 'selected' : '' }}>Personal</option>
                        </select>
                      </div>
                      <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-ghost-danger btn-sm remove-email" style="display:none;">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Phone Numbers --}}
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">
                      <div class="icon"><i class="fas fa-phone"></i></div>
                      <div>Phone Numbers</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addPhone">
                      <i class="fas fa-plus me-1"></i>Add Phone
                    </button>
                  </div>

                  <div id="phoneContainer" class="subcard">
                    <div class="row phone-item align-items-end g-3 mb-2">
                      <div class="col-md-6">
                        <label class="form-label visually-hidden">Phone</label>
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                          <input type="text" class="form-control" name="phones[0][phone]" placeholder="Phone number">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label visually-hidden">Type</label>
                        <select class="form-select" name="phones[0][type]">
                          <option value="mobile" {{ old('phones.0.type', 'mobile') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                          <option value="office" {{ old('phones.0.type') == 'office' ? 'selected' : '' }}>Office</option>
                          <option value="home" {{ old('phones.0.type') == 'home' ? 'selected' : '' }}>Home</option>
                          <option value="fax" {{ old('phones.0.type') == 'fax' ? 'selected' : '' }}>Fax</option>
                        </select>
                      </div>
                      <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-ghost-danger btn-sm remove-phone" style="display:none;">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Services (Tom Select multi-select) --}}
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">
                      <div class="icon"><i class="fas fa-toolbox"></i></div>
                      <div>Services</div>
                    </div>
                    <div class="btn-group">
                      <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-info" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Manage Services
                      </a>
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickServiceModal">
                        <i class="fas fa-plus me-1"></i>Quick Add Service
                      </button>
                    </div>
                  </div>

                  @if($services->count() === 0)
                    <div class="alert alert-info" id="noServicesAlert">
                      <i class="fas fa-info-circle me-2"></i>
                      No services available. You can <button type="button" class="btn btn-link p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#quickServiceModal">create a service</button> or
                      <a href="{{ route('admin.services.index') }}" class="alert-link" target="_blank">manage all services</a>.
                    </div>
                  @endif

                  <div class="subcard">
                    <label for="servicesSelect" class="form-label">Select Services</label>
                    <select id="servicesSelect" name="services[]" multiple class="form-select" data-placeholder="Select services...">
                      @foreach($services as $service)
                        <option
                          value="{{ $service->id }}"
                          {{ in_array($service->id, old('services', [])) ? 'selected' : '' }}
                          data-detail="{{ $service->detail }}"
                        >
                          {{ $service->name }}
                        </option>
                      @endforeach
                    </select>
                    @error('services')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Type to search and select multiple services.</small>
                  </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-sticky-note"></i></div>
                    <div>Notes</div>
                  </div>
                  <div class="subcard">
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about this client...">{{ old('notes') }}</textarea>
                  </div>
                </div>

                {{-- Employee Assignment --}}
                <div class="mb-4">
                  <div class="section-title">
                    <div class="icon"><i class="fas fa-user-tag"></i></div>
                    <div>Employee Assignment</div>
                  </div>
                  <p class="text-muted small mb-3">Select an employee who will have access to this client's information.</p>
                  <div class="subcard">
                    @if($employees->count() > 0)
                      <div class="mb-2">
                        <label for="assigned_employee" class="form-label visually-hidden">Assign Employee</label>
                        <select class="form-select @error('assigned_employee') is-invalid @enderror"
                          id="assigned_employee" name="assigned_employee" aria-label="Select employee">
                          <option value="">Select an employee...</option>
                          @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_employee') == $employee->id ? 'selected' : '' }}>
                              {{ $employee->name }} ({{ $employee->position }}{{ $employee->department ? ' - ' . $employee->department : '' }})
                            </option>
                          @endforeach
                        </select>
                        @error('assigned_employee')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                    @else
                      <div class="alert alert-info m-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No active employees available for assignment. <a href="{{ route('admin.employees.create') }}">Create an employee</a> first.
                      </div>
                    @endif
                  </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex justify-content-end gap-2">
                  <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Client
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

{{-- Quick Add Service Modal --}}
<div class="modal fade" id="quickServiceModal" tabindex="-1" aria-labelledby="quickServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickServiceModalLabel">
          <i class="fas fa-plus me-2"></i>Quick Add Service
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="quickServiceErrors"></div>
        <form id="quickServiceForm">
          @csrf
          <div class="row">
            <div class="col-8">
              <div class="mb-3">
                <label for="quickServiceName" class="form-label">Service Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="quickServiceName" name="name" required maxlength="255">
              </div>
            </div>
            <div class="col-4">
              <div class="mb-3">
                <label for="quickServiceType" class="form-label">Type <span class="text-danger">*</span></label>
                <select class="form-select" id="quickServiceType" name="type" required>
                  <option value="">Select...</option>
                  @for($i = 0; $i <= 10; $i++)
                    <option value="{{ $i }}">Type {{ $i }}</option>
                  @endfor
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="quickServiceDetail" class="form-label">Detail</label>
            <textarea class="form-control" id="quickServiceDetail" name="detail" rows="3" maxlength="1000"></textarea>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="quickServiceActive" name="is_active" value="1" checked>
            <label class="form-check-label" for="quickServiceActive">
              Active Service
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="createQuickService()">
          <i class="fas fa-save me-1"></i>Create & Select
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  {{-- Tom Select JS --}}
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Tooltips
      var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      [].slice.call(tooltipTriggerList).map(function (el) { return new bootstrap.Tooltip(el); });

      // Services multi-select (Tom Select)
      var servicesSelectEl = document.getElementById('servicesSelect');
      if (servicesSelectEl) {
        var ts = new TomSelect(servicesSelectEl, {
          plugins: {
            remove_button: { title: 'Remove' }
          },
          create: false,
          persist: false,
          maxOptions: 10000,
          closeAfterSelect: false,
          placeholder: servicesSelectEl.getAttribute('data-placeholder') || 'Select services...',
          render: {
            option: function(data, escape) {
              var detail = data.detail || (data.$option && data.$option.dataset ? data.$option.dataset.detail : '');
              return '<div>' +
                       '<div>' + escape(data.text) + '</div>' +
                       (detail ? '<div class="text-muted small">' + escape(detail) + '</div>' : '') +
                     '</div>';
            }
          },
          onInitialize: function() {
            servicesSelectEl.tomselect = this;
          }
        });
      }

      // Phone and Email Management
      var phoneIndex = 1;
      var emailIndex = 1;

      // Username generation
      var clientNameInput = document.getElementById('client_name');
      var usernameInput = document.getElementById('username');

      function generateUsername() {
        var name = clientNameInput.value;
        if (name) {
          var cleanName = name.replace(/\s/g, '');
          var baseName = cleanName.substring(0, 5);
          var randomNumbers = Math.floor(Math.random() * 90 + 10); // 2-digit
          usernameInput.value = baseName + randomNumbers;
        } else {
          usernameInput.value = '';
        }
      }

      clientNameInput.addEventListener('input', generateUsername);
      clientNameInput.addEventListener('change', generateUsername);

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

      // Add phone
      document.getElementById('addPhone').addEventListener('click', function() {
        var phoneContainer = document.getElementById('phoneContainer');
        var phoneHTML =
          '      <div class="row phone-item align-items-end g-3 mb-2">' +
          '        <div class="col-md-6">' +
          '          <div class="input-group">' +
          '            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>' +
          '            <input type="text" class="form-control" name="phones[' + phoneIndex + '][phone]" placeholder="Phone number">' +
          '          </div>' +
          '        </div>' +
          '        <div class="col-md-4">' +
          '          <select class="form-select" name="phones[' + phoneIndex + '][type]">' +
          '            <option value="mobile" selected>Mobile</option>' +
          '            <option value="office">Office</option>' +
          '            <option value="home">Home</option>' +
          '            <option value="fax">Fax</option>' +
          '          </select>' +
          '        </div>' +
          '        <div class="col-md-2 text-end">' +
          '          <button type="button" class="btn btn-ghost-danger btn-sm remove-phone">' +
          '            <i class="fas fa-times"></i>' +
          '          </button>' +
          '        </div>' +
          '      </div>';
        phoneContainer.insertAdjacentHTML('beforeend', phoneHTML);
        phoneIndex++;
        updateRemoveButtons('phone');
      });

      // Add email
      document.getElementById('addEmail').addEventListener('click', function() {
        var emailContainer = document.getElementById('emailContainer');
        var emailHTML =
          '      <div class="row email-item align-items-end g-3 mb-2">' +
          '        <div class="col-md-6">' +
          '          <div class="input-group">' +
          '            <span class="input-group-text"><i class="fas fa-at"></i></span>' +
          '            <input type="email" class="form-control" name="emails[' + emailIndex + '][email]" placeholder="Email address" required>' +
          '          </div>' +
          '        </div>' +
          '        <div class="col-md-4">' +
          '          <select class="form-select" name="emails[' + emailIndex + '][type]">' +
          '            <option value="primary" selected>Primary</option>' +
          '            <option value="billing">Billing</option>' +
          '            <option value="support">Support</option>' +
          '            <option value="personal">Personal</option>' +
          '          </select>' +
          '        </div>' +
          '        <div class="col-md-2 text-end">' +
          '          <button type="button" class="btn btn-ghost-danger btn-sm remove-email">' +
          '            <i class="fas fa-times"></i>' +
          '          </button>' +
          '        </div>' +
          '      </div>';
        emailContainer.insertAdjacentHTML('beforeend', emailHTML);
        emailIndex++;
        updateRemoveButtons('email');
      });

      // Remove phone/email
      document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-phone')) {
          e.target.closest('.phone-item').remove();
          updateRemoveButtons('phone');
        }
        if (e.target.closest('.remove-email')) {
          e.target.closest('.email-item').remove();
          updateRemoveButtons('email');
        }
      });

      // Update remove buttons visibility
      function updateRemoveButtons(type) {
        var items = document.querySelectorAll('.' + type + '-item');
        items.forEach(function(item) {
          var removeBtn = item.querySelector('.remove-' + type);
          if (!removeBtn) return;
          if (items.length > 1) {
            removeBtn.style.display = 'inline-block';
          } else {
            removeBtn.style.display = 'none';
          }
        });
      }

      updateRemoveButtons('phone');
      updateRemoveButtons('email');

      // Generate username on page load (e.g., after validation errors)
      generateUsername();
    });

    // Quick Service Creation
    function createQuickService() {
      var modal = bootstrap.Modal.getInstance(document.getElementById('quickServiceModal'));
      var formData = new FormData(document.getElementById('quickServiceForm'));

      fetch('{{ route("admin.services.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function(response) { return response.json(); })
      .then(function(data) {
        if (data.success) {
          // Hide info alert if present
          var noServicesAlert = document.getElementById('noServicesAlert');
          if (noServicesAlert) noServicesAlert.style.display = 'none';

          // Add the new service to the Tom Select and select it
          var selectEl = document.getElementById('servicesSelect');
          if (selectEl && selectEl.tomselect) {
            var ts = selectEl.tomselect;

            if (!ts.options[data.service.id]) {
              ts.addOption({
                value: String(data.service.id),
                text: data.service.name,
                detail: data.service.detail || ''
              });
            }
            ts.addItem(String(data.service.id));
          }

          document.getElementById('quickServiceForm').reset();
          modal.hide();

          // Success alert
          var alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
          alertDiv.innerHTML =
            'Service "' + data.service.name + '" created successfully and selected!' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
          var body = document.querySelector('.card-modern .card-body');
          body.insertBefore(alertDiv, body.firstChild);
          setTimeout(function() { alertDiv.remove(); }, 3000);
        } else {
          var errorDiv = document.getElementById('quickServiceErrors');
          errorDiv.innerHTML = '';
          if (data.errors) {
            Object.values(data.errors).forEach(function(error) {
              errorDiv.innerHTML += '<div class="text-danger small">' + error[0] + '</div>';
            });
          } else {
            errorDiv.innerHTML = '<div class="text-danger small">An error occurred while creating the service.</div>';
          }
        }
      })
      .catch(function(error) {
        console.error('Error:', error);
        document.getElementById('quickServiceErrors').innerHTML = '<div class="text-danger small">Network error occurred.</div>';
      });
    }
  </script>
@endpush