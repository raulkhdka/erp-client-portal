@extends('layouts.app')

@section('title', 'My Forms')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .page-header h1 {
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }

    .page-header .subtitle {
        opacity: 0.9;
        margin-top: 0.5rem;
        font-size: 1rem;
    }

    .main-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1.5rem 2rem;
        border-radius: 20px 20px 0 0 !important;
    }

    .table-container {
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .table-modern {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #dee2e6;
    }

    .table-modern thead th {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-modern tbody td {
        padding: 1.25rem 1.5rem;
        border: 1px solid #dee2e6;
        border-top: none;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 500;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.9rem;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .sn-number {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .alert-modern {
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state h3 {
        margin-bottom: 1rem;
        color: #495057;
    }

    .pagination-modern .page-link {
        border: none;
        border-radius: 10px;
        margin: 0 2px;
        padding: 0.5rem 1rem;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .pagination-modern .page-link:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .field-count {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .table-responsive {
            border-radius: 15px;
        }

        .btn-action {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-12">
                <h1><i class="fas fa-file-alt me-3"></i>My Forms</h1>
                <p class="subtitle mb-0">View and fill out forms shared with you</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="main-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Shared Forms</h5>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary">{{ $forms->count() }} forms displayed</span>
                </div>
            </div>
        </div>

        <div class="table-container">
            @if($forms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th><i class="fas fa-file-signature me-2"></i>Form Name</th>
                                <th><i class="fas fa-align-left me-2"></i>Description</th>
                                <th><i class="fas fa-toggle-on me-2"></i>Status</th>
                                <th><i class="fas fa-list-ul me-2"></i>Fields</th>
                                <th><i class="fas fa-calendar me-2"></i>Created</th>
                                <th><i class="fas fa-eye me-2"></i>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forms as $index => $form)
                            <tr>
                                <td>
                                    <span class="sn-number">{{ $forms->firstItem() + $index }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $form->name }}</div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($form->description, 50) ?? 'No description' }}</span>
                                </td>
                                <td>
                                    @if ($form->is_active)
                                        <span class="badge badge-modern bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge badge-modern bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="field-count">{{ $form->fields->count() }} fields</span>
                                </td>
                                <td>
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $form->created_at->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted">{{ $form->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dynamic-forms.public-show', $form->id) }}"
                                           class="btn btn-info btn-action"
                                           target="_blank"
                                           title="Fill Form"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center p-4">
                    <nav aria-label="Forms pagination">
                        <ul class="pagination pagination-modern">
                            {{ $forms->links() }}
                        </ul>
                    </nav>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Shared Forms</h3>
                    <p class="mb-4">No forms have been shared with you yet. Contact your administrator for access.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const cards = document.querySelectorAll('.main-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection
