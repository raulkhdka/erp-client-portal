@extends('layouts.app')

@section('title', 'Record New Call')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Record New Call</h3>
                    <a href="{{ route('call-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Call Logs
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('call-logs.store') }}" method="POST">
                        @csrf

                        <!-- Call Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Call Information</h5>
                            </div>

                            <!-- Employee Selection (Admin Only) -->
                            @if(Auth::user()->role === 'admin')
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Assign to Employee</label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror"
                                            id="employee_id"
                                            name="employee_id">
                                        <option value="">Select employee...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty to assign to yourself</div>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select class="form-select client-select @error('client_id') is-invalid @enderror"
                                            id="client_id"
                                            name="client_id"
                                            required>
                                        <option value="">Select client...</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}"
                                                    data-contact-name="{{ $client->user ? $client->user->name : ($client->contact_person ?? 'N/A') }}"
                                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="call_type" class="form-label">Call Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('call_type') is-invalid @enderror"
                                            id="call_type"
                                            name="call_type"
                                            required>
                                        <option value="">Select type...</option>
                                        <option value="incoming" {{ old('call_type') == 'incoming' ? 'selected' : '' }}>
                                            <i class="fas fa-phone"></i> Incoming
                                        </option>
                                        <option value="outgoing" {{ old('call_type') == 'outgoing' ? 'selected' : '' }}>
                                            <i class="fas fa-phone-alt"></i> Outgoing
                                        </option>
                                    </select>
                                    @error('call_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="call_date" class="form-label">Call Date/Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                           class="form-control @error('call_date') is-invalid @enderror"
                                           id="call_date"
                                           name="call_date"
                                           value="{{ old('call_date', now()->format('Y-m-d\TH:i')) }}"
                                           required>
                                    @error('call_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Caller Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Caller Information</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="caller_name" class="form-label">Caller Name</label>
                                    <input type="text"
                                           class="form-control @error('caller_name') is-invalid @enderror"
                                           id="caller_name"
                                           name="caller_name"
                                           value="{{ old('caller_name') }}"
                                           maxlength="255"
                                           placeholder="Enter caller's name">
                                    @error('caller_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Will auto-fill when client is selected</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="caller_phone_select" class="form-label">Caller Phone</label>
                                    <select class="form-select @error('caller_phone') is-invalid @enderror"
                                            id="caller_phone_select"
                                            name="caller_phone_select"
                                            style="display: none;">
                                        <option value="">Select phone number...</option>
                                    </select>
                                    <input type="text"
                                           class="form-control @error('caller_phone') is-invalid @enderror"
                                           id="caller_phone"
                                           name="caller_phone"
                                           value="{{ old('caller_phone') }}"
                                           maxlength="20"
                                           placeholder="Phone number">
                                    @error('caller_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Will show client phone options when available</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number"
                                           class="form-control @error('duration_minutes') is-invalid @enderror"
                                           id="duration_minutes"
                                           name="duration_minutes"
                                           value="{{ old('duration_minutes') }}"
                                           min="0"
                                           placeholder="Call duration">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Call Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Call Details</h5>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('subject') is-invalid @enderror"
                                           id="subject"
                                           name="subject"
                                           value="{{ old('subject') }}"
                                           required
                                           maxlength="255"
                                           placeholder="Brief subject of the call">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority"
                                            name="priority"
                                            required>
                                        @foreach(\App\Models\CallLog::getPriorityOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('priority', 'medium') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        @foreach(\App\Models\CallLog::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 1) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="4"
                                              required
                                              placeholder="Detailed description of the call and discussion">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes"
                                              name="notes"
                                              rows="3"
                                              placeholder="Any additional notes or comments">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Follow-up -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Follow-up</h5>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="follow_up_required" class="form-label">Follow-up Required</label>
                                    <textarea class="form-control @error('follow_up_required') is-invalid @enderror"
                                              id="follow_up_required"
                                              name="follow_up_required"
                                              rows="2"
                                              placeholder="Describe any follow-up actions needed">{{ old('follow_up_required') }}</textarea>
                                    @error('follow_up_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date"
                                           class="form-control @error('follow_up_date') is-invalid @enderror"
                                           id="follow_up_date"
                                           name="follow_up_date"
                                           value="{{ old('follow_up_date') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('follow_up_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Task Creation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="create_task"
                                                   name="create_task"
                                                   value="1"
                                                   {{ old('create_task', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="create_task">
                                                <strong>Automatically create a task for this call</strong>
                                            </label>
                                            <div class="form-text">
                                                This will create a task assigned to you for follow-up.
                                                Tasks are not created for calls with "Resolved" status.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('call-logs.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Record Call
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, jQuery available:', typeof $ !== 'undefined');
    console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');

    // Wait for jQuery and Select2 to be ready
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        // Initialize Select2 for client selection
        $('#client_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search and select client...',
            allowClear: true,
            width: '100%'
        });
        console.log('Select2 initialized for client_id');
    } else {
        console.error('jQuery or Select2 not available');
    }

    // Auto-populate caller information when client is selected
    const clientSelect = document.getElementById('client_id');
    const callerNameField = document.getElementById('caller_name');
    const callerPhoneField = document.getElementById('caller_phone');
    const callerPhoneSelect = document.getElementById('caller_phone_select');

    console.log('Client select element found:', clientSelect !== null);
    console.log('Caller name field found:', callerNameField !== null);
    console.log('Caller phone field found:', callerPhoneField !== null);
    console.log('Caller phone select found:', callerPhoneSelect !== null);

    if (clientSelect) {
        clientSelect.addEventListener('change', function() {
            const clientId = this.value;
            console.log('Client selected:', clientId);

            if (clientId) {
                // Clear existing values
                if (callerNameField) callerNameField.value = '';
                if (callerPhoneField) callerPhoneField.value = '';
                if (callerPhoneSelect) callerPhoneSelect.innerHTML = '<option value="">Select phone number...</option>';

                // Show loading state
                if (callerNameField) callerNameField.placeholder = 'Loading...';

                // Fetch client contact information
                console.log('Fetching contacts for client:', clientId);
                fetch(`/call-logs/client/${clientId}/contacts`)
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Client contacts data:', data);

                        // Auto-fill caller name with primary contact
                        if (data.primary_contact) {
                            if (callerNameField) callerNameField.value = data.primary_contact;
                            console.log('Set caller name to:', data.primary_contact);
                        }
                        if (callerNameField) callerNameField.placeholder = 'Enter caller\'s name';

                        // Handle phone numbers
                        if (data.phones && data.phones.length > 0) {
                            console.log('Found phone numbers:', data.phones.length);
                            // If multiple phone numbers, show select dropdown
                            if (data.phones.length > 1) {
                                console.log('Multiple phones found, showing dropdown');
                                data.phones.forEach(phone => {
                                    const option = document.createElement('option');
                                    option.value = phone.phone;
                                    option.textContent = `${phone.phone} (${phone.type}${phone.is_primary ? ' - Primary' : ''})`;
                                    if (phone.is_primary) {
                                        option.selected = true;
                                    }
                                    if (callerPhoneSelect) callerPhoneSelect.appendChild(option);
                                });

                                // Show select dropdown and hide input
                                if (callerPhoneSelect) callerPhoneSelect.style.display = 'block';
                                if (callerPhoneField) callerPhoneField.style.display = 'none';

                                // Set initial value from primary phone
                                const primaryPhone = data.phones.find(p => p.is_primary) || data.phones[0];
                                if (primaryPhone) {
                                    if (callerPhoneSelect) callerPhoneSelect.value = primaryPhone.phone;
                                    if (callerPhoneField) callerPhoneField.value = primaryPhone.phone;
                                    console.log('Set primary phone to:', primaryPhone.phone);
                                }

                                // Listen for phone selection changes
                                if (callerPhoneSelect) {
                                    callerPhoneSelect.addEventListener('change', function() {
                                        if (callerPhoneField) callerPhoneField.value = this.value;
                                        console.log('Phone selection changed to:', this.value);
                                    });
                                }

                            } else {
                                console.log('Single phone found, auto-filling input');
                                // Only one phone number, auto-fill input
                                if (callerPhoneField) callerPhoneField.value = data.phones[0].phone;
                                if (callerPhoneSelect) callerPhoneSelect.style.display = 'none';
                                if (callerPhoneField) callerPhoneField.style.display = 'block';
                                console.log('Set single phone to:', data.phones[0].phone);
                            }
                        } else {
                            console.log('No phone numbers found');
                            // No phone numbers found
                            if (callerPhoneSelect) callerPhoneSelect.style.display = 'none';
                            if (callerPhoneField) {
                                callerPhoneField.style.display = 'block';
                                callerPhoneField.placeholder = 'No phone numbers on file';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching client contacts:', error);
                        if (callerNameField) callerNameField.placeholder = 'Enter caller\'s name';
                        if (callerPhoneField) callerPhoneField.placeholder = 'Phone number';

                        // Hide select and show input on error
                        if (callerPhoneSelect) callerPhoneSelect.style.display = 'none';
                        if (callerPhoneField) callerPhoneField.style.display = 'block';
                    });
            } else {
                console.log('Client deselected, clearing fields');
                // Client deselected, clear fields
                if (callerNameField) callerNameField.value = '';
                if (callerPhoneField) callerPhoneField.value = '';
                if (callerPhoneSelect) callerPhoneSelect.innerHTML = '<option value="">Select phone number...</option>';
                if (callerPhoneSelect) callerPhoneSelect.style.display = 'none';
                if (callerPhoneField) callerPhoneField.style.display = 'block';
                if (callerNameField) callerNameField.placeholder = 'Enter caller\'s name';
                if (callerPhoneField) callerPhoneField.placeholder = 'Phone number';
            }
        });
    } else {
        console.error('Client select element not found');
    }

    // Disable task creation for resolved status
    const statusSelect = document.getElementById('status');
    const createTaskCheckbox = document.getElementById('create_task');

    console.log('Status select element found:', statusSelect !== null);
    console.log('Create task checkbox found:', createTaskCheckbox !== null);

    if (statusSelect && createTaskCheckbox) {
        statusSelect.addEventListener('change', function() {
            console.log('Status changed to:', this.value);
            if (this.value == '8') { // Resolved status
                createTaskCheckbox.checked = false;
                createTaskCheckbox.disabled = true;
                console.log('Disabled task creation for resolved status');
            } else {
                createTaskCheckbox.disabled = false;
                console.log('Enabled task creation');
            }
        });

        // Initial check
        if (statusSelect.value == '8') {
            createTaskCheckbox.checked = false;
            createTaskCheckbox.disabled = true;
            console.log('Initial status is resolved, task creation disabled');
        }
    }

    // Auto-fill current date/time if not set
    const callDateField = document.getElementById('call_date');
    if (callDateField && !callDateField.value) {
        const now = new Date();
        const formattedDate = now.toISOString().slice(0, 16);
        callDateField.value = formattedDate;
    }
});
</script>
@endpush
