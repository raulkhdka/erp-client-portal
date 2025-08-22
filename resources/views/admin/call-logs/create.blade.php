@extends('layouts.app')

@section('title', 'Record New Call')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}">Call Logs</a>
    <span class="breadcrumb-item active">Record New Call</span>
@endsection

@section('actions')
    <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Call Logs
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

        /* Modern card with full width, no margin/padding, and rectangular shape */
        .card-modern {
            position: relative;
            border: 1px solid #eef2f6;
            border-radius: 0; /* Rectangular shape */
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            will-change: transform, box-shadow;
            backface-visibility: hidden;
            background: #f8fafc; /* slightly grey form background */
            width: 100%; /* Full width */
            margin: 0; /* No margins */
            padding: 0; /* No padding */
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
            border-radius: 0; /* Match rectangular shape */
        }
        .card-modern:hover::before,
        .card-modern:focus-within::before {
            box-shadow: inset 0 0 0 1px rgba(16,185,129,.15);
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
            background: white; /* Match employees view */
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
                flex: 0 0 auto;
                width: 83.333333%;
            }
        }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .card-modern { transition: none; }
            .form-control, .form-select, textarea.form-control { transition: none; }
        }

        /* Match employees view form styling */
        form>div {
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: white;
        }
        form .subcard {
            background: white;
        }

        /* Alert styling */
        .alert-danger {
            border-radius: 12px;
            border: 2px solid #fee2e2;
            background: #fff1f2;
            color: #dc2626;
        }

        /* Form check styling */
        .form-check-label {
            color: #0f172a;
        }
        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        .form-check-input {
            accent-color: #10b981;
        }

        /* Responsive column widths */
        @media (min-width: 992px) {
            .call-type-container {
                flex: 0 0 auto;
                width: 33.333333%;
            }
            .call-date-container,
            .call-time-container {
                flex: 0 0 auto;
                width: 33.333333%;
            }
            .follow-up-date-container {
                flex: 0 0 auto;
                width: 50%;
            }
            .subject-container {
                flex: 0 0 auto;
                width: 50%;
            }
            .priority-container,
            .status-container {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        .assign-task-label {
            margin-right: 20px;
        }

        /* Ensure date picker calendar is visible */
        .nepali-date-picker, .ndp-calendar, .nepali-datepicker {
            z-index: 9999 !important;
            position: absolute;
        }
    </style>
@endsection

@section('content')
<div>
    <div class="card card-modern">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
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
                <div class="mb-4">
                    <div class="section-title">
                        <div class="icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <div>Call Information</div>
                            <div class="section-subtext">Details about the call and assignment.</div>
                        </div>
                    </div>
                    <div class="subcard">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Assign to Employee</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <select
                                        class="form-select @error('employee_id') is-invalid @enderror"
                                        id="employee_id" name="employee_id">
                                        <option value="">Select employee...</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Leave empty to assign to yourself</div>
                            </div>

                            <div class="col-md-6">
                                <label for="client_id" class="form-label">Client</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <select
                                        class="form-select @error('client_id') is-invalid @enderror"
                                        id="client_id" name="client_id">
                                        <option value="">Select client (optional)...</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                data-contact-name="{{ $client->user ? $client->user->name ?? 'N/A' : 'N/A' }}"
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                                ({{ $client->company_name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 call-type-container">
                                <label for="call_type" class="form-label">Call Type <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i
                                            class="fas fa-phone-alt"></i></span>
                                    <select class="form-select @error('call_type') is-invalid @enderror"
                                        id="call_type" name="call_type" required>
                                        <option value="">Select type...</option>
                                        <option value="incoming"
                                            {{ old('call_type') == 'incoming' ? 'selected' : '' }}>
                                            Incoming</option>
                                        <option value="outgoing"
                                            {{ old('call_type') == 'outgoing' ? 'selected' : '' }}>
                                            Outgoing</option>
                                    </select>
                                    @error('call_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 call-date-container">
                                <label for="call_date" class="form-label">Call Date <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control nepali-date @error('call_date') is-invalid @enderror"
                                        id="call_date" name="call_date" value="{{ old('call_date') }}"
                                        required placeholder="YYYY-MM-DD" autocomplete="off" data-mode="dark" data-offset="5">
                                    <span class="input-group-text"><i
                                            class="fas fa-calendar"></i></span>
                                    @error('call_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 call-time-container">
                                <label for="call_time" class="form-label">Call Time <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="time"
                                        class="form-control @error('call_time') is-invalid @enderror"
                                        id="call_time" name="call_time"
                                        value="{{ old('call_time', now()->format('H:i')) }}" required>
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    @error('call_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Caller Info --}}
                <div class="mb-4">
                    <div class="section-title">
                        <div class="icon"><i class="fas fa-user-circle"></i></div>
                        <div>Caller Information</div>
                    </div>
                    <div class="subcard">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="caller_name" class="form-label">Caller Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i
                                            class="fas fa-id-card"></i></span>
                                    <input type="text"
                                        class="form-control @error('caller_name') is-invalid @enderror"
                                        id="caller_name" name="caller_name"
                                        value="{{ old('caller_name') }}" maxlength="255"
                                        placeholder="Enter caller's name">
                                    @error('caller_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="caller_phone" class="form-label">Caller Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text"
                                        class="form-control @error('caller_phone') is-invalid @enderror"
                                        id="caller_phone" name="caller_phone"
                                        value="{{ old('caller_phone') }}" maxlength="20"
                                        placeholder="Enter phone number">
                                    @error('caller_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="duration_minutes" class="form-label">Duration
                                    (minutes)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="number"
                                        class="form-control @error('duration_minutes') is-invalid @enderror"
                                        id="duration_minutes" name="duration_minutes"
                                        value="{{ old('duration_minutes') }}" min="0"
                                        placeholder="Call duration">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Call Details --}}
                <div class="mb-4">
                    <div class="section-title">
                        <div class="icon"><i class="fas fa-info-circle"></i></div>
                        <div>Call Details</div>
                    </div>
                    <div class="subcard">
                        <div class="row g-3">
                            <div class="col-md-6 subject-container">
                                <label for="subject" class="form-label">Subject <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i
                                            class="fas fa-comment"></i></span>
                                    <input type="text"
                                        class="form-control @error('subject') is-invalid @enderror"
                                        id="subject" name="subject" value="{{ old('subject') }}"
                                        required maxlength="255"
                                        placeholder="Brief subject of the call">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3 priority-container">
                                <label for="priority" class="form-label">Priority <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i
                                            class="fas fa-exclamation-circle"></i></span>
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority" name="priority" required>
                                        @foreach (\App\Models\CallLog::getPriorityOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('priority', 'medium') == $value ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3 status-container">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                        @foreach (\App\Models\CallLog::getStatusOptions() as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('status', 1) == $value ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description & Notes --}}
                <div class="mb-4">
                    <div class="section-title">
                        <div class="icon"><i class="fas fa-file-alt"></i></div>
                        <div>Description & Notes</div>
                    </div>
                    <div class="subcard">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Follow-up --}}
                <div class="mb-4">
                    <div class="section-title">
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                        <div>Task Creation</div>
                    </div>
                    <div class="subcard">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="follow_up_required" class="form-label">Task
                                    Description</label>
                                <textarea class="form-control @error('follow_up_required') is-invalid @enderror" id="follow_up_required"
                                    name="follow_up_required" rows="2">{{ old('follow_up_required') }}</textarea>
                                @error('follow_up_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 follow-up-date-container">
                                <label for="follow_up_date" class="form-label">Next Call
                                    Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control nepali-date @error('follow_up_date') is-invalid @enderror"
                                        id="follow_up_date" name="follow_up_date"
                                        value="{{ old('follow_up_date') }}" placeholder="YYYY-MM-DD"
                                        autocomplete="off" data-mode="dark" data-offset="5">
                                    <span class="input-group-text"><i
                                            class="fas fa-calendar"></i></span>
                                    @error('follow_up_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Task checkbox and Assigned To field --}}
                <div class="mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <div style="flex: 0 0 60%;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="create_task"
                                    name="create_task" value="1"
                                    {{ old('create_task', true) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="create_task"><strong>Automatically create a task for this
                                        call</strong></label>
                                <div class="form-text">This will create a task for follow-up. Not
                                    created for "Resolved" calls.</div>
                            </div>
                        </div>
                        <div style="flex: 0 0 40%;" id="assigned_to_container"
                            style="display: {{ old('create_task', true) ? 'block' : 'none' }};">
                            <label for="assigned_to" class="form-label assign-task-label">Assign
                                Task To</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <select class="form-select @error('assigned_to') is-invalid @enderror"
                                    id="assigned_to" name="assigned_to">
                                    <option value="">Select employee...</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            {{ old('assigned_to', old('employee_id')) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Defaults to call log's assigned employee.</div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.call-logs.index') }}"
                        class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i
                            class="fas fa-save me-2"></i>Record Call</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync assigned_to with employee_id
            const employeeIdSelect = document.getElementById('employee_id');
            const assignedToSelect = document.getElementById('assigned_to');
            let assignedToManuallyChanged = false;

            assignedToSelect.addEventListener('change', function() {
                assignedToManuallyChanged = true;
            });

            employeeIdSelect.addEventListener('change', function() {
                if (createTaskCheckbox.checked && !assignedToManuallyChanged) {
                    assignedToSelect.value = this.value || '';
                }
            });

            // Toggle assigned_to visibility
            const createTaskCheckbox = document.getElementById('create_task');
            const assignedToContainer = document.getElementById('assigned_to_container');
            createTaskCheckbox.addEventListener('change', function() {
                assignedToContainer.style.display = this.checked ? 'block' : 'none';
                if (this.checked && !assignedToManuallyChanged) {
                    assignedToSelect.value = employeeIdSelect.value || '';
                }
            });

            // Initialize assigned_to value
            if (createTaskCheckbox.checked) {
                assignedToContainer.style.display = 'block';
                if (!assignedToManuallyChanged) {
                    assignedToSelect.value = employeeIdSelect.value || '';
                }
            }

            // Handle status and task checkbox
            const statusSelect = document.getElementById('status');
            const resolvedStatus = '{{ \App\Models\CallLog::STATUS_RESOLVED }}';

            function checkTaskStatus() {
                if (statusSelect.value == resolvedStatus) {
                    createTaskCheckbox.checked = false;
                    createTaskCheckbox.disabled = true;
                    assignedToContainer.style.display = 'none';
                } else {
                    createTaskCheckbox.disabled = false;
                    assignedToContainer.style.display = createTaskCheckbox.checked ? 'block' : 'none';
                }
            }

            checkTaskStatus();
            statusSelect.addEventListener('change', checkTaskStatus);

            // Populate caller_name based on client selection
            const clientSelect = document.getElementById('client_id');
            clientSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('caller_name').value = selectedOption && selectedOption.value ? (selectedOption.dataset.contactName || '') : '';
            });
        });
    </script>
@endpush