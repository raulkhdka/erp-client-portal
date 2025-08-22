@php
    // Determine if this is an AJAX request for modal content
    $isAjax = Request::ajax();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if (!$isAjax) {{ $form->name }} @endif</title>

    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #6c757d;
            --success: #06d6a0;
            --danger: #ef476f;
            --warning: #ffd166;
            --info: #118ab2;
            --light: #f8f9fa;
            --dark: #212529;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
            --border-radius: 12px;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            padding: @if ($isAjax) 0 @else 2rem @endif;
        }

        .form-container {
            background: #ffffff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2.5rem;
            max-width: 800px;
            margin: @if ($isAjax) 0 @else 0 auto @endif;
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--info));
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .form-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .form-header .description {
            font-size: 1rem;
            color: var(--gray-600);
            font-weight: 400;
        }

        /* Two-column layout for KYC form */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem;
        }

        .form-col {
            flex: 1 0 0;
            padding: 0 0.75rem;
            min-width: 250px;
        }

        .form-field {
            margin-bottom: 1.75rem;
        }

        .form-field-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.6rem;
            color: var(--gray-800);
            font-size: 0.95rem;
        }

        .form-field-label .form-field-required {
            color: var(--danger);
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1.25rem;
            font-size: 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            background-color: #fff;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px rgba(239, 71, 111, 0.15);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .form-check-input {
            margin-right: 0.75rem;
            width: 1.25rem;
            height: 1.25rem;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .form-check-label {
            font-weight: 400;
            cursor: pointer;
        }

        .form-field-help {
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-top: 0.5rem;
            font-style: italic;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--danger);
            font-weight: 500;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(33, 37, 41, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--box-shadow-lg);
        }

        .spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid rgba(67, 97, 238, 0.2);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }

        .toast {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-lg);
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--box-shadow);
        }

        .alert.alert-danger {
            background-color: #ffefef;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert.alert-success {
            background-color: #e6fff6;
            color: #05a677;
            border-left: 4px solid var(--success);
        }

        .alert.alert-info {
            background-color: #e6f7ff;
            color: var(--info);
            border-left: 4px solid var(--info);
        }

        .alert ul {
            margin: 0.5rem 0 0 1.25rem;
        }

        .alert li {
            margin-bottom: 0.25rem;
        }

        .btn-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            opacity: 0.5;
            cursor: pointer;
            padding: 0;
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close:hover {
            opacity: 0.75;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 2px solid transparent;
            padding: 0.85rem 1.75rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            text-decoration: none;
            min-width: 140px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }

        .btn-secondary {
            background-color: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .btn-secondary:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            body {
                padding: @if ($isAjax) 0 @else 1rem @endif;
            }

            .form-container {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .form-row {
                flex-direction: column;
                margin: 0;
            }

            .form-col {
                padding: 0;
                min-width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 1.25rem;
            }

            .form-header {
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
            }

            .form-field {
                margin-bottom: 1.5rem;
            }

            .form-control {
                padding: 0.75rem 1rem;
            }
        }

        .modal-body .form-container {
            margin: 0;
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .modal-body .form-container::before {
            display: none;
        }

        /* KYC Form Specific Styles */
        .kyc-section {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .kyc-section:last-child {
            border-bottom: none;
            margin-bottom: 1.5rem;
        }

        .kyc-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1.25rem;
            padding-left: 0.5rem;
            border-left: 4px solid var(--primary);
        }

        .kyc-subtitle {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div>Submitting your form...</div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>

    <!-- Form Container -->
    <div class="form-container">
        <!-- Error Alert -->
        <div class="alert alert-danger alert-dismissible fade" role="alert" id="errorAlert" style="display: none;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
            <h4 class="alert-heading">Please correct the following errors:</h4>
            <ul class="error-message" id="errorList"></ul>
        </div>

        <!-- Success Alert -->
        <div class="alert alert-success alert-dismissible fade" role="alert" id="successAlert" style="display: none;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
            <h4 class="alert-heading">Success!</h4>
            <span id="successMessage"></span>
        </div>

        <div class="form-header">
            <h1 id="formTitle">{{ $form->name }}</h1>
            @if ($form->description)
                <div class="description" id="formDescription">{{ $form->description }}</div>
            @endif
        </div>

        @if ($form->exists)
            @if ($hasSubmitted)
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Form Already Submitted</h4>
                    <p>You have already submitted this form. Thank you for your response!</p>
                </div>
                @if (!$isAjax)
                    <div class="form-actions">
                        <a href="{{ route('clients.forms.index') }}" class="btn btn-secondary" aria-label="Back to forms list">
                            <i class="fas fa-arrow-left"></i> Back to Forms
                        </a>
                    </div>
                @endif
            @else
                <form id="publicForm" method="POST" action="{{ route('admin.dynamic-forms.submit', ['form' => $form->id]) }}" novalidate enctype="multipart/form-data" role="form" aria-labelledby="formTitle">
                    @csrf

                    <!-- Dynamic Fields with Two-Column Support -->
                    @php
                        $fields = $form->fields->toArray();
                        $processedFields = [];
                    @endphp

                    @for ($i = 0; $i < count($fields); $i++)
                        @php
                            $field = $fields[$i];
                            $nextField = isset($fields[$i + 1]) ? $fields[$i + 1] : null;

                            // Skip if already processed
                            if (in_array($field['id'], $processedFields)) continue;

                            $requiredAttr = $field['is_required'] ? 'required' : '';
                            $options = $field['field_options'] ? json_decode($field['field_options'], true) : [];
                            $nameAttr = $field['field_name'];
                        @endphp

                        <!-- Check if this field and next field should be in same row -->
                        @if ($nextField &&
                              !in_array($nextField['id'], $processedFields) &&
                              (
                                  (strpos($field['field_name'], 'first') !== false && strpos($nextField['field_name'], 'last') !== false) ||
                                  (strpos($field['field_name'], 'name') !== false && strpos($nextField['field_name'], 'address') !== false) ||
                                  (strpos($field['field_name'], 'city') !== false && strpos($nextField['field_name'], 'state') !== false) ||
                                  (strpos($field['field_name'], 'state') !== false && strpos($nextField['field_name'], 'zip') !== false) ||
                                  (strpos($field['field_name'], 'zip') !== false && strpos($nextField['field_name'], 'country') !== false) ||
                                  (strpos($field['field_name'], 'id_type') !== false && strpos($nextField['field_name'], 'id_number') !== false)
                              ))
                            <!-- Two-column row -->
                            <div class="form-row">
                                <div class="form-col">
                                    <div class="form-field">
                                        <label for="{{ $field['field_name'] }}" class="form-field-label">
                                            {{ $field['field_label'] }}
                                            @if ($field['is_required'])
                                                <span class="form-field-required" aria-hidden="true">*</span>
                                            @endif
                                        </label>

                                        @if ($field['field_type'] === 'textarea')
                                            <textarea class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" placeholder="{{ $field['placeholder'] }}" {{ $requiredAttr }} rows="4" aria-describedby="{{ $field['field_name'] }}_help">{{ old($nameAttr) }}</textarea>
                                        @elseif ($field['field_type'] === 'select')
                                            <select class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field['field_name'] }}_help">
                                                <option value="">{{ $field['placeholder'] ?: 'Choose...' }}</option>
                                                @foreach ($options as $option)
                                                    <option value="{{ $option }}" {{ old($nameAttr) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($field['field_type'] === 'radio')
                                            @foreach ($options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ $nameAttr }}" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ $requiredAttr }} {{ old($nameAttr) == $option ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif ($field['field_type'] === 'checkbox')
                                            @foreach ($options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="{{ $nameAttr }}[]" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ in_array($option, (array) old($nameAttr, [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif ($field['field_type'] === 'file')
                                            <input type="file" class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field['field_name'] }}_help">
                                        @else
                                            <input type="{{ $field['field_type'] }}" class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" placeholder="{{ $field['placeholder'] }}" {{ $requiredAttr }} value="{{ old($nameAttr) }}" aria-describedby="{{ $field['field_name'] }}_help">
                                        @endif

                                        @if ($field['help_text'])
                                            <div class="form-field-help" id="{{ $field['field_name'] }}_help">{{ $field['help_text'] }}</div>
                                        @endif

                                        @error($nameAttr)
                                            <div class="invalid-feedback d-block" role="alert" aria-live="assertive">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @php
                                    $nextNameAttr = $nextField['field_name'];
                                    $nextRequiredAttr = $nextField['is_required'] ? 'required' : '';
                                    $nextOptions = $nextField['field_options'] ? json_decode($nextField['field_options'], true) : [];
                                    $processedFields[] = $nextField['id'];
                                @endphp

                                <div class="form-col">
                                    <div class="form-field">
                                        <label for="{{ $nextField['field_name'] }}" class="form-field-label">
                                            {{ $nextField['field_label'] }}
                                            @if ($nextField['is_required'])
                                                <span class="form-field-required" aria-hidden="true">*</span>
                                            @endif
                                        </label>

                                        @if ($nextField['field_type'] === 'textarea')
                                            <textarea class="form-control" id="{{ $nextField['field_name'] }}" name="{{ $nextNameAttr }}" placeholder="{{ $nextField['placeholder'] }}" {{ $nextRequiredAttr }} rows="4" aria-describedby="{{ $nextField['field_name'] }}_help">{{ old($nextNameAttr) }}</textarea>
                                        @elseif ($nextField['field_type'] === 'select')
                                            <select class="form-control" id="{{ $nextField['field_name'] }}" name="{{ $nextNameAttr }}" {{ $nextRequiredAttr }} aria-describedby="{{ $nextField['field_name'] }}_help">
                                                <option value="">{{ $nextField['placeholder'] ?: 'Choose...' }}</option>
                                                @foreach ($nextOptions as $option)
                                                    <option value="{{ $option }}" {{ old($nextNameAttr) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif ($nextField['field_type'] === 'radio')
                                            @foreach ($nextOptions as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="{{ $nextNameAttr }}" id="{{ $nextNameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ $nextRequiredAttr }} {{ old($nextNameAttr) == $option ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $nextNameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif ($nextField['field_type'] === 'checkbox')
                                            @foreach ($nextOptions as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="{{ $nextNameAttr }}[]" id="{{ $nextNameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ in_array($option, (array) old($nextNameAttr, [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $nextNameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        @elseif ($nextField['field_type'] === 'file')
                                            <input type="file" class="form-control" id="{{ $nextField['field_name'] }}" name="{{ $nextNameAttr }}" {{ $nextRequiredAttr }} aria-describedby="{{ $nextField['field_name'] }}_help">
                                        @else
                                            <input type="{{ $nextField['field_type'] }}" class="form-control" id="{{ $nextField['field_name'] }}" name="{{ $nextNameAttr }}" placeholder="{{ $nextField['placeholder'] }}" {{ $nextRequiredAttr }} value="{{ old($nextNameAttr) }}" aria-describedby="{{ $nextField['field_name'] }}_help">
                                        @endif

                                        @if ($nextField['help_text'])
                                            <div class="form-field-help" id="{{ $nextField['field_name'] }}_help">{{ $nextField['help_text'] }}</div>
                                        @endif

                                        @error($nextNameAttr)
                                            <div class="invalid-feedback d-block" role="alert" aria-live="assertive">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @php
                                $processedFields[] = $field['id'];
                                $i++; // Skip next field as it's already processed
                            @endphp
                        @else
                            <!-- Single column field -->
                            <div class="form-field">
                                <label for="{{ $field['field_name'] }}" class="form-field-label">
                                    {{ $field['field_label'] }}
                                    @if ($field['is_required'])
                                        <span class="form-field-required" aria-hidden="true">*</span>
                                    @endif
                                </label>

                                @if ($field['field_type'] === 'textarea')
                                    <textarea class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" placeholder="{{ $field['placeholder'] }}" {{ $requiredAttr }} rows="4" aria-describedby="{{ $field['field_name'] }}_help">{{ old($nameAttr) }}</textarea>
                                @elseif ($field['field_type'] === 'select')
                                    <select class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field['field_name'] }}_help">
                                        <option value="">{{ $field['placeholder'] ?: 'Choose...' }}</option>
                                        @foreach ($options as $option)
                                            <option value="{{ $option }}" {{ old($nameAttr) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['field_type'] === 'radio')
                                    @foreach ($options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $nameAttr }}" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ $requiredAttr }} {{ old($nameAttr) == $option ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                @elseif ($field['field_type'] === 'checkbox')
                                    @foreach ($options as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="{{ $nameAttr }}[]" id="{{ $nameAttr . '_' . Str::slug($option) }}" value="{{ $option }}" {{ in_array($option, (array) old($nameAttr, [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $nameAttr . '_' . Str::slug($option) }}">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                @elseif ($field['field_type'] === 'file')
                                    <input type="file" class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" {{ $requiredAttr }} aria-describedby="{{ $field['field_name'] }}_help">
                                @else
                                    <input type="{{ $field['field_type'] }}" class="form-control" id="{{ $field['field_name'] }}" name="{{ $nameAttr }}" placeholder="{{ $field['placeholder'] }}" {{ $requiredAttr }} value="{{ old($nameAttr) }}" aria-describedby="{{ $field['field_name'] }}_help">
                                @endif

                                @if ($field['help_text'])
                                    <div class="form-field-help" id="{{ $field['field_name'] }}_help">{{ $field['help_text'] }}</div>
                                @endif

                                @error($nameAttr)
                                    <div class="invalid-feedback d-block" role="alert" aria-live="assertive">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                $processedFields[] = $field['id'];
                            @endphp
                        @endif
                    @endfor

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg" aria-label="Submit form">
                            <i class="fas fa-paper-plane"></i> Submit Form
                        </button>
                        @if (!$isAjax)
                            <a href="{{ route('clients.forms.index') }}" class="btn btn-secondary" aria-label="Back to forms list">
                                <i class="fas fa-arrow-left"></i> Back to Forms
                            </a>
                        @endif
                    </div>
                </form>
            @endif
        @else
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Form Not Found</h4>
                <p>The requested form could not be found.</p>
            </div>
            @if (!$isAjax)
                <div class="form-actions">
                    <a href="{{ route('clients.forms.index') }}" class="btn btn-secondary" aria-label="Back to forms list">
                        <i class="fas fa-arrow-left"></i> Back to Forms
                    </a>
                </div>
            @endif
        @endif
    </div>


    <script>
        function PublicFormHandler() {
            this.elements = {
                form: document.getElementById('publicForm'),
                loadingOverlay: document.getElementById('loadingOverlay'),
                errorAlert: document.getElementById('errorAlert'),
                errorList: document.getElementById('errorList'),
                successAlert: document.getElementById('successAlert'),
                successMessage: document.getElementById('successMessage'),
                toastContainer: document.getElementById('toastContainer')
            };

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

                var validation = this.validateForm();
                if (!validation.isValid) {
                    this.showErrors(validation.errors);
                    this.showToast('Please fix the errors before submitting.', 'error');
                    return;
                }

                this.hideErrors();
                this.showLoading();

                try {
                    var formData = new FormData(this.elements.form);
                    var response = await axios.post(this.elements.form.action, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });

                    this.hideLoading();

                    if (response.data.success) {
                        this.showSuccess(response.data.message);
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
                            this.showSuccess('Thank you for your submission!');
                        }
                    } else {
                        this.showErrors(response.data.errors || ['Unexpected response format']);
                        this.showToast(response.data.errors && response.data.errors.general ? response.data.errors.general : 'Failed to submit form.', 'error');
                    }
                } catch (error) {
                    this.hideLoading();
                    var errors = ['An unexpected error occurred. Please try again.'];
                    var message = 'An error occurred while submitting the form';
                    if (error.response && error.response.status === 422) {
                        errors = Object.values(error.response.data.errors).flat();
                        message = error.response.data.errors && error.response.data.errors.general ? error.response.data.errors.general : 'Validation failed. Please check your inputs.';
                    } else if (error.response && error.response.status === 403) {
                        errors = error.response.data.errors || ['You are not authorized to submit this form.'];
                        message = error.response.data.errors && error.response.data.errors.general ? error.response.data.errors.general : 'Unauthorized action.';
                    } else if (error.response && error.response.status === 500) {
                        errors = error.response.data.errors || ['Server error occurred.'];
                        message = error.response.data.message || 'Server error occurred.';
                    } else if (error.code === 'ERR_NETWORK') {
                        errors = ['Network error. Please check your connection.'];
                        message = 'Network error. Please check your connection.';
                    }
                    this.showErrors(errors);
                    this.showToast(message, 'error');
                }
            };

            this.showErrors = function(errors) {
                if (this.elements.errorList && this.elements.errorAlert) {
                    this.elements.errorList.innerHTML = '';
                    if (Array.isArray(errors)) {
                        errors.forEach(function(error) {
                            var li = document.createElement('li');
                            li.textContent = error;
                            this.elements.errorList.appendChild(li);
                        }.bind(this));
                    } else if (typeof errors === 'object') {
                        Object.values(errors).flat().forEach(function(error) {
                            var li = document.createElement('li');
                            li.textContent = error;
                            this.elements.errorList.appendChild(li);
                        }.bind(this));
                    } else {
                        var li = document.createElement('li');
                        li.textContent = errors;
                        this.elements.errorList.appendChild(li);
                    }

                    this.elements.errorAlert.classList.add('show');
                    this.elements.errorAlert.style.display = 'block';
                    this.elements.errorAlert.scrollIntoView({ behavior: 'smooth' });
                }
            };

            this.hideErrors = function() {
                if (this.elements.errorAlert) {
                    this.elements.errorAlert.classList.remove('show');
                    this.elements.errorAlert.style.display = 'none';
                }
            };

            this.showSuccess = function(message) {
                if (this.elements.successAlert && this.elements.successMessage) {
                    this.elements.successMessage.textContent = message;
                    this.elements.successAlert.classList.add('show');
                    this.elements.successAlert.style.display = 'block';
                    this.elements.successAlert.scrollIntoView({ behavior: 'smooth' });
                }
            };

            this.showToast = function(message, type) {
                if (!this.elements.toastContainer) return;

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
                    });
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

        // Re-initialize when modal content is loaded via AJAX
        document.addEventListener('shown.bs.modal', function (event) {
            if (document.getElementById('publicForm') && window.axios && window.bootstrap) {
                new PublicFormHandler();
            }
        });
    </script>
</body>
</html>
