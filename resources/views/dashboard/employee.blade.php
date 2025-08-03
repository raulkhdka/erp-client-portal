@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-tie me-2"></i>Employee Dashboard</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Accessible Clients</h6>
            </div>
            <div class="card-body">
                @if($accessibleClients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Status</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accessibleClients as $client)
                            <tr>
                                <td>{{ $client->company_name }}</td>
                                <td>{{ $client->user->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $client->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($client->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($client->pivot->permissions)
                                        @foreach($client->pivot->permissions as $permission)
                                            <span class="badge bg-info me-1">{{ $permission }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center">You don't have access to any clients yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
                <p><strong>Department:</strong> {{ $employee->department ?? 'N/A' }}</p>
                <p><strong>Position:</strong> {{ $employee->position }}</p>
                <p><strong>Hire Date:</strong> {{ $employee->hire_date->format('M d, Y') }}</p>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
