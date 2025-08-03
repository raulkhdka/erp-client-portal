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

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.clients.store') }}">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Client User Creation</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">User Name *</label>
                            <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                   id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                            @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Client Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="client_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                                   id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                            @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                            @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tax_id" class="form-label">Tax ID</label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                   id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                            @error('tax_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="business_license" class="form-label">Business License</label>
                            <input type="text" class="form-control @error('business_license') is-invalid @enderror"
                                   id="business_license" name="business_license" value="{{ old('business_license') }}">
                            @error('business_license')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Phone Numbers</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addPhone">
                                    <i class="fas fa-plus me-1"></i>Add Phone
                                </button>
                            </div>
                            <div id="phoneContainer">
                                <div class="row phone-item mb-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="phones[0][phone]" placeholder="Phone number" value="{{ old('phones.0.phone') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="phones[0][type]">
                                            <option value="mobile" {{ old('phones.0.type', 'mobile') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                            <option value="office" {{ old('phones.0.type') == 'office' ? 'selected' : '' }}>Office</option>
                                            <option value="home" {{ old('phones.0.type') == 'home' ? 'selected' : '' }}>Home</option>
                                            <option value="fax" {{ old('phones.0.type') == 'fax' ? 'selected' : '' }}>Fax</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-phone" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Additional Email Addresses</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addEmail">
                                    <i class="fas fa-plus me-1"></i>Add Email
                                </button>
                            </div>
                            <div id="emailContainer">
                                <div class="row email-item mb-3">
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="emails[0][email]" placeholder="Additional email address" value="{{ old('emails.0.email') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="emails[0][type]">
                                            <option value="primary" {{ old('emails.0.type', 'primary') == 'primary' ? 'selected' : '' }}>Primary</option>
                                            <option value="billing" {{ old('emails.0.type') == 'billing' ? 'selected' : '' }}>Billing</option>
                                            <option value="support" {{ old('emails.0.type') == 'support' ? 'selected' : '' }}>Support</option>
                                            <option value="personal" {{ old('emails.0.type') == 'personal' ? 'selected' : '' }}>Personal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-email" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Services</h5>
                                <div class="btn-group">
                                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-external-link-alt me-1"></i>Manage Services
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickServiceModal">
                                        <i class="fas fa-plus me-1"></i>Quick Add Service
                                    </button>
                                </div>
                            </div>
                            @if($services->count() > 0)
                                <div class="row" id="servicesContainer">
                                    @foreach($services as $service)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                       value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                       {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_{{ $service->id }}" title="{{ $service->detail }}">
                                                    {{ $service->name }}
                                                    @if($service->detail)
                                                        <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $service->detail }}"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info" id="noServicesAlert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No services available. You can <button type="button" class="btn btn-link p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#quickServiceModal">create a service</button> or <a href="{{ route('admin.services.index') }}" class="alert-link" target="_blank">manage all services</a>.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Any additional notes about this client...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Employee Assignment</h5>
                            <p class="text-muted small mb-3">Select employees who will have access to this client's information. Hold `Ctrl` (or `Cmd` on Mac) to select multiple.</p>
                            @if($employees->count() > 0)
                                <div class="mb-3">
                                    <label for="assigned_employees" class="form-label visually-hidden">Assign Employees</label>
                                    <select class="form-select @error('assigned_employees') is-invalid @enderror"
                                            id="assigned_employees" name="assigned_employees[]" multiple aria-label="Select employees">
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                    {{ in_array($employee->id, old('assigned_employees', [])) ? 'selected' : '' }}>
                                                {{ $employee->user->name }} ({{ $employee->position }}{{ $employee->department ? ' - ' . $employee->department : '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_employees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No active employees available for assignment. <a href="{{ route('admin.employees.create') }}">Create an employee</a> first.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Client
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
                <h6>Creating a New Client</h6>
                <p class="small text-muted mb-3">
                    Fill out this form to add a new client to the system. The client will receive login credentials
                    via email and can access their dedicated dashboard.
                </p>

                <h6>Employee Assignment</h6>
                <p class="small text-muted mb-3">
                    Assign employees to this client to grant them access to client information and documents.
                    Employees will be able to view and manage this client's data based on their permissions.
                </p>

                <h6>Required Fields</h6>
                <ul class="small text-muted mb-3">
                    <li>Contact Person Name</li>
                    <li>Email Address</li>
                    <li>Password</li>
                    <li>Company Name</li>
                </ul>

                <h6>Services</h6>
                <p class="small text-muted">
                    Select the services that this client will be using. You can modify these later.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Phone and Email Management
    let phoneIndex = 1; // Start from 1 since index 0 is already used
    let emailIndex = 1; // Start from 1 since index 0 is already used

    // Add phone functionality
    document.getElementById('addPhone').addEventListener('click', function() {
        const phoneContainer = document.getElementById('phoneContainer');
        const phoneHTML = `
            <div class="row phone-item mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="phones[${phoneIndex}][phone]" placeholder="Phone number">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="phones[${phoneIndex}][type]">
                        <option value="mobile" selected>Mobile</option>
                        <option value="office">Office</option>
                        <option value="home">Home</option>
                        <option value="fax">Fax</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-phone">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        phoneContainer.insertAdjacentHTML('beforeend', phoneHTML);
        phoneIndex++;
        updateRemoveButtons('phone');
    });

    // Add email functionality
    document.getElementById('addEmail').addEventListener('click', function() {
        const emailContainer = document.getElementById('emailContainer');
        const emailHTML = `
            <div class="row email-item mb-3">
                <div class="col-md-6">
                    <input type="email" class="form-control" name="emails[${emailIndex}][email]" placeholder="Additional email address">
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="emails[${emailIndex}][type]">
                        <option value="primary" selected>Primary</option>
                        <option value="billing">Billing</option>
                        <option value="support">Support</option>
                        <option value="personal">Personal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-email">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        emailContainer.insertAdjacentHTML('beforeend', emailHTML);
        emailIndex++;
        updateRemoveButtons('email');
    });

    // Remove phone/email functionality
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
        const items = document.querySelectorAll(`.${type}-item`);
        items.forEach((item, index) => {
            const removeBtn = item.querySelector(`.remove-${type}`);
            if (items.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
    // Initial call to hide remove button if only one item exists on load
    updateRemoveButtons('phone');
    updateRemoveButtons('email');
});

// Quick Service Creation
function createQuickService() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('quickServiceModal'));
    const formData = new FormData(document.getElementById('quickServiceForm'));

    fetch('{{ route("admin.services.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new service to the services list
            let servicesContainer = document.getElementById('servicesContainer');
            const noServicesAlert = document.getElementById('noServicesAlert');

            if (noServicesAlert) {
                noServicesAlert.style.display = 'none';
            }

            if (!servicesContainer) {
                // If no services container exists, create one
                const parentDiv = noServicesAlert.parentNode;
                const newContainer = document.createElement('div');
                newContainer.className = 'row';
                newContainer.id = 'servicesContainer';
                parentDiv.insertBefore(newContainer, noServicesAlert);
                servicesContainer = newContainer; // Update reference
            }

            const serviceHtml = `
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="services[]"
                               value="${data.service.id}" id="service_${data.service.id}" checked>
                        <label class="form-check-label" for="service_${data.service.id}" title="${data.service.detail || ''}">
                            ${data.service.name}
                            ${data.service.detail ? `<i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="${data.service.detail}"></i>` : ''}
                        </label>
                    </div>
                </div>
            `;

            servicesContainer.insertAdjacentHTML('beforeend', serviceHtml);

            // Re-initialize tooltips for new elements if necessary (Bootstrap 5)
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Reset form and close modal
            document.getElementById('quickServiceForm').reset();
            modal.hide();

            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                Service "${data.service.name}" created successfully and selected!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            // Insert alert after the form card body, or at the top of the main content div
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);


            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        } else {
            // Show error messages
            const errorDiv = document.getElementById('quickServiceErrors');
            errorDiv.innerHTML = '';
            if (data.errors) {
                Object.values(data.errors).forEach(error => {
                    errorDiv.innerHTML += `<div class="text-danger small">${error[0]}</div>`;
                });
            } else {
                errorDiv.innerHTML = '<div class="text-danger small">An error occurred while creating the service.</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('quickServiceErrors').innerHTML = '<div class="text-danger small">Network error occurred.</div>';
    });
}
</script>
@endpush

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
                                    {{-- Assuming 'types' are passed from controller or defined elsewhere. For a quick add,
                                         you might want a fixed set or fetch dynamically. Using placeholder example for now. --}}
                                    @for($i = 0; $i <= 10; $i++) {{-- Replace with actual service types from your DB/logic --}}
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
