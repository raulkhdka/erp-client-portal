@extends('layouts.app')

@section('title', 'Share {{ $form->name }}')

@push('styles')
<style>
    .share-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border: none;
        margin: 2rem auto;
        max-width: 600px;
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

    .alert-modern {
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: 1rem auto;
    }

    .alert-success {
        background: linear-gradient(45deg, #56ab2f, #a8e6cf);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(45deg, #f093fb, #f5576c);
        color: white;
    }

    .form-actions {
        display: flex;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        position: relative;
        overflow: hidden;
        min-width: 150px;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
        color: white;
        text-decoration: none;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:active {
        transform: translateY(-1px) scale(1.02);
    }

    .btn-outline-secondary {
        border: 2px solid #e9ecef;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .share-container {
            padding: 1rem;
            margin: 1rem;
        }

        .btn-primary {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            min-width: 120px;
        }

        .btn-outline-secondary {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
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
        <div>Sharing...</div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Share Container -->
<div class="share-container">
    <!-- Error Alert -->
    <div class="alert-modern alert-danger alert-dismissible fade" role="alert" id="errorAlert" style="display: none;">
        <ul class="error-message" id="errorList"></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Success Alert -->
    <div class="alert-modern alert-success alert-dismissible fade" role="alert" id="successAlert" style="display: none;">
        <span id="successMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">
            <i class="fas fa-share-alt me-2" style="color: #667eea;"></i>
            Share {{ $form->name }}
        </h1>
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>

    <form id="shareForm" method="POST" action="{{ route('dynamic-forms.send', $form->id) }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="user_id" class="form-label fw-bold">Select Client <span class="text-danger">*</span></label>
            <select class="form-control filter-select" id="user_id" name="user_id" required>
                <option value="">Choose a client...</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>{{ $client->name }} ({{ $client->email }})</option>
                @endforeach
            </select>
            @error('user_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="message" class="form-label fw-bold">Optional Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Add a personalized message...">{{ old('message') }}</textarea>
            @error('message')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Share Form
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Axios CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
class ShareFormHandler {
    constructor() {
        this.init();
        this.bindEvents();
    }

    init() {
        this.elements = {
            form: document.getElementById('shareForm'),
            loadingOverlay: document.getElementById('loadingOverlay'),
            errorAlert: document.getElementById('errorAlert'),
            errorList: document.getElementById('errorList'),
            successAlert: document.getElementById('successAlert'),
            successMessage: document.getElementById('successMessage'),
            toastContainer: document.getElementById('toastContainer')
        };

        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    bindEvents() {
        this.elements.form?.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    validateForm() {
        const errors = {};
        const userInput = this.elements.form.querySelector('#user_id');

        if (!userInput.value) {
            errors.user_id = 'Please select a client';
            userInput.classList.add('is-invalid');
        } else {
            userInput.classList.remove('is-invalid');
        }

        return { isValid: Object.keys(errors).length === 0, errors };
    }

    async handleSubmit(e) {
        e.preventDefault();

        const validation = this.validateForm();
        if (!validation.isValid) {
            this.showErrors(validation.errors);
            this.showToast('Please fix the errors before sharing.', 'error');
            return;
        }

        this.hideErrors();
        this.showLoading();

        try {
            const formData = new FormData(this.elements.form);
            console.log('Sending form data:', Object.fromEntries(formData));

            const response = await axios.post(this.elements.form.action, formData);

            console.log('Server response:', response.data);

            this.hideLoading();

            if (response.data.success) {
                this.showSuccess(response.data.message);
                this.showToast(response.data.message, 'success');

                if (response.data.redirect) {
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 2000);
                }
            } else {
                this.showErrors(response.data.errors || ['Unexpected response format']);
                this.showToast('Failed to share form.', 'error');
            }

        } catch (error) {
            this.hideLoading();
            console.error('Form share error:', error, error.response?.data);

            let errors = ['An unexpected error occurred. Please try again.'];
            let message = 'An error occurred while sharing the form';
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
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type.toLowerCase()] || 'bg-info';

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
    new ShareFormHandler();
});
</script>
@endpush