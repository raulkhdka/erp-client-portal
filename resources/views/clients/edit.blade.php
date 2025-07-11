@extends('layouts.app')

@section('title', 'Edit Client - ' . $client->company_name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Client: {{ $client->company_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Back to Client
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list me-2"></i>All Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('clients.update', $client->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Contact Person Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Contact Person Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $client->user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $client->user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Company Information</h5>
                        </div>
                        <div class="col-md-6">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}" required>
                            @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $client->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tax_id" class="form-label">Tax ID</label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                   id="tax_id" name="tax_id" value="{{ old('tax_id', $client->tax_id) }}">
                            @error('tax_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="business_license" class="form-label">Business License</label>
                            <input type="text" class="form-control @error('business_license') is-invalid @enderror"
                                   id="business_license" name="business_license" value="{{ old('business_license', $client->business_license) }}">
                            @error('business_license')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $client->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone Numbers -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Phone Numbers</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addPhone">
                                    <i class="fas fa-plus me-1"></i>Add Phone
                                </button>
                            </div>
                            <div id="phoneContainer">
                                @forelse($client->phones as $index => $phone)
                                    <div class="row phone-item mb-3">
                                        <input type="hidden" name="phones[{{ $index }}][id]" value="{{ $phone->id }}">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="phones[{{ $index }}][phone]"
                                                   placeholder="Phone number" value="{{ old('phones.'.$index.'.phone', $phone->phone) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="phones[{{ $index }}][type]"
                                                   placeholder="Type (e.g., Primary, Mobile)"
                                                   list="phoneTypes"
                                                   value="{{ old('phones.'.$index.'.type', $phone->type) }}">
                                            <datalist id="phoneTypes">
                                                <option value="Primary">
                                                <option value="Mobile">
                                                <option value="Office">
                                                <option value="Fax">
                                                <option value="Home">
                                                <option value="Work">
                                                <option value="Emergency">
                                            </datalist>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-phone" {{ $client->phones->count() <= 1 ? 'style=display:none;' : '' }}>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="row phone-item mb-3">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="phones[0][phone]" placeholder="Phone number" value="{{ old('phones.0.phone') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="phones[0][type]"
                                                   placeholder="Type (e.g., Primary, Mobile)"
                                                   list="phoneTypes"
                                                   value="{{ old('phones.0.type', 'Primary') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-phone" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Email Addresses -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Additional Email Addresses</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addEmail">
                                    <i class="fas fa-plus me-1"></i>Add Email
                                </button>
                            </div>
                            <div id="emailContainer">
                                @forelse($client->emails as $index => $email)
                                    <div class="row email-item mb-3">
                                        <input type="hidden" name="emails[{{ $index }}][id]" value="{{ $email->id }}">
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" name="emails[{{ $index }}][email]"
                                                   placeholder="Additional email address" value="{{ old('emails.'.$index.'.email', $email->email) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="emails[{{ $index }}][type]"
                                                   placeholder="Type (e.g., Secondary, Billing)"
                                                   list="emailTypes"
                                                   value="{{ old('emails.'.$index.'.type', $email->type) }}">
                                            <datalist id="emailTypes">
                                                <option value="Secondary">
                                                <option value="Billing">
                                                <option value="Support">
                                                <option value="Personal">
                                                <option value="Finance">
                                                <option value="Legal">
                                                <option value="HR">
                                                <option value="Technical">
                                            </datalist>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-email" {{ $client->emails->count() <= 1 ? 'style=display:none;' : '' }}>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="row email-item mb-3">
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" name="emails[0][email]" placeholder="Additional email address" value="{{ old('emails.0.email') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="emails[0][type]"
                                                   placeholder="Type (e.g., Secondary, Billing)"
                                                   list="emailTypes"
                                                   value="{{ old('emails.0.type', 'Secondary') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-email" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Services</h5>
                                <div class="btn-group">
                                    <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-external-link-alt me-1"></i>Manage Services
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickServiceModal">
                                        <i class="fas fa-plus me-1"></i>Quick Add Service
                                    </button>
                                </div>
                            </div>
                            @if($services->count() > 0)
                                <div class="row" id="servicesContainer">
                                    @php
                                        $currentServiceIds = old('services', $client->services->pluck('id')->toArray());
                                    @endphp
                                    @foreach($services as $service)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                       value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                       {{ in_array($service->id, $currentServiceIds) ? 'checked' : '' }}>
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
                                    No services available. You can <button type="button" class="btn btn-link p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#quickServiceModal">create a service</button> or <a href="{{ route('services.index') }}" class="alert-link" target="_blank">manage all services</a>.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Any additional notes about this client...">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Employee Assignment -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Employee Assignment</h5>
                            <p class="text-muted small mb-3">Select employees who will have access to this client's information</p>
                            @if($employees->count() > 0)
                                @php
                                    $assignedEmployeeIds = old('assigned_employees', $client->assignedEmployees->pluck('id')->toArray());
                                @endphp
                                <div class="row">
                                    @foreach($employees as $employee)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border {{ in_array($employee->id, $assignedEmployeeIds) ? 'border-primary' : '' }}">
                                                <div class="card-body p-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="assigned_employees[]" value="{{ $employee->id }}"
                                                               id="employee_{{ $employee->id }}"
                                                               {{ in_array($employee->id, $assignedEmployeeIds) ? 'checked' : '' }}>
                                                        <label class="form-check-label w-100" for="employee_{{ $employee->id }}">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <i class="fas fa-user"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-0">{{ $employee->user->name }}</h6>
                                                                    <small class="text-muted">{{ $employee->position }}</small>
                                                                    @if($employee->department)
                                                                        <br><small class="text-muted">{{ $employee->department }}</small>
                                                                    @endif
                                                                    @if(in_array($employee->id, $assignedEmployeeIds))
                                                                        <br><span class="badge bg-success">Currently Assigned</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No active employees available for assignment. <a href="{{ route('employees.create') }}">Create an employee</a> first.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Client
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Contact Methods -->
        @if($client->phones->count() > 0 || $client->emails->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-address-book me-2"></i>Current Contact Methods</h6>
            </div>
            <div class="card-body">
                @if($client->phones->count() > 0)
                <h6>Phone Numbers</h6>
                @foreach($client->phones as $phone)
                <p class="mb-1 small">
                    <i class="fas fa-phone me-2"></i>{{ $phone->phone }}
                    <span class="badge bg-secondary ms-2">{{ ucfirst($phone->type) }}</span>
                </p>
                @endforeach
                <hr>
                @endif

                @if($client->emails->count() > 0)
                <h6>Additional Emails</h6>
                @foreach($client->emails as $email)
                <p class="mb-1 small">
                    <i class="fas fa-envelope me-2"></i>{{ $email->email }}
                    <span class="badge bg-secondary ms-2">{{ ucfirst($email->type) }}</span>
                </p>
                @endforeach
                @endif
            </div>
        </div>
        @endif

        <!-- Help Information -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Edit Information</h6>
            </div>
            <div class="card-body">
                <h6>Editing Client</h6>
                <p class="small text-muted mb-3">
                    Update the client's information using this form. Changes will be saved immediately.
                </p>

                <h6>Employee Assignment</h6>
                <p class="small text-muted mb-3">
                    Modify employee assignments to control who has access to this client's information.
                    Changes will update employee permissions immediately.
                </p>

                <h6>Required Fields</h6>
                <ul class="small text-muted mb-3">
                    <li>Contact Person Name</li>
                    <li>Email Address</li>
                    <li>Company Name</li>
                    <li>Status</li>
                </ul>

                <h6>Services</h6>
                <p class="small text-muted">
                    Update the services that this client is using. You can select multiple services.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Employee assignment visual feedback
document.addEventListener('DOMContentLoaded', function() {
    const employeeCheckboxes = document.querySelectorAll('input[name="assigned_employees[]"]');

    employeeCheckboxes.forEach(checkbox => {
        // Set initial state
        const card = checkbox.closest('.card');
        if (checkbox.checked) {
            card.classList.add('border-primary');
            card.style.backgroundColor = '#f8f9ff';
        }

        // Add change listener
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                card.classList.add('border-primary');
                card.style.backgroundColor = '#f8f9ff';
            } else {
                card.classList.remove('border-primary');
                card.style.backgroundColor = '';
            }
        });
    });

    // Phone and Email Management
    let phoneIndex = {{ $client->phones->count() }}; // Start from current count
    let emailIndex = {{ $client->emails->count() }}; // Start from current count

    // Add phone functionality
    document.getElementById('addPhone').addEventListener('click', function() {
        const phoneContainer = document.getElementById('phoneContainer');
        const phoneHTML = `
            <div class="row phone-item mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="phones[${phoneIndex}][phone]" placeholder="Phone number">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="phones[${phoneIndex}][type]"
                           placeholder="Type (e.g., Primary, Mobile)"
                           list="phoneTypes"
                           value="Primary">
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
                    <input type="text" class="form-control" name="emails[${emailIndex}][type]"
                           placeholder="Type (e.g., Secondary, Billing)"
                           list="emailTypes"
                           value="Secondary">
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

    // Initialize remove buttons on page load
    updateRemoveButtons('phone');
    updateRemoveButtons('email');
});

// Quick Service Creation
function createQuickService() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('quickServiceModal'));
    const formData = new FormData(document.getElementById('quickServiceForm'));

    fetch('{{ route("services.store") }}', {
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
            const servicesContainer = document.getElementById('servicesContainer');
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

            document.getElementById('servicesContainer').insertAdjacentHTML('beforeend', serviceHtml);

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

<!-- Quick Service Creation Modal -->
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
