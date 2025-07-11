<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $form->name }} - ERP System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .form-body {
            padding: 40px;
        }
        .form-footer {
            background: #f8f9fa;
            padding: 20px 40px;
            border-top: 1px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-1px);
        }
        .required-asterisk {
            color: #dc3545;
        }
        .success-message {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                @if(session('success'))
                    <div class="success-message">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h3>Form Submitted Successfully!</h3>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                @else
                    <div class="form-container">
                        <div class="form-header">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <h2 class="mb-2">{{ $form->name }}</h2>
                            @if($form->description)
                                <p class="mb-0 opacity-75">{{ $form->description }}</p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('dynamic-forms.submit', $form->id) }}" enctype="multipart/form-data" id="publicForm">
                            @csrf

                            <div class="form-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Please correct the following errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @foreach($form->fields->sortBy('field_order') as $field)
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            {{ $field->field_label }}
                                            @if($field->is_required)
                                                <span class="required-asterisk">*</span>
                                            @endif
                                        </label>

                                        @switch($field->field_type)
                                            @case('text')
                                                <input type="text"
                                                       class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                       name="fields[{{ $field->field_name }}]"
                                                       value="{{ old('fields.'.$field->field_name) }}"
                                                       {{ $field->is_required ? 'required' : '' }}>
                                                @break

                                            @case('email')
                                                <input type="email"
                                                       class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                       name="fields[{{ $field->field_name }}]"
                                                       value="{{ old('fields.'.$field->field_name) }}"
                                                       {{ $field->is_required ? 'required' : '' }}>
                                                @break

                                            @case('number')
                                                <input type="number"
                                                       class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                       name="fields[{{ $field->field_name }}]"
                                                       value="{{ old('fields.'.$field->field_name) }}"
                                                       {{ $field->is_required ? 'required' : '' }}>
                                                @break

                                            @case('date')
                                                <input type="date"
                                                       class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                       name="fields[{{ $field->field_name }}]"
                                                       value="{{ old('fields.'.$field->field_name) }}"
                                                       {{ $field->is_required ? 'required' : '' }}>
                                                @break

                                            @case('textarea')
                                                <textarea class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                          name="fields[{{ $field->field_name }}]"
                                                          rows="4"
                                                          {{ $field->is_required ? 'required' : '' }}>{{ old('fields.'.$field->field_name) }}</textarea>
                                                @break

                                            @case('select')
                                                <select class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                        name="fields[{{ $field->field_name }}]"
                                                        {{ $field->is_required ? 'required' : '' }}>
                                                    <option value="">-- Select an option --</option>
                                                    @if($field->field_options)
                                                        @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                                            <option value="{{ $option }}"
                                                                    {{ old('fields.'.$field->field_name) === $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @break

                                            @case('checkbox')
                                                @if($field->field_options)
                                                    @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="fields[{{ $field->field_name }}][]"
                                                                   value="{{ $option }}"
                                                                   id="{{ $field->field_name }}_{{ $loop->index }}"
                                                                   {{ in_array($option, old('fields.'.$field->field_name, [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $field->field_name }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break

                                            @case('radio')
                                                @if($field->field_options)
                                                    @foreach(json_decode($field->field_options, true) ?? [] as $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                   name="fields[{{ $field->field_name }}]"
                                                                   value="{{ $option }}"
                                                                   id="{{ $field->field_name }}_{{ $loop->index }}"
                                                                   {{ old('fields.'.$field->field_name) === $option ? 'checked' : '' }}
                                                                   {{ $field->is_required ? 'required' : '' }}>
                                                            <label class="form-check-label" for="{{ $field->field_name }}_{{ $loop->index }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break

                                            @case('file')
                                                <input type="file"
                                                       class="form-control @error('fields.'.$field->field_name) is-invalid @enderror"
                                                       name="fields[{{ $field->field_name }}]"
                                                       {{ $field->is_required ? 'required' : '' }}>
                                                <small class="form-text text-muted">
                                                    Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF (max 10MB)
                                                </small>
                                                @break
                                        @endswitch

                                        @error('fields.'.$field->field_name)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-footer">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Form
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Your information is secure and will be handled confidentially.
                                    </small>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation and UX improvements
        document.getElementById('publicForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

            // Re-enable button after a timeout (in case of errors)
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 10000);
        });

        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        // File upload validation
        document.querySelectorAll('input[type="file"]').forEach(fileInput => {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png', 'image/gif'];

                    if (file.size > maxSize) {
                        alert('File size must be less than 10MB');
                        this.value = '';
                        return;
                    }

                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid file type (PDF, DOC, DOCX, JPG, PNG, GIF)');
                        this.value = '';
                        return;
                    }
                }
            });
        });

        // Smooth scroll to errors
        window.addEventListener('load', function() {
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        });
    </script>
</body>
</html>
