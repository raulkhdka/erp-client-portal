@extends('layouts.app')

@section('title', 'My Assigned Employees')

{{-- Define the navbar-title section here to display "My Assigned Employees" in the top navigation bar --}}
@section('page-navbar-title')
    <h5 class="mb-0"><i class="fas fa-users me-2"></i>My Assigned Employees</h5>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- The card-header below can be optional if the title is now solely in the navbar --}}
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Assigned Employees List</h5> {{-- You can keep a more subtle title here, or remove it if redundant --}}
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($assignedEmployees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Access Status</th>
                                        <th>Assigned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedEmployees as $employee)
                                        <tr>
                                            <td>
                                                <strong>{{ $employee->user->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $employee->user->email ?? '' }}">{{ $employee->user->email ?? 'N/A' }}</a>
                                            </td>
                                            <td>
                                                {{-- Using the 'role' attribute from your User model --}}
                                                <span class="badge bg-secondary">{{ ucfirst($employee->user->role ?? 'N/A') }}</span>
                                            </td>
                                            <td>
                                                @if($employee->pivot->is_active)
                                                    <span class="badge bg-success">Active Access</span>
                                                @else
                                                    <span class="badge bg-warning">Inactive Access</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $employee->pivot->access_granted_date ? \Carbon\Carbon::parse($employee->pivot->access_granted_date)->format('M d, Y') : 'N/A' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $assignedEmployees->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No employees currently assigned to your account.</h5>
                            <p class="text-muted">If you believe this is an error, please contact our team.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Any specific JavaScript for this page can go here
});
</script>
@endpush
@endsection