@extends('layouts.app')

@section('title', 'Create Service')

@section('breadcrumb')
    <div class="breadcrumb-nav d-flex align-items-center">
        <a href="{{ route('admin.services.index') }}" class="text-decoration-none text-dark">Services</a>
        <span class="mx-2">/</span>
        <span class="text-primary fw-bold">Create New Service</span>
    </div>
@endsection

@section('actions')
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.services.index') }}" class="btn btn-ghost-danger btn-sm rounded-pill shadow-sm btn-back">
            <i class="fas fa-arrow-left me-2"></i>Back to Services
        </a>
    </div>
@endsection

@section('styles')
    <style>
        :root {
            --bg-primary: #f1f5f9;
            --card-bg: #f8fafc;
            --card-border: #d1d9e6;
            --text-primary: #000000;
            --text-muted: #4b5563;
            --input-bg: #f8fafc;
            --input-border: #d1d9e6;
            --gradient-start: #10b981;
            --gradient-end: #059669;
            --subcard-border: #a1a9b6;
            --icon-bg: #eafaf3;
            --icon-color: #10b981;
            --btn-primary-bg: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --btn-primary-hover-bg: linear-gradient(135deg, #0ea5a0 0%, #047857 100%);
            --btn-primary-text: #ffffff;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .modal-content {
            background: var(--card-bg);
        }

        /* Modern card with darker border */
        .card-modern {
            position: relative;
            border: 2px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(2, 6, 23, 0.06);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            will-change: transform, box-shadow;
            backface-visibility: hidden;
            background: var(--card-bg);
        }
        .card-modern:hover,
        .card-modern:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.08);
            border-color: var(--input-border);
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

        /* Section title */
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }
        .section-title .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--icon-bg);
            color: var(--icon-color);
            display: grid;
            place-items: center;
        }
        .section-subtext {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Inputs with icons and focus ring */
        .input-group-text {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text-muted);
            min-width: 42px;
            justify-content: center;
        }
        .form-control, .form-select, textarea.form-control {
            border-radius: 12px;
            border: 2px solid var(--input-border);
            background: var(--input-bg);
            color: var(--text-primary);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .form-control:focus, .form-select:focus, textarea.form-control:focus {
            border-color: var(--gradient-start);
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, 0.15);
            background: #fff;
        }

        /* Sub-cards inside form */
        .subcard {
            border: 2px dashed var(--subcard-border);
            border-radius: 14px;
            padding: 1rem;
            background: var(--card-bg);
        }

        /* Buttons */
        .btn-ghost-danger, .btn-back {
            background: var(--btn-primary-bg);
            border: none;
            color: var(--btn-primary-text);
        }
        .btn-ghost-danger:hover, .btn-back:hover {
            background: var(--btn-primary-hover-bg);
            color: var(--btn-primary-text);
        }

        .btn-primary {
            background: var(--btn-primary-bg);
            border: none;
            color: var(--btn-primary-text);
        }
        .btn-primary:hover {
            background: var(--btn-primary-hover-bg);
            color: var(--btn-primary-text);
        }

        /* Breadcrumb */
        .breadcrumb-nav a {
            color: var(--text-primary);
        }
        .breadcrumb-nav .text-primary {
            color: var(--gradient-start) !important;
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
            <div class="card card-modern">
                <div class="card-body">
                    <form action="{{ route('admin.services.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <div class="section-title">
                                <div class="icon"><i class="fas fa-plus-circle"></i></div>
                                <div>
                                    <div>Create New Service</div>
                                    <div class="section-subtext">Define the service details and settings.</div>
                                </div>
                            </div>

                            <div class="subcard">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-concierge-bell"></i></span>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name"
                                                   name="name"
                                                   value="{{ old('name') }}"
                                                   required
                                                   maxlength="255"
                                                   placeholder="e.g. Premium Support">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                            <select class="form-select @error('type') is-invalid @enderror"
                                                    id="type"
                                                    name="type"
                                                    required
                                                    style="font-size: 0.9rem;">
                                                <option value="">Select type...</option>
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ old('type') == $i ? 'selected' : '' }}>
                                                        Type {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted d-block mt-2">Choose a type category from 0-10</small>
                                    </div>

                                    <div class="col-12">
                                        <textarea class="form-control @error('detail') is-invalid @enderror"
                                                  id="detail"
                                                  name="detail"
                                                  rows="5"
                                                  maxlength="1000"
                                                  placeholder="Enter detailed description of the service (optional)"
                                                  style="border-radius: 12px;">{{ old('detail') }}</textarea>
                                        @error('detail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2">Maximum 1000 characters</small>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                                   type="checkbox"
                                                   id="is_active"
                                                   name="is_active"
                                                   value="1"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active" style="font-size: 0.95rem;">
                                                Active Service
                                            </label>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted d-block mt-3">Only active services can be assigned to clients</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.services.index') }}" class="btn btn-ghost-danger btn-sm rounded-pill shadow-sm" style="font-size: 0.9rem;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm" style="font-size: 0.9rem;">
                                <i class="fas fa-save me-2"></i>Create Service
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
        counter.className = 'form-text text-end mt-2';
        counter.style.fontSize = '0.9rem';
        detailField.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - detailField.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.className = remaining < 100 ? 'form-text text-end mt-2 text-warning fw-bold' : 'form-text text-end mt-2 text-muted';
        }

        detailField.addEventListener('input', updateCounter);
        updateCounter();
    }
});
</script>
@endpush
@endsection