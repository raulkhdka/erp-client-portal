@extends('layouts.app')
@section('title', $form->name)

@push('styles')
<style>
.form-container {
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    padding: 2rem;
    max-width: 800px;
    margin: 2rem auto;
}

.form-header {
    margin-bottom: 2rem;
}

.form-header h1 {
    font-size: 1.75rem;
    font-weight: 600;
}

.form-header .description {
    font-size: 1rem;
    color: #6c757d;
}

.form-field {
    margin-bottom: 1.5rem;
}

.form-field-label {
    font-weight: normal;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.form-field-help {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.form-field-required {
    color: #dc3545;
}

.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

.alert {
    max-width: 600px;
    margin: 0 auto 1rem;
}

.form-actions {
    display: flex;
    justify-content: center;
}

@media (max-width: 576px) {
    .form-container {
        padding: 1rem;
        margin: 1rem;
    }

    .form-header h1 {
        font-size: 1.5rem;
    }

    .form-actions {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center text-white">
        <div class="spinner-border mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div>Submitting...</div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Form Container -->
<div class="form-container">
    <!-- Error Alert -->
    <div class="alert alert-danger alert-dismissible fade" role="alert" id="errorAlert" style="display: none;">
        <ul class="error-message" id="errorList"></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Success Alert -->
    <div class="alert alert-success alert-dismissible fade" role="alert" id="successAlert" style="display: none;">
        <span id="successMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="form-header">
        <h1>{{ $form->name }}</h1>
        @if ($form->description)
            <div class="description">{{ $form->description }}</div>
        @endif
    </div>

    <form id="publicForm" method="POST" action="{{ route('dynamic-forms.submit', $form->id) }}" novalidate enctype="multipart/form-data">
        @csrf

        @foreach ($form->fields as $field)
            <div class="form-field">
                <label for="{{ $field->field_name }}" class="form-field-label">
                    {{ $field->field_label }}
                    @if ($field->is_required)
                        <span class="form-field-required">*</span>
                    @endif
                </label>

                @php
                    $requiredAttr = $field->is_required ? 'required' : '';
                    $options = $field->field_options ? json_decode($field->field_options, true) : [];
                    $nameAttr = $field->field_name;
                @endphp

                @if ($field->field_type === 'textarea')
                    <textarea class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" placeholder="{{ $field->placeholder }}" {{ $requiredAttr }} rows="4">{{ old($nameAttr) }}</textarea>
                @elseif ($field->field_type === 'select')
                    <select class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" {{ $requiredAttr }}>
                        <option value="">{{ $field->placeholder ?: 'Choose...' }}</option>
                        @foreach ($options as $option)
                            <option value="{{ $option }}" {{ old($nameAttr) == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                @elseif ($field->field_type === 'radio')
                    @foreach ($options as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="{{ $nameAttr }}" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ $requiredAttr }} {{ old($nameAttr) == $option ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                        </div>
                    @endforeach
                @elseif ($field->field_type === 'checkbox')
                    @foreach ($options as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{ $nameAttr }}[]" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ in_array($option, (array) old($nameAttr, [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                        </div>
                    @endforeach
                @elseif ($field->field_type === 'file')
                    <input type="file" class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" {{ $requiredAttr }}>
                @else
                    <input type="{{ $field->field_type }}" class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" placeholder="{{ $field->placeholder }}" {{ $requiredAttr }} value="{{ old($nameAttr) }}">
                @endif

                @if ($field->help_text)
                    <div class="form-field-help">{{ $field->help_text }}</div>
                @endif

                @error($nameAttr)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        @endforeach

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Submit Form
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Axios CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
class PublicFormHandler {
    constructor() {
        this.init();
        this.bindEvents();
    }

    init() {
        this.elements = {
            form: document.getElementById('publicForm'),
            loadingOverlay: document.getElementById('loadingOverlay'),
            errorAlert: document.getElementById('errorAlert'),
            errorList: document.getElementById('errorList'),
            successAlert: document.getElementById('safetyAlert'),
            successMessage: document.getElementById('successMessage'),
            toastContainer: document.getElementById('toastContainer')
        };

        // Configure Axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    bindEvents() {
        this.elements.form?.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    validateForm() {
        const errors = {};
        const inputs = this.elements.form.querySelectorAll('input[required], select[required], textarea[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                errors[input.name] = `${input.closest('.form-field').querySelector('.form-field-label').textContent} is required`;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Validate checkboxes (at least one checked if required)
        const checkboxGroups = this.elements.form.querySelectorAll('input[type="checkbox"][required]');
        checkboxGroups.forEach(group => {
            const name = group.name;
            const checked = this.elements.form.querySelectorAll(`input[name="${name}"]:checked`).length;
            if (!checked) {
                errors[name] = `${group.closest('.form-field').querySelector('.form-field-label').textContent} requires at least one selection`;
                group.closest('.form-field').querySelectorAll('input[type="checkbox"]').forEach(cb => cb.classList.add('is-invalid'));
            } else {
                group.closest('.form-field').querySelectorAll('input[type="checkbox"]').forEach(cb => cb.classList.remove('is-invalid'));
            }
        });

        // Validate radio groups
        const radioGroups = {};
        this.elements.form.querySelectorAll('input[type="radio"][required]').forEach(radio => {
            radioGroups[radio.name] = radioGroups[radio.name] || [];
            radioGroups[radio.name].push(radio);
        });

        Object.entries(radioGroups).forEach(([name, radios]) => {
            const checked = radios.some(radio => radio.checked);
            if (!checked) {
                errors[name] = `${radios[0].closest('.form-field').querySelector('.form-field-label').textContent} requires a selection`;
                radios.forEach(radio => radio.classList.add('is-invalid'));
            } else {
                radios.forEach(radio => radio.classList.remove('is-invalid'));
            }
        });

        return { isValid: Object.keys(errors).length === 0, errors };
    }

    async handleSubmit(e) {
        e.preventDefault();

        const validation = this.validateForm();
        if (!validation.isValid) {
            this.showErrors(validation.errors);
            this.showToast('Please fix the errors before submitting.', 'error');
            return;
        }

        this.hideErrors();
        this.showLoading();

        try {
            const formData = new FormData(this.elements.form);
            console.log('Submitting form data:', Object.fromEntries(formData)); // Debug form data

            const response = await axios.post(this.elements.form.action, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            console.log('Server response:', response.data); // Debug server response

            this.hideLoading();

            if (response.data.success) {
                this.showSuccess(response.data.message);
                this.showToast(response.data.message, 'success');

                if (response.data.redirect) {
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 2000);
                } else {
                    this.elements.form.reset();
                    this.showSuccess('Thank you for your submission!');
                }
            } else {
                this.showErrors(response.data.errors || ['Unexpected response format']);
                this.showToast('Failed to submit form.', 'error');
            }

        } catch (error) {
            this.hideLoading();
            console.error('Form submission error:', error, error.response?.data); // Detailed error logging

            let errors = ['An unexpected error occurred. Please try again.'];
            let message = 'An error occurred while submitting the form';
            if (error.response?.status === 422) {
                errors = Object.values(error.response.data.errors).flat();
                message = 'Validation failed. Please check your inputs.';
            } else if (error.response?.status === 500) {
                errors = error.response.data.errors || ['Server error occurred.'];
                message = error.response.data.message || 'Server error occurred.';
            } else if (error.code === 'ERR_NETWORK') {
                errors = ['Network error. Please check your connection.'];
                message = 'Network error. Please check your connection.';
            }

            this.showErrors(errors);
            this.showToast(message, 'error');
        }
    }

    showErrors(errors) {
        if (this.elements.errorList && this.elements.errorAlert) {
            this.elements.errorList.innerHTML = '';
            if (Array.isArray(errors)) {
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    this.elements.errorList.appendChild(li);
                });
            } else if (typeof errors === 'object') {
                Object.values(errors).flat().forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    this.elements.errorList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = errors;
                this.elements.errorList.appendChild(li);
            }

            this.elements.errorAlert.classList.add('show');
            this.elements.errorAlert.style.display = 'block';
            this.elements.errorAlert.scrollIntoView({ behavior: 'smooth' });
        }
    }

    hideErrors() {
        if (this.elements.errorAlert) {
            this.elements.errorAlert.classList.remove('show');
            this.elements.errorAlert.style.display = 'none';
        }
    }

    showSuccess(message) {
        if (this.elements.successAlert && this.elements.successMessage) {
            this.elements.successMessage.textContent = message;
            this.elements.successAlert.classList.add('show');
            this.elements.successAlert.style.display = 'block';
            this.elements.successAlert.scrollIntoView({ behavior: 'smooth' });
        }
    }

    showToast(message, type = 'info') {
        if (!this.elements.toastContainer) return;

        const toastId = 'toast_' + Date.now();
        const bgClass = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type.toLowerCase()] || 'bg-blue-500';

        const toastHTML = `
            <div class="toast ${bgClass} text-white" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body d-flex justify-content-between align-items-center">
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        this.elements.toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    showLoading() {
        if (this.elements.loadingOverlay) {
            this.elements.loadingOverlay.style.display = 'flex';
        }
    }

    hideLoading() {
        if (this.elements.loadingOverlay) {
            this.elements.loadingOverlay.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PublicFormHandler();
});
</script>
@endpush
