@extends('layouts.app')

@section('title', 'Edit Call Log')

@section('breadcrumb')
    <a href="{{ route('admin.call-logs.index') }}">Call Logs</a>
    <span class="breadcrumb-item active">Edit Call Log</span>
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
            background: #f1f5f9;
        }

        .modal-content {
            background: #f1f5f9;
        }

        /* Modern card with slightly greyish background */
        .card-modern {
            position: relative;
            border: 1px solid #eef2f6;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            will-change: transform, box-shadow;
            backface-visibility: hidden;
            background: #f1f5f9;
        }

        .card-modern:hover,
        .card-modern:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.08);
            border-color: #e3eaf2;
        }

        .card-modern .card-body {
            padding: 1.5rem;
        }

        @media (min-width: 992px) {
            .card-modern .card-body {
                padding: 2rem;
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
            box-shadow: inset 0 0 0 1px rgba(16, 185, 129, .15);
        }

        /* Independent scroll shell for the form area (desktop) */
        .form-shell {
            --stack-offset: 160px;
        }

        @media (min-width: 992px) {
            .form-col {
                min-height: 0;
            }

            .form-shell {
                height: calc(100dvh - var(--stack-offset));
                display: flex;
                min-height: 0;
            }

            .form-scroll {
                flex: 1 1 auto;
                height: 100%;
                overflow: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 1rem;
                min-height: 0;
            }
        }

        /* Section title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 800;
            color: #10b981;
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
            background: #f1f5f9;
            border: 1px solid #eef2f6;
            color: #64748b;
            min-width: 42px;
            justify-content: center;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: 12px;
            border: 0.5px solid #000000;
            background: #f1f5f9;
            color: #000000;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
            background: #fff;
            color: #000000;
        }

        .form-label {
            color: #000000;
        }

        /* Sub-cards inside form */
        .subcard {
            border: 1px dashed #1f2937;
            border-radius: 14px;
            padding: 1rem;
            background: #f1f5f9;
        }

        /* Buttons */
        .btn-ghost-danger {
            border: 1px solid #fee2e2;
            color: #dc2626;
            background: #f1f5f9;
        }

        .btn-ghost-danger:hover {
            background: #ffeaea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
        }

        /* Form width: wider for better spacing */
        .form-wrap {
            margin-inline: auto;
        }

        @media (min-width: 992px) {
            .form-col {
                flex: 0 0 auto;
                width: 91.666667%;
            }
        }

        /* Tom Select theming to match cards/inputs */
        .ts-wrapper.form-select .ts-control,
        .ts-wrapper .ts-control {
            border-radius: 12px;
            border: 0.5px solid #000000;
            background: #f1f5f9;
            color: #000000;
            min-height: calc(1.5em + .75rem + 2px);
            padding-block: .25rem;
            padding-inline: .5rem;
        }

        .ts-wrapper.single.input-active .ts-control,
        .ts-wrapper.multi.input-active .ts-control,
        .ts-wrapper .ts-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .15);
            color: #000000;
        }

        .ts-dropdown {
            border: 0.5px solid #000000;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            overflow: hidden;
            background: #f1f5f9;
        }

        .ts-dropdown .active {
            background: #f0fdf4;
            color: #065f46;
        }

        .ts-control .item {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #000000;
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
            .card-modern {
                transition: none;
            }

            .form-control,
            .form-select,
            textarea.form-control {
                transition: none;
            }
        }

        /* Error alert styling */
        .alert-danger {
            border-radius: 12px;
            border: 2px solid #fee2e2;
            background: #fff1f2;
            color: #dc2626;
        }

        /* Checkbox label styling */
        .form-check-label {
            color: #10b981;
        }

        /* Checkbox styling */
        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-input {
            accent-color: #10b981;
        }

        /* Custom widths for Call Type and Call Date/Time */
        @media (min-width: 992px) {
            .call-type-container {
                flex: 0 0 auto;
                width: 60%;
            }

            .call-date-container {
                flex: 0 0 auto;
                width: 40%;
                margin-left: 0;
            }
        }

        /* Custom widths for Call Details section */
        @media (min-width: 992px) {
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
                                @if ($errors->any())
                                    <div class="alert alert-danger mb-4">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.call-logs.update', $callLog->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

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
                                                                    {{ old('employee_id', $callLog->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                                    <label for="client_id" class="form-label">Client <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                        <select
                                                            class="form-select client-select @error('client_id') is-invalid @enderror"
                                                            id="client_id" name="client_id" required>
                                                            <option value="">Select client...</option>
                                                            @foreach ($clients as $client)
                                                                <option value="{{ $client->id }}"
                                                                    data-contact-name="{{ $client->user ? $client->user->name ?? 'N/A' : 'N/A' }}"
                                                                    {{ old('client_id', $callLog->client_id) == $client->id ? 'selected' : '' }}>
                                                                    {{ $client->name }} ({{ $client->company_name ?? 'N/A' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('client_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-7 call-type-container">
                                                    <label for="call_type" class="form-label">Call Type <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-phone-alt"></i></span>
                                                        <select class="form-select @error('call_type') is-invalid @enderror"
                                                            id="call_type" name="call_type" required>
                                                            <option value="">Select type...</option>
                                                            <option value="incoming"
                                                                {{ old('call_type', $callLog->call_type) == 'incoming' ? 'selected' : '' }}>
                                                                Incoming</option>
                                                            <option value="outgoing"
                                                                {{ old('call_type', $callLog->call_type) == 'outgoing' ? 'selected' : '' }}>
                                                                Outgoing</option>
                                                        </select>
                                                        @error('call_type')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-5 call-date-container">
                                                    <label for="call_date" class="form-label">Call Date/Time <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="datetime-local"
                                                            class="form-control @error('call_date') is-invalid @enderror"
                                                            id="call_date" name="call_date"
                                                            value="{{ old('call_date', $callLog->call_date->format('Y-m-d\TH:i')) }}"
                                                            required>
                                                        <span class="input-group-text"><i
                                                                class="fas fa-calendar"></i></span>
                                                        @error('call_date')
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
                                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                        <input type="text"
                                                            class="form-control @error('caller_name') is-invalid @enderror"
                                                            id="caller_name" name="caller_name"
                                                            value="{{ old('caller_name', $callLog->caller_name) }}"
                                                            maxlength="255" placeholder="Enter caller's name">
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
                                                            value="{{ old('caller_phone', $callLog->caller_phone) }}"
                                                            maxlength="20" placeholder="Phone number">
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
                                                            value="{{ old('duration_minutes', $callLog->duration_minutes) }}"
                                                            min="0" placeholder="Call duration">
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
                                                            class="text-danger"
                                                            title="title for both call log and task">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-comment"></i></span>
                                                        <input type="text"
                                                            class="form-control @error('subject') is-invalid @enderror"
                                                            id="subject" name="subject"
                                                            value="{{ old('subject', $callLog->subject) }}" required
                                                            maxlength="255" placeholder="Brief subject of the call">
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
                                                                    {{ old('priority', $callLog->priority) == $value ? 'selected' : '' }}>
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
                                                                    {{ old('status', $callLog->status) == $value ? 'selected' : '' }}>
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
                                                    rows="4" required>{{ old('description', $callLog->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="notes" class="form-label">Additional Notes</label>
                                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $callLog->notes) }}</textarea>
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
                                                <div class="col-md-8">
                                                    <label for="follow_up_required" class="form-label">Task
                                                        Description</label>
                                                    <textarea class="form-control @error('follow_up_required') is-invalid @enderror" id="follow_up_required"
                                                        name="follow_up_required" rows="2">{{ old('follow_up_required', $callLog->tasks()->first() ? $callLog->tasks()->first()->description : '') }}</textarea>
                                                    @error('follow_up_required')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="follow_up_date" class="form-label">Next Call Date</label>
                                                    <div class="input-group">
                                                        <input type="date"
                                                            class="form-control @error('follow_up_date') is-invalid @enderror"
                                                            id="follow_up_date" name="follow_up_date"
                                                            value="{{ old('follow_up_date', $callLog->tasks()->first() && $callLog->tasks()->first()->due_date ? $callLog->tasks()->first()->due_date->format('Y-m-d') : '') }}"
                                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
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
                                                        {{ old('create_task', $callLog->tasks()->first() ? true : false) ? 'checked' : '' }}
                                                        {{ $callLog->status == \App\Models\CallLog::STATUS_RESOLVED ? 'disabled' : '' }}>
                                                    <label class="form-check-label"
                                                        for="create_task"><strong>Automatically create a task for this
                                                            call</strong></label>
                                                    <div class="form-text">This will create or update a task for follow-up. Not
                                                        created for "Resolved" calls.</div>
                                                </div>
                                            </div>
                                            <div style="flex: 0 0 40%;" id="assigned_to_container"
                                                style="display: {{ old('create_task', $callLog->tasks()->first() ? true : false) ? 'block' : 'none' }};">
                                                <label for="assigned_to" class="form-label assign-task-label">Assign Task To</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <select class="form-select @error('assigned_to') is-invalid @enderror"
                                                        id="assigned_to" name="assigned_to">
                                                        <option value="">Select employee...</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->id }}"
                                                                {{ old('assigned_to', $callLog->tasks()->first() ? $callLog->tasks()->first()->assigned_to : '') == $employee->id ? 'selected' : '' }}>
                                                                {{ $employee->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('assigned_to')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="form-text">Select an employee to assign the task.</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.call-logs.index') }}"
                                            class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary"><i
                                                class="fas fa-save me-2"></i>Update Call Log</button>
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
            // Initialize Tom Select for client_id
            new TomSelect('#client_id', {
                placeholder: 'Search and select client...',
                allowEmptyOption: true,
                create: false,
                render: {
                    option: function(data, escape) {
                        return `<div>${escape(data.text)}</div>`;
                    },
                    item: function(data, escape) {
                        return `<div>${escape(data.text)}</div>`;
                    }
                }
            });

            // Initialize Tom Select for employee_id
            new TomSelect('#employee_id', {
                placeholder: 'Select employee...',
                allowEmptyOption: true,
                create: false
            });

            // Initialize Tom Select for call_type
            new TomSelect('#call_type', {
                placeholder: 'Select type...',
                allowEmptyOption: true,
                create: false
            });

            // Initialize Tom Select for priority
            new TomSelect('#priority', {
                placeholder: 'Select priority...',
                allowEmptyOption: true,
                create: false
            });

            // Initialize Tom Select for status
            new TomSelect('#status', {
                placeholder: 'Select status...',
                allowEmptyOption: true,
                create: false
            });

            // Initialize Tom Select for assigned_to
            new TomSelect('#assigned_to', {
                placeholder: 'Select employee...',
                allowEmptyOption: true,
                create: false
            });

            // Toggle assigned_to visibility
            const createTaskCheckbox = document.getElementById('create_task');
            const assignedToContainer = document.getElementById('assigned_to_container');
            createTaskCheckbox.addEventListener('change', function() {
                assignedToContainer.style.display = this.checked ? 'block' : 'none';
            });

            // Initialize assigned_to visibility
            if (createTaskCheckbox.checked) {
                assignedToContainer.style.display = 'block';
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
        });
    </script>
@endpush