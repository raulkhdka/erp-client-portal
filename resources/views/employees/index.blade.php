@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-tie me-2"></i>Employees</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Employee
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($employees->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <strong>{{ $employee->employee_id }}</strong>
                        </td>
                        <td>{{ $employee->user->name }}</td>
                        <td>{{ $employee->user->email }}</td>
                        <td>
                            {{ $employee->department ?? 'Not specified' }}
                        </td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->hire_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : ($employee->status === 'inactive' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                        onclick="deleteEmployee({{ $employee->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $employees->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No employees found</h4>
            <p class="text-muted">Start by adding your first employee to the system.</p>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add First Employee
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this employee? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteEmployee(employeeId) {
    const form = document.getElementById('deleteForm');
    form.action = '/employees/' + employeeId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
@endsection
