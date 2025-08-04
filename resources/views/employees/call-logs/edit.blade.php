@extends('layouts.app')

@section('title', 'Edit My Call Log')

@section('breadcrumb')
    <a href="{{ route('employees.call-logs.index') }}">My Call Logs</a>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employees.call-logs.show', $callLog) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>View Call Log
        </a>
        <a href="{{ route('employees.call-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to My Call Logs
        </a>
    </div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit My Call Log</h4>
                    {{-- <div>
                        <a href="{{ route('employees.call-logs.show', $callLog) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('employees.call-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div> --}}
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('employees.call-logs.update', $callLog) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Basic Information</h5>

                                <div class="form-group">
                                    <label for="client_id">Client *</label>
                                    <select class="form-select client-select @error('client_id') is-invalid @enderror"
                                            id="client_id" name="client_id" required>
                                        <option value="">Select client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                    data-contact-name="{{ $client->name }}"
                                                    {{ old('client_id', $callLog->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="caller_name">Caller Name</label>
                                    <input type="text" name="caller_name" id="caller_name" class="form-control @error('caller_name') is-invalid @enderror"
                                           value="{{ old('caller_name', $callLog->caller_name) }}" placeholder="Name of the person who called">
                                    @error('caller_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="caller_phone">Caller Phone</label>
                                    <select class="form-select @error('caller_phone_select') is-invalid @enderror"
                                            id="caller_phone_select" name="caller_phone_select" style="display: none;">
                                        <option value="">Select phone number...</option>
                                    </select>
                                    <input type="text" class="form-control @error('caller_phone') is-invalid @enderror"
                                           id="caller_phone" name="caller_phone"
                                           value="{{ old('caller_phone', $callLog->caller_phone) }}" maxlength="20"
                                           placeholder="Phone number">
                                    <small class="form-text text-muted">Will show client phone options when available</small>
                                    @error('caller_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="call_type">Call Type *</label>
                                    <select name="call_type" id="call_type" class="form-control @error('call_type') is-invalid @enderror" required>
                                        <option value="incoming" {{ old('call_type', $callLog->call_type) == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                        <option value="outgoing" {{ old('call_type', $callLog->call_type) == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                    </select>
                                    @error('call_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="call_date">Call Date & Time</label>
                                    <input type="datetime-local" name="call_date" id="call_date" class="form-control @error('call_date') is-invalid @enderror"
                                           value="{{ old('call_date', $callLog->call_date ? $callLog->call_date->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('call_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="duration_minutes">Duration (minutes)</label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                                           value="{{ old('duration_minutes', $callLog->duration_minutes) }}" min="0" placeholder="Call duration in minutes">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Call Details</h5>

                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="low" {{ old('priority', $callLog->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $callLog->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $callLog->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $callLog->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ old('status', $callLog->status) == 1 ? 'selected' : '' }}>Pending</option>
                                        <option value="2" {{ old('status', $callLog->status) == 2 ? 'selected' : '' }}>In Progress</option>
                                        <option value="3" {{ old('status', $callLog->status) == 3 ? 'selected' : '' }}>On Hold</option>
                                        <option value="4" {{ old('status', $callLog->status) == 4 ? 'selected' : '' }}>Escalated</option>
                                        <option value="5" {{ old('status', $callLog->status) == 5 ? 'selected' : '' }}>Waiting for Client</option>
                                        <option value="6" {{ old('status', $callLog->status) == 6 ? 'selected' : '' }}>Testing</option>
                                        <option value="7" {{ old('status', $callLog->status) == 7 ? 'selected' : '' }}>Completed</option>
                                        <option value="8" {{ old('status', $callLog->status) == 8 ? 'selected' : '' }}>Resolved</option>
                                        <option value="9" {{ old('status', $callLog->status) == 9 ? 'selected' : '' }}>Backlog</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="follow_up_required" id="follow_up_required" class="form-check-input @error('follow_up_required') is-invalid @enderror"
                                               value="1" {{ old('follow_up_required', $callLog->follow_up_required) ? 'checked' : '' }}
                                               onchange="toggleFollowUpDate()">
                                        <label class="form-check-label" for="follow_up_required">
                                            Follow-up Required
                                        </label>
                                    </div>
                                    @error('follow_up_required')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group" id="follow_up_date_group" style="{{ old('follow_up_required', $callLog->follow_up_required) ? '' : 'display: none;' }}">
                                    <label for="follow_up_date">Follow-up Date</label>
                                    <input type="date" name="follow_up_date" id="follow_up_date" class="form-control @error('follow_up_date') is-invalid @enderror"
                                           value="{{ old('follow_up_date', $callLog->follow_up_date ? $callLog->follow_up_date->format('Y-m-d') : '') }}">
                                    @error('follow_up_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="subject">Subject *</label>
                                    <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror"
                                           value="{{ old('subject', $callLog->subject) }}" required placeholder="Brief subject of the call">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Fields -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required
                                              placeholder="Detailed description of the call...">{{ old('description', $callLog->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="notes">Additional Notes</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                              placeholder="Any additional notes or comments...">{{ old('notes', $callLog->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Call Log
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
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
$(document).ready(function() {
    $('#client_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search and select client...',
        allowClear: true,
        width: '100%'
    });

    function fetchClientContacts(clientId, initialLoad = false) {
        const callerNameField = document.getElementById('caller_name');
        const callerPhoneField = document.getElementById('caller_phone');
        const callerPhoneSelect = document.getElementById('caller_phone_select');
        callerPhoneSelect.innerHTML = '<option value="">Select phone number...</option>';

        const currentName = initialLoad ? '{{ old('caller_name', $callLog->caller_name) }}' : callerNameField.value;
        const currentPhone = initialLoad ? '{{ old('caller_phone', $callLog->caller_phone) }}' : callerPhoneField.value;

        if (clientId) {
            const selectedOption = $('#client_id').find('option:selected');
            const clientName = selectedOption.data('contact-name');
            if (clientName && (!currentName || !initialLoad)) {
                callerNameField.value = clientName;
            } else {
                callerNameField.placeholder = 'Enter caller\'s name';
            }
            fetch(`/call-logs/client/${clientId}/contacts`)
                .then(response => response.json())
                .then(data => {
                    if (data.primary_contact && (!currentName || !initialLoad)) {
                        callerNameField.value = data.primary_contact;
                    }
                    if (data.phones && data.phones.length > 0) {
                        let selectedPhone = null;
                        data.phones.forEach(phone => {
                            const option = document.createElement('option');
                            option.value = phone.phone;
                            option.textContent = `${phone.phone} (${phone.type}${phone.is_primary ? ' - Primary' : ''})`;
                            if ((initialLoad && phone.phone === currentPhone) || (!initialLoad && phone.is_primary)) {
                                option.selected = true;
                                selectedPhone = phone.phone;
                            }
                            callerPhoneSelect.appendChild(option);
                        });
                        if (data.phones.length > 1) {
                            callerPhoneSelect.style.display = 'block';
                            callerPhoneField.style.display = 'none';
                            callerPhoneField.value = selectedPhone || callerPhoneSelect.value || currentPhone || '';
                        } else {
                            callerPhoneSelect.style.display = 'none';
                            callerPhoneField.style.display = 'block';
                            callerPhoneField.value = selectedPhone || data.phones[0].phone || currentPhone || '';
                        }
                    } else {
                        callerPhoneSelect.style.display = 'none';
                        callerPhoneField.style.display = 'block';
                        callerPhoneField.value = currentPhone || '';
                        callerPhoneField.placeholder = 'Enter phone number manually';
                    }
                })
                .catch(() => {
                    callerNameField.placeholder = 'Enter caller\'s name';
                    callerPhoneField.placeholder = 'Enter phone number';
                    callerPhoneSelect.style.display = 'none';
                    callerPhoneField.style.display = 'block';
                    callerPhoneField.value = currentPhone || '';
                });
        } else {
            callerNameField.value = '';
            callerNameField.placeholder = 'Enter caller\'s name';
            callerPhoneField.value = '';
            callerPhoneField.placeholder = 'Enter phone number';
            callerPhoneSelect.innerHTML = '<option value="">Select phone number...</option>';
            callerPhoneSelect.style.display = 'none';
            callerPhoneField.style.display = 'block';
        }
    }

    const preSelectedClientId = '{{ old('client_id', $callLog->client_id) }}';
    if (preSelectedClientId && $('#client_id').find(`option[value="${preSelectedClientId}"]`).length > 0) {
        $('#client_id').val(preSelectedClientId).trigger('change.select2');
        fetchClientContacts(preSelectedClientId, true);
    } else {
        document.getElementById('caller_phone_select').style.display = 'none';
        document.getElementById('caller_phone').style.display = 'block';
    }

    $('#client_id').on('change', function() {
        const clientId = $(this).val();
        fetchClientContacts(clientId, false);
    });

    document.getElementById('caller_phone_select').addEventListener('change', function() {
        document.getElementById('caller_phone').value = this.value;
    });

    function toggleFollowUpDate() {
        const followUpRequired = document.getElementById('follow_up_required').checked;
        document.getElementById('follow_up_date_group').style.display = followUpRequired ? '' : 'none';
    }
    toggleFollowUpDate(); // Initialize visibility
});
</script>
@endpush