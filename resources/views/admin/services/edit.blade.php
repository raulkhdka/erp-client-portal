@extends('layouts.app')

@section('title', 'Edit Service')

@section('breadcrumb')
    <a href="{{ route('admin.services.index') }}">Services</a>
    <a href="{{ route('admin.services.show', $service) }}">{{ $service->name }}</a>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.services.show', $service) }}" class="btn btn-outline-primary">
            <i class="fas fa-eye me-2"></i>View
        </a>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Services
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.services.update', $service) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $service->name) }}"
                                           required
                                           maxlength="255"
                                           placeholder="Enter service name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Service Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror"
                                            id="type"
                                            name="type"
                                            required>
                                        <option value="">Select type...</option>
                                        @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('type', $service->type) == $i ? 'selected' : '' }}>
                                                Type {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Choose a type category from 0-10</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="detail" class="form-label">Service Detail</label>
                            <textarea class="form-control @error('detail') is-invalid @enderror"
                                      id="detail"
                                      name="detail"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Enter detailed description of the service (optional)">{{ old('detail', $service->detail) }}</textarea>
                            @error('detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Service
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Only active services can be assigned to clients</div>
                            </div>
                        </div>

                        @if($service->clients()->count() > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This service is currently assigned to {{ $service->clients()->count() }} client(s).
                                Changes will affect all existing assignments.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for detail field
    const detailField = document.getElementById('detail');
    if (detailField) {
        const maxLength = 1000;
        const counter = document.createElement('div');
        counter.className = 'form-text text-end mt-1';
        counter.style.fontSize = '0.875rem';
        detailField.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - detailField.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.className = remaining < 100 ? 'form-text text-end mt-1 text-warning' : 'form-text text-end mt-1 text-muted';
        }

        detailField.addEventListener('input', updateCounter);
        updateCounter();
    }
});
</script>
@endpush
@endsection
