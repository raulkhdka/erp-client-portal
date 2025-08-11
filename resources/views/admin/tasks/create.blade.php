@extends('layouts.app')

@section('title', 'Create New Task')

@section('breadcrumb')
    <a href="{{ route('admin.tasks.index') }}">Tasks</a>
    <span class="breadcrumb-item active">Create New Task</span>
@endsection

@section('actions')
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Tasks
    </a>
@endsection

@section('styles')

    <style>
        /* Page background */
        body {
            background: #f1f5f9;
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
            background: #f8fafc;
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
            --stack-offset: 160px;
        }
        @media (min-width: 992px) {
            .form-col { min-height: 0; }
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
            border: 0.5px solid #000000;
            background: #f8fafc;
            color: #000000;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .form-control:focus, .form-select:focus, textarea.form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
            background: #fff;
            color: #000000;
        }

        /* Sub-cards inside form */
        .subcard {
            border: 1px dashed #1f2937;
            border-radius: 14px;
            padding: 1rem;
            background: #f8fafc;
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

        /* Tom Select theming to match cards/inputs */
        .ts-wrapper.form-select .ts-control,
        .ts-wrapper .ts-control {
            border-radius: 12px;
            border: 0.5px solid #000000;
            background: #f8fafc;
            color: #000000;
            min-height: calc(1.5em + .75rem + 2px);
            padding-block: .25rem;
            padding-inline: .5rem;
        }
        .ts-wrapper.single.input-active .ts-control,
        .ts-wrapper.multi.input-active .ts-control,
        .ts-wrapper .ts-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 .2rem rgba(16,185,129,.15);
            color: #000000;
        }
        .ts-dropdown {
            border: 0.5px solid #000000;
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
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.tasks.store') }}">
                                @csrf

                                {{-- Task Information --}}
                                <div class="mb-4">
                                    <div class="section-title">
                                        <div class="icon"><i class="fas fa-tasks"></i></div>
                                        <div>
                                            <div>Task Information</div>
                                            <div class="section-subtext">Provide details about the task.</div>
                                        </div>
                                    </div>
                                    <div class="subcard">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                        id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Website Redesign">
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                    <select name="client_id" id="client_id" class="form-select client-select @error('client_id') is-invalid @enderror" required>
                                                        <option value="">Select a client</option>
                                                        @foreach ($clients as $client)
                                                            <option value="{{ $client->id }}"
                                                                {{ old('client_id', $selectedClientId ?? '') == $client->id ? 'selected' : '' }}>
                                                                {{ $client->name }} ({{ $client->company_name }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('client_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="assigned_to" class="form-label">Assign To</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                                        <option value="">Select an employee</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->id }}"
                                                                {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                                                {{ $employee->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('assigned_to')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <small class="form-text text-muted">Leave empty to assign later</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-exclamation-circle"></i></span>
                                                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }} data-color="#22c55e">Low</option>
                                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }} data-color="#eab308">Medium</option>
                                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }} data-color="#f97316">High</option>
                                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }} data-color="#ef4444">Urgent</option>
                                                    </select>
                                                    @error('priority')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }} data-color="#f3f4f6">Pending</option>
                                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }} data-color="#3b82f6">In Progress</option>
                                                        <option value="3" {{ old('status') == 3 ? 'selected' : '' }} data-color="#6b7280">On Hold</option>
                                                        <option value="4" {{ old('status') == 4 ? 'selected' : '' }} data-color="#dc2626">Escalated</option>
                                                        <option value="5" {{ old('status') == 5 ? 'selected' : '' }} data-color="#eab308">Waiting for Client</option>
                                                        <option value="6" {{ old('status') == 6 ? 'selected' : '' }} data-color="#8b5cf6">Testing</option>
                                                        <option value="7" {{ old('status') == 7 ? 'selected' : '' }} data-color="#22c55e">Completed</option>
                                                        <option value="8" {{ old('status') == 8 ? 'selected' : '' }} data-color="#10b981">Resolved</option>
                                                        <option value="9" {{ old('status') == 9 ? 'selected' : '' }} data-color="#d1d5db">Backlog</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="call_log_id" class="form-label">Related Call Log (optional)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    <select name="call_log_id" id="call_log_id" class="form-select @error('call_log_id') is-invalid @enderror">
                                                        <option value="">-- No Call Log --</option>
                                                        @foreach ($callLogs as $log)
                                                            <option value="{{ $log->id }}"
                                                                {{ old('call_log_id') == $log->id ? 'selected' : '' }}>
                                                                {{ $log->id }} - {{ $log->caller_name ?? 'Unknown Caller' }} ({{ $log->call_date->format('Y-m-d') }} - {{ $log->subject ?? 'No Subject' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('call_log_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Scheduling & Planning --}}
                                <div class="mb-4">
                                    <div class="section-title">
                                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                                        <div>
                                            <div>Scheduling & Planning</div>
                                            <div class="section-subtext">Set dates for task tracking.</div>
                                        </div>
                                    </div>
                                    <div class="subcard">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="due_date" class="form-label">Due Date</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                                        id="due_date" name="due_date" value="{{ old('due_date') }}" placeholder="Select date">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    @error('due_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="completed_at_group">
                                                    <label for="completed_at" class="form-label">Completed At</label>
                                                    <div class="input-group">
                                                        <input type="datetime-local" class="form-control @error('completed_at') is-invalid @enderror"
                                                            id="completed_at" name="completed_at" value="{{ old('completed_at') }}">
                                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                        @error('completed_at')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description & Notes --}}
                                <div class="mb-4">
                                    <div class="section-title">
                                        <div class="icon"><i class="fas fa-file-alt"></i></div>
                                        <div>
                                            <div>Description & Notes</div>
                                            <div class="section-subtext">Provide detailed task information and additional notes.</div>
                                        </div>
                                    </div>
                                    <div class="subcard">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required
                                                    placeholder="Detailed description of what needs to be done...">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="notes" class="form-label">Notes</label>
                                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                                    placeholder="Any additional notes or comments...">{{ old('notes') }}</textarea>
                                                @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Task
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
            // Initialize TomSelect for client and employee dropdowns
            new TomSelect('#client_id', {
                create: false,
                placeholder: 'Select a client',
                allowEmptyOption: true,
                render: {
                    option: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    }
                }
            });

            new TomSelect('#assigned_to', {
                create: false,
                placeholder: 'Select an employee',
                allowEmptyOption: true,
                render: {
                    option: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    }
                }
            });

            new TomSelect('#call_log_id', {
                create: false,
                placeholder: '-- No Call Log --',
                allowEmptyOption: true,
                render: {
                    option: function(item, escape) {
                        return '<div class="py-1">' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    }
                }
            });

            new TomSelect('#priority', {
                create: false,
                placeholder: 'Select priority',
                allowEmptyOption: false,
                render: {
                    option: function(item, escape) {
                        const bgColor = item.element ? item.element.getAttribute('data-color') || '#f8fafc' : '#f8fafc';
                        return '<div style="background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px;">' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        const bgColor = item.element ? item.element.getAttribute('data-color') || '#f8fafc' : '#f8fafc';
                        return '<div style="background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px;">' + escape(item.text) + '</div>';
                    }
                }
            });

            new TomSelect('#status', {
                create: false,
                placeholder: 'Select status',
                allowEmptyOption: false,
                render: {
                    option: function(item, escape) {
                        const bgColor = item.element ? item.element.getAttribute('data-color') || '#f8fafc' : '#f8fafc';
                        return '<div style="background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px;">' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        const bgColor = item.element ? item.element.getAttribute('data-color') || '#f8fafc' : '#f8fafc';
                        return '<div style="background-color: ' + bgColor + '; padding: 4px 8px; border-radius: 4px;">' + escape(item.text) + '</div>';
                    }
                }
            });

            // Show/hide date fields based on status
            const statusSelect = document.getElementById('status');
            const completedGroup = document.getElementById('completed_at_group');
            const completedAtField = document.getElementById('completed_at');

            function updateDateFields() {
                const status = parseInt(statusSelect.value);

                // Show completed_at field if status is Completed or Resolved
                if (status >= {{ \App\Models\Task::STATUS_COMPLETED }}) {
                    completedGroup.style.display = 'block';
                    if ((status === {{ \App\Models\Task::STATUS_COMPLETED }} || status === {{ \App\Models\Task::STATUS_RESOLVED }}) && !completedAtField.value) {
                        const now = new Date();
                        const formattedDate = now.toISOString().slice(0, 16);
                        completedAtField.value = formattedDate;
                    }
                } else {
                    completedGroup.style.display = 'none';
                    completedAtField.value = '';
                }
            }

            statusSelect.addEventListener('change', updateDateFields);
            updateDateFields();
        });
    </script>
@endpush