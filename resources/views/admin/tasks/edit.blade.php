@extends('layouts.app')

@section('title', 'Edit Task')

@section('breadcrumb')
    <span class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></span>
    <span class="breadcrumb-item active">Edit Task</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .modal-content {
            background: #f8fafc;
        }
        .card-modern {
            border: 1px solid #eef2f6;
            border-radius: 0;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
            background: #f8fafc;
            padding: 1.25rem;
        }
        .card-modern:hover, .card-modern:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.08);
            border-color: #e3eaf2;
        }
        @media (min-width: 992px) {
            .card-modern {
                padding: 1.5rem;
            }
        }
        .card-modern::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: 0;
        }
        .card-modern:hover::before, .card-modern:focus-within::before {
            box-shadow: inset 0 0 0 1px rgba(16,185,129,.15);
        }
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
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc2626;
        }
        .error-messages {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .subcard {
            border: 1px dashed #e5e7eb;
            border-radius: 14px;
            padding: 1rem;
            background: white;
        }
        form > div {
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .btn-ghost-danger {
            border: 1px solid #fee2e2;
            color: #dc2626;
            background: #f8fafc;
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
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow animate-slide-up">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-edit text-purple-600 me-2"></i>Edit Task
                        </h3>
                    </div>
                    <div class="card-body form-shell">
                        <div class="form-scroll">
                            <form id="editTaskForm" action="{{ route('admin.tasks.update', $task->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-modern">
                                    @if ($task->is_overdue)
                                        <div class="alert alert-danger mt-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>This task is overdue!
                                        </div>
                                    @endif
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
                                                    <label for="edit_title" class="form-label">Task Title <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                                        <input type="text" name="title" id="edit_title" class="form-control" value="{{ old('title', $task->title) }}" required placeholder="e.g. Website Redesign">
                                                        @error('title')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                        <select name="client_id" id="edit_client_id" class="form-select" required>
                                                            <option value="">Select a client</option>
                                                            @foreach ($clients as $client)
                                                                <option value="{{ $client->id }}" {{ old('client_id', $task->client_id) == $client->id ? 'selected' : '' }}>
                                                                    {{ $client->name }} ({{ $client->company_name }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('client_id')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_assigned_to" class="form-label">Assign To</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                        <select name="assigned_to" id="edit_assigned_to" class="form-select">
                                                            <option value="">Select an employee</option>
                                                            @foreach ($employees as $employee)
                                                                <option value="{{ $employee['id'] }}" {{ old('assigned_to', $task->assigned_to) == $employee['id'] ? 'selected' : '' }}>
                                                                    {{ $employee['name'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('assigned_to')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <small class="form-text text-muted">Leave empty to keep unassigned</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-exclamation-circle"></i></span>
                                                        <select name="priority" id="edit_priority" class="form-select" required>
                                                            @foreach (\App\Models\Task::getPriorityOptions() as $value => $label)
                                                                <option value="{{ $value }}" {{ old('priority', $task->priority) == $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('priority')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                        <select name="status" id="edit_status" class="form-select" required>
                                                            @foreach (\App\Models\Task::getStatusOptions() as $value => $label)
                                                                <option value="{{ $value }}" {{ old('status', $task->status) == $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('status')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <small class="form-text text-muted">Current: <span class="badge bg-{{ $task->status_color }}">{{ $task->status_label }}</span></small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="edit_call_log_id" class="form-label">Related Call Log (optional)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                        <select name="call_log_id" id="edit_call_log_id" class="form-select">
                                                            <option value="">-- No Call Log --</option>
                                                            @foreach ($callLogs as $log)
                                                                <option value="{{ $log->id }}" {{ old('call_log_id', $task->call_log_id) == $log->id ? 'selected' : '' }}>
                                                                    {{ $log->id }} - {{ $log->subject }} ({{ $log->call_date ? \Carbon\Carbon::parse($log->call_date)->format('Y-m-d') : 'N/A' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('call_log_id')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                                    <label for="edit_due_date" class="form-label">Due Date</label>
                                                    <div class="input-group">
                                                        <input type="text" name="due_date" id="edit_due_date" class="form-control nepali-date" data-offset="5" data-mode="dark" value="{{ old('due_date', $task->due_date_formatted) }}" placeholder="Select date" autocomplete="off">
                                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                        @error('due_date')
                                                            <div class="error-messages">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                                    <label for="edit_description" class="form-label">Description <span class="text-danger">*</span></label>
                                                    <textarea name="description" id="edit_description" class="form-control" rows="5" required placeholder="Detailed description of what needs to be done...">{{ old('description', $task->description) }}</textarea>
                                                    @error('description')
                                                        <div class="error-messages">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label for="edit_notes" class="form-label">Notes</label>
                                                    <textarea name="notes" id="edit_notes" class="form-control" rows="3" placeholder="Any additional notes or comments...">{{ old('notes', $task->notes) }}</textarea>
                                                    @error('notes')
                                                        <div class="error-messages">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-ghost-danger">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Task
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize Nepali date picker for due_date
        document.addEventListener('DOMContentLoaded', function () {

            // Form submission handling with Axios
            const form = document.getElementById('editTaskForm');
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';

                // Clear previous error messages and invalid states
                form.querySelectorAll('.error-messages').forEach(el => el.textContent = '');
                form.querySelectorAll('.form-control.is-invalid, .form-select.is-invalid')
                    .forEach(el => el.classList.remove('is-invalid'));

                // Perform client-side validation
                const requiredFields = form.querySelectorAll('[required]');
                let hasErrors = false;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        hasErrors = true;
                        field.classList.add('is-invalid');
                        const errorDiv = field.parentElement.querySelector('.error-messages') ||
                            field.parentElement.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('error-messages')) {
                            errorDiv.textContent = `${field.name.charAt(0).toUpperCase() + field.name.slice(1)} is required.`;
                        }
                    }
                });

                if (hasErrors) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Task';
                    showToast('error', 'Please fill in all required fields.');
                    return;
                }

                // Collect form data
                const formData = new FormData(form);
                // Ensure _method is included for PUT request
                formData.append('_method', 'PUT');

                // Send Ajax request with Axios
                axios({
                    method: 'post', // Laravel expects POST for PUT with _method
                    url: form.action,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                        document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => {
                    // Success response
                    showToast('success', response.data.message || 'Task updated successfully!');
                    // Optionally redirect to tasks index after a delay
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.tasks.index') }}";
                    }, 1500);
                })
                .catch(error => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Task';

                    if (error.response && error.response.status === 422) {
                        // Handle validation errors
                        const errors = error.response.data.errors;
                        Object.keys(errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = input.parentElement.querySelector('.error-messages') ||
                                    input.parentElement.nextElementSibling;
                                if (errorDiv && errorDiv.classList.contains('error-messages')) {
                                    errorDiv.textContent = errors[field][0];
                                }
                            }
                        });
                        showToast('error', 'Please correct the errors in the form.');
                    } else {
                        // Handle other errors
                        showToast('error', error.response?.data?.message || 'Failed to update task.');
                    }
                });
            });

            // Reset form state on input change
            form.querySelectorAll('.form-control, .form-select').forEach(input => {
                input.addEventListener('change', function () {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.parentElement.querySelector('.error-messages') ||
                        this.parentElement.nextElementSibling;
                    if (errorDiv && errorDiv.classList.contains('error-messages')) {
                        errorDiv.textContent = '';
                    }
                });
            });
        });
    </script>
@endpush