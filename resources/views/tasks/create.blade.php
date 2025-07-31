@extends('layouts.app')

@section('title', 'Create New Task')

@section('breadcrumb')
    <a href="{{ route('tasks.index') }}">Tasks</a>
    <span class="breadcrumb-item active">Create</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tasks
        </a>
    </div>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">

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

                        <form action="{{ route('tasks.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Task Information</h5>

                                    <div class="form-group">
                                        <label for="title">Task Title *</label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            value="{{ old('title') }}" required placeholder="Brief title for the task">
                                    </div>

                                    <div class="form-group">
                                        <label for="client_id">Client *</label>
                                        <select name="client_id" id="client_id" class="form-control client-select" required>
                                            <option value="">Select a client</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}"
                                                    {{ old('client_id', $selectedClientId ?? '') == $client->id ? 'selected' : '' }}>
                                                    {{ $client->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="assigned_to">Assign To</label>
                                        <select name="assigned_to" id="assigned_to" class="form-control">
                                            <option value="">Select an employee</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Leave empty to assign later</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="priority">Priority *</label>
                                        <select name="priority" id="priority" class="form-control" required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low
                                            </option>
                                            <option value="medium"
                                                {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium
                                            </option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High
                                            </option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                                Urgent</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status *</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>In Progress
                                            </option>
                                            <option value="3" {{ old('status') == 3 ? 'selected' : '' }}>On Hold
                                            </option>
                                            <option value="4" {{ old('status') == 4 ? 'selected' : '' }}>Escalated
                                            </option>
                                            <option value="5" {{ old('status') == 5 ? 'selected' : '' }}>Waiting for
                                                Client</option>
                                            <option value="6" {{ old('status') == 6 ? 'selected' : '' }}>Testing
                                            </option>
                                            <option value="7" {{ old('status') == 7 ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="8" {{ old('status') == 8 ? 'selected' : '' }}>Resolved
                                            </option>
                                            <option value="9" {{ old('status') == 9 ? 'selected' : '' }}>Backlog
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label>
                                        <input type="date" name="due_date" id="due_date" class="form-control"
                                            value="{{ old('due_date') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="call_log_id">Related Call Log (optional)</label>
                                        <select name="call_log_id" id="call_log_id" class="form-control">
                                            <option value="">-- No Call Log --</option>
                                            @foreach ($callLogs as $log)
                                                <option value="{{ $log->id }}"
                                                    {{ old('call_log_id') == $log->id ? 'selected' : '' }}>
                                                    {{ $log->id }} - {{ Str::limit($log->notes, 50) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Scheduling & Planning</h5>

                                    <!-- Date fields for status tracking -->
                                    <div class="form-group" id="started_at_group" style="display: none;">
                                        <label for="started_at">Started At</label>
                                        <input type="datetime-local" name="started_at" id="started_at" class="form-control"
                                            value="{{ old('started_at') }}">
                                    </div>

                                    <div class="form-group" id="completed_at_group" style="display: none;">
                                        <label for="completed_at">Completed At</label>
                                        <input type="datetime-local" name="completed_at" id="completed_at"
                                            class="form-control" value="{{ old('completed_at') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width Fields -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Description *</label>
                                        <textarea name="description" id="description" class="form-control" rows="5" required
                                            placeholder="Detailed description of what needs to be done...">{{ old('description') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                            placeholder="Any additional notes or comments...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Task
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Initialize Select2 for client dropdowns with proper styling
        $(document).ready(function() {
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select a client',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#client_id').parent(),
                // Add specific styling for the dropdown
                containerCss: {
                    'background-color': '#fff',
                    'border': '1px solid #ced4da'
                },
                dropdownCss: {
                    'background-color': '#fff',
                    'border': '1px solid #ced4da'
                }
            });
        });

        // Show/hide date fields based on status
        document.getElementById('status').addEventListener('change', function() {
            const status = parseInt(this.value);
            const startedGroup = document.getElementById('started_at_group');
            const completedGroup = document.getElementById('completed_at_group');

            // Show started_at field if status is in progress or higher
            if (status >= 2) {
                startedGroup.style.display = 'block';
            } else {
                startedGroup.style.display = 'none';
                document.getElementById('started_at').value = '';
            }

            // Show completed_at field if status is completed or resolved
            if (status >= 7) {
                completedGroup.style.display = 'block';
            } else {
                completedGroup.style.display = 'none';
                document.getElementById('completed_at').value = '';
            }
        });

        // Trigger the status change event on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('status').dispatchEvent(new Event('change'));
        });

        // Auto-fill started_at when status changes to in progress
        document.getElementById('status').addEventListener('change', function() {
            const status = parseInt(this.value);
            const startedAtField = document.getElementById('started_at');

            if (status === 2 && !startedAtField.value) { // In Progress
                const now = new Date();
                const formattedDate = now.toISOString().slice(0, 16);
                startedAtField.value = formattedDate;
            }
        });

        // Auto-fill completed_at when status changes to completed/resolved
        document.getElementById('status').addEventListener('change', function() {
            const status = parseInt(this.value);
            const completedAtField = document.getElementById('completed_at');

            if ((status === 7 || status === 8) && !completedAtField.value) { // Completed or Resolved
                const now = new Date();
                const formattedDate = now.toISOString().slice(0, 16);
                completedAtField.value = formattedDate;
            }
        });
    </script>
@endsection
