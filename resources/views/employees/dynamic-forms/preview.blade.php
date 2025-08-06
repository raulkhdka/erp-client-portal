@php
    // Determine if this is an AJAX request for modal content
    $isAjax = Request::ajax();
    // Check if the form was created by the current employee
    $isFormOwner = Auth::user()->isEmployee() && $form->employee_id === Auth::user()->id;
@endphp

@if (!$isAjax)
    @extends('layouts.app')

    @section('title')
        {{ $form->name }}
    @endsection

    @push('styles')
        <style>
            /* Hide sidebar specifically for this view */
            #sidebar, .sidebar-wrapper, .sidebar-menu {
                display: none !important;
            }

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
                min-width: 300px;
            }

            .alert {
                max-width: 600px;
                margin: 0 auto 1rem;
                border-radius: 10px;
            }

            .form-actions {
                display: flex;
                justify-content: center;
                gap: 1rem;
            }

            .btn-back {
                border-radius: 10px;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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

            .modal-body .form-container {
                margin: 0;
                border: none;
                box-shadow: none;
                padding: 0;
            }
        </style>
    @endpush
@endif

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
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>

    <!-- Form Container -->
    <div class="form-container">
        <div class="form-header">
            <h1 id="formTitle">{{ $form->name }}</h1>
            @if ($form->description)
                <div class="description" id="formDescription">{{ $form->description }}</div>
            @endif
        </div>

        @if ($form->exists)
            @if ($hasSubmitted)
                <div class="alert alert-info" role="alert">
                    You have already submitted this form. Thank you for your response!
                </div>
                @if (!$isAjax)
                    <div class="form-actions">
                        <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary btn-back" aria-label="Back to forms list">
                            <i class="fas fa-arrow-left me-2"></i>Back to Forms
                        </a>
                    </div>
                @endif
            @else
                @if (Auth::user()->isEmployee() && !$isFormOwner)
                    <div class="alert alert-warning" role="alert">
                        You are not authorized to submit this form as it was not created by you.
                    </div>
                    @if (!$isAjax)
                        <div class="form-actions">
                            <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary btn-back" aria-label="Back to forms list">
                                <i class="fas fa-arrow-left me-2"></i>Back to Forms
                            </a>
                        </div>
                    @endif
                @else
                    <form id="publicForm" method="POST" action="{{ route('employees.dynamic-forms.submit', ['form' => $form->id]) }}" novalidate enctype="multipart/form-data" role="form" aria-labelledby="formTitle">
                        @csrf
                        @foreach ($form->fields as $field)
                            <div class="form-field">
                                <label for="{{ $field->field_name }}" class="form-field-label">
                                    {{ $field->field_label }}
                                    @if ($field->is_required)
                                        <span class="form-field-required" aria-hidden="true">*</span>
                                    @endif
                                </label>

                                @php
                                    $requiredAttr = $field->is_required ? 'required' : '';
                                    $options = $field->field_options ? json_decode($field->field_options, true) : [];
                                    $nameAttr = $field->field_name;
                                @endphp

                                @if ($field->field_type === 'textarea')
                                    <textarea class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" placeholder="{{ $field->placeholder }}" {{ $requiredAttr }} rows="4" aria-describedby="{{ $field->field_name }}_help">{{ old($nameAttr) }}</textarea>
                                @elseif ($field->field_type === 'select')
                                    <select class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field->field_name }}_help">
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
                                    <input type="file" class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field->field_name }}_help">
                                @else
                                    <input type="{{ $field->field_type }}" class="form-control" id="{{ $field->field_name }}" name="{{ $nameAttr }}" placeholder="{{ $field->placeholder }}" {{ $requiredAttr }} value="{{ old($nameAttr) }}" aria-describedby="{{ $field->field_name }}_help">
                                @endif

                                @if ($field->help_text)
                                    <div class="form-field-help" id="{{ $field->field_name }}_help">{{ $field->help_text }}</div>
                                @endif

                                @error($nameAttr)
                                    <div class="invalid-feedback d-block" role="alert" aria-live="assertive">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg" aria-label="Submit form">
                                <i class="fas fa-paper-plane me-2"></i>Submit Form
                            </button>
                            @if (!$isAjax)
                                <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary btn-back" aria-label="Back to forms list">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Forms
                                </a>
                            @endif
                        </div>
                    </form>
                @endif
            @endif
        @else
            <div class="alert alert-danger" role="alert">Form not found.</div>
            @if (!$isAjax)
                <div class="form-actions">
                    <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary btn-back" aria-label="Back to forms list">
                        <i class="fas fa-arrow-left me-2"></i>Back to Forms
                    </a>
                </div>
            @endif
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios@1.7.7/dist/axios.min.js" integrity="sha256-9bKyYHG7WfRmaDNW3xG1OSYUz2lmWGkXmQxl1Irw3Lk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function PublicFormHandler() {
            this.elements = {
                form: document.getElementById('publicForm'),
                loadingOverlay: document.getElementById('loadingOverlay'),
                toastContainer: document.getElementById('toastContainer')
            };

            // Track if a toast is already displayed to prevent duplicates
            this.isToastDisplayed = false;

            if (!window.axios) {
                console.error('Axios not loaded');
                this.showToast('Required scripts failed to load.', 'error');
                return;
            }

            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            if (this.elements.form) {
                this.elements.form.addEventListener('submit', this.handleSubmit.bind(this));
            } else {
                console.warn('Public form not found');
            }

            this.validateForm = function() {
                var errors = {};
                var inputs = this.elements.form ? this.elements.form.querySelectorAll('input[required], select[required], textarea[required]') : [];

                inputs.forEach(function(input) {
                    if (!input.value.trim()) {
                        errors[input.name] = input.closest('.form-field').querySelector('.form-field-label').textContent + ' is required';
                        input.classList.add('is-invalid');
                        input.setAttribute('aria-invalid', 'true');
                    } else {
                        input.classList.remove('is-invalid');
                        input.removeAttribute('aria-invalid');
                    }
                });

                var checkboxGroups = this.elements.form ? this.elements.form.querySelectorAll('input[type="checkbox"][required]') : [];
                checkboxGroups.forEach(function(group) {
                    var name = group.name;
                    var checked = this.elements.form.querySelectorAll('input[name="' + name + '"]:checked').length;
                    if (!checked) {
                        errors[name] = group.closest('.form-field').querySelector('.form-field-label').textContent + ' requires at least one selection';
                        group.closest('.form-field').querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
                            cb.classList.add('is-invalid');
                            cb.setAttribute('aria-invalid', 'true');
                        });
                    } else {
                        group.closest('.form-field').querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
                            cb.classList.remove('is-invalid');
                            cb.removeAttribute('aria-invalid');
                        });
                    }
                }.bind(this));

                var radioGroups = {};
                var radios = this.elements.form ? this.elements.form.querySelectorAll('input[type="radio"][required]') : [];
                radios.forEach(function(radio) {
                    radioGroups[radio.name] = radioGroups[radio.name] || [];
                    radioGroups[radio.name].push(radio);
                });

                Object.entries(radioGroups).forEach(function([name, radios]) {
                    var checked = radios.some(function(radio) { return radio.checked; });
                    if (!checked) {
                        errors[name] = radios[0].closest('.form-field').querySelector('.form-field-label').textContent + ' requires a selection';
                        radios.forEach(function(radio) {
                            radio.classList.add('is-invalid');
                            radio.setAttribute('aria-invalid', 'true');
                        });
                    } else {
                        radios.forEach(function(radio) {
                            radio.classList.remove('is-invalid');
                            radio.removeAttribute('aria-invalid');
                        });
                    }
                });

                return { isValid: Object.keys(errors).length === 0, errors: errors };
            };

            this.handleSubmit = async function(e) {
                e.preventDefault();

                var submitButton = this.elements.form.querySelector('button[type="submit"]');
                submitButton.disabled = true; // Disable button to prevent multiple submissions

                var validation = this.validateForm();
                if (!validation.isValid) {
                    this.showToast('Please fix the errors before submitting.', 'error');
                    submitButton.disabled = false;
                    return;
                }

                this.showLoading();

                try {
                    var formData = new FormData(this.elements.form);
                    var response = await axios.post(this.elements.form.action, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });

                    this.hideLoading();

                    if (response.data.success) {
                        this.showToast(response.data.message, 'success');
                        if (document.getElementById('formModal')) {
                            setTimeout(function() {
                                var modal = bootstrap.Modal.getInstance(document.getElementById('formModal'));
                                if (modal) modal.hide();
                                window.location.reload();
                            }, 2000);
                        } else if (response.data.redirect) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        } else {
                            this.elements.form.reset();
                            this.showToast('Thank you for your submission!', 'success');
                        }
                    } else {
                        this.showToast(response.data.message || 'Failed to submit form.', 'error');
                    }
                } catch (error) {
                    this.hideLoading();
                    var message = 'An error occurred while submitting the form';
                    if (error.response) {
                        if (error.response.status === 422) {
                            message = error.response.data.message || 'Validation failed. Please check your inputs.';
                        } else if (error.response.status === 403) {
                            message = error.response.data.message || 'Unauthorized action.';
                        } else if (error.response.status === 500) {
                            message = error.response.data.message || 'Server error occurred.';
                        }
                    } else if (error.code === 'ERR_NETWORK') {
                        message = 'Network error. Please check your connection.';
                    }
                    if (!this.isToastDisplayed) {
                        this.isToastDisplayed = true;
                        this.showToast(message, 'error');
                        setTimeout(() => { this.isToastDisplayed = false; }, 3000);
                    }
                } finally {
                    submitButton.disabled = false;
                }
            };

            this.showToast = function(message, type) {
                if (!this.elements.toastContainer || this.isToastDisplayed) {
                    console.log('Toast skipped:', { message, type, isToastDisplayed: this.isToastDisplayed });
                    return;
                }

                this.isToastDisplayed = true;
                var toastId = 'toast_' + Date.now();
                var bgClass = {
                    'success': 'bg-success',
                    'error': 'bg-danger',
                    'warning': 'bg-warning',
                    'info': 'bg-info'
                }[type.toLowerCase()] || 'bg-info';

                var toastHTML = '<div class="toast align-items-center text-white ' + bgClass + ' border-0" id="' + toastId + '" role="alert" aria-live="assertive" aria-atomic="true">' +
                                '<div class="d-flex">' +
                                '<div class="toast-body">' + message + '</div>' +
                                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                                '</div></div>';

                this.elements.toastContainer.insertAdjacentHTML('beforeend', toastHTML);

                var toastElement = document.getElementById(toastId);
                if (toastElement) {
                    var toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                    toast.show();
                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                        this.isToastDisplayed = false;
                    }.bind(this));
                }
            };

            this.showLoading = function() {
                if (this.elements.loadingOverlay) {
                    this.elements.loadingOverlay.style.display = 'flex';
                }
            };

            this.hideLoading = function() {
                if (this.elements.loadingOverlay) {
                    this.elements.loadingOverlay.style.display = 'none';
                }
            };
        }

        // Initialize form handler when the DOM is ready and form exists
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('publicForm') && window.axios && window.bootstrap) {
                new PublicFormHandler();
            }
        });

        // Prevent multiple initializations in modal
        let handlerInitialized = false;
        document.addEventListener('shown.bs.modal', function (event) {
            if (document.getElementById('publicForm') && window.axios && window.bootstrap && !handlerInitialized) {
                new PublicFormHandler();
                handlerInitialized = true;
            }
        });

        // Reset handlerInitialized when modal is hidden
        document.addEventListener('hidden.bs.modal', function (event) {
            handlerInitialized = false;
        });
    </script>
@endpush