@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users me-2"></i>Clients</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Client
        </a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($clients->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                    <tr>
                        <td>
                            <strong>{{ $client->company_name }}</strong>
                            @if(isset($client->services) && $client->services instanceof \Illuminate\Database\Eloquent\Collection && $client->services->count() > 0)
                                <br><small class="text-muted">
                                    @foreach($client->services->take(2) as $service)
                                        <span class="badge bg-light text-dark me-1">{{ $service->name }}</span>
                                    @endforeach
                                    @if($client->services->count() > 2)
                                        <span class="text-muted">+{{ $client->services->count() - 2 }} more</span>
                                    @endif
                                </small>
                            @endif
                        </td>
                        <td>{{ $client->user->name }}</td>
                        <td>
                            {{ $client->user->email }}
                            @if($client->emails->count() > 0)
                                <br><small class="text-muted">
                                    +{{ $client->emails->count() }} additional email(s)
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($client->phones->count() > 0)
                                {{ $client->phones->first()->phone }}
                                @if($client->phones->count() > 1)
                                    <br><small class="text-muted">
                                        +{{ $client->phones->count() - 1 }} more
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">No phone</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $client->status === 'active' ? 'success' : ($client->status === 'inactive' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td>{{ $client->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                        onclick="deleteClient({{ $client->id }})">
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
            {{ $clients->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No clients found</h4>
            <p class="text-muted">Start by adding your first client to the system.</p>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add First Client
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
                Are you sure you want to delete this client? This action cannot be undone.
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
function deleteClient(clientId) {
    const form = document.getElementById('deleteForm');
    form.action = '/clients/' + clientId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
@endsection
