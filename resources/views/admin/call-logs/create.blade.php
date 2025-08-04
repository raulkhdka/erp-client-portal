@extends('layouts.app')

@section('title', 'Record New Call')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}">Call Logs</a>
    <span class="breadcrumb-item active">Record New Call</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.call-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
        </a>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Record New Call</h3>
                    <a href="{{ route('admin.call-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>⬅️ Back to Call Logs
                    </a>
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

                    <form action="{{ route('admin.call-logs.store') }}" method="POST">
                        @csrf

                        {{-- Call Information --}}
                        <div class="row mb-4">
                            <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Call Information</h5></div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Assign to Employee</label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                                        <option value="">Select employee...</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-text">Leave empty to assign to yourself</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select class="form-select client-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                        <option value="">Select client...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                data-contact-name="{{ $client->user ? $client->user->name : $client->contact_person ?? 'N/A' }}"
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} ({{ $client->company_name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="call_type" class="form-label">Call Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('call_type') is-invalid @enderror" id="call_type" name="call_type" required>
                                        <option value="">Select type...</option>
                                        <option value="incoming" {{ old('call_type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                        <option value="outgoing" {{ old('call_type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                    </select>
                                    @error('call_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="call_date" class="form-label">Call Date/Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('call_date') is-invalid @enderror" id="call_date" name="call_date"
                                        value="{{ old('call_date', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('call_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Caller Info --}}
                        <div class="row mb-4">
                            <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Caller Information</h5></div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="caller_name" class="form-label">Caller Name</label>
                                    <input type="text" class="form-control @error('caller_name') is-invalid @enderror" id="caller_name"
                                        name="caller_name" value="{{ old('caller_name') }}" maxlength="255" placeholder="Enter caller's name">
                                    @error('caller_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="caller_phone_select" class="form-label">Caller Phone</label>
                                    <input type="text" class="form-control @error('caller_phone') is-invalid @enderror" id="caller_phone"
                                        name="caller_phone" value="{{ old('caller_phone') }}" maxlength="20" placeholder="Phone number">
                                    @error('caller_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes') }}" min="0" placeholder="Call duration">
                                    @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Call Details --}}
                        <div class="row mb-4">
                            <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Call Details</h5></div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject"
                                        value="{{ old('subject') }}" required maxlength="255" placeholder="Brief subject of the call">
                                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        @foreach (\App\Models\CallLog::getPriorityOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('priority', 'medium') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        @foreach (\App\Models\CallLog::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 1) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description & Notes --}}
                        <div class="mb-4">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Follow-up --}}
                        <div class="row mb-4">
                            <div class="col-12"><h5 class="border-bottom pb-2 mb-3">Follow-up</h5></div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="follow_up_required" class="form-label">Follow-up Required</label>
                                    <textarea class="form-control @error('follow_up_required') is-invalid @enderror" id="follow_up_required" name="follow_up_required" rows="2">{{ old('follow_up_required') }}</textarea>
                                    @error('follow_up_required') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('follow_up_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Task checkbox --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="create_task" name="create_task" value="1" {{ old('create_task', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_task"><strong>Automatically create a task for this call</strong></label>
                                <div class="form-text">This will create a task assigned to you for follow-up. Not created for "Resolved" calls.</div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.call-logs.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Record Call</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#client_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search and select client...',
        allowClear: true,
        width: '100%'
    });

    const statusSelect = document.getElementById('status');
    const createTaskCheckbox = document.getElementById('create_task');
    const resolvedStatus = '{{ \App\Models\CallLog::STATUS_RESOLVED }}';

    function checkTaskStatus() {
        if (statusSelect.value == resolvedStatus) {
            createTaskCheckbox.checked = false;
            createTaskCheckbox.disabled = true;
        } else {
            createTaskCheckbox.disabled = false;
        }
    }

    checkTaskStatus();
    statusSelect.addEventListener('change', checkTaskStatus);
});
</script>
@endpush