@extends('layouts.app')

@section('title', 'Upload Document')

@section('breadcrumb')
    <a href="{{ route('employee.documents.index') }}">Documents</a>
    <span class="breadcrumb-item active">Upload</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employee.documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Documents
        </a>
    </div>
@endsection

@push('styles')
    <style>
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #0056b3;
            background-color: rgba(0, 123, 255, 0.05);
        }

        .upload-area.dragover {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .file-preview {
            display: none;
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .permission-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">📤 Upload Document</h4>
                    <a href="{{ route('employee.documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Documents
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('employee.documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- File Upload Area -->
                        <div class="mb-4">
                            <label class="form-label">Document File <span class="text-danger">*</span></label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                <h5>Drag and drop your file here</h5>
                                <p class="text-muted">or click to browse (Max: 10MB)</p>
                                <input type="file" id="document_file" name="document_file" class="d-none" required>
                            </div>
                            <div id="filePreview" class="file-preview">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file fa-2x me-3"></i>
                                    <div>
                                        <strong id="fileName"></strong><br>
                                        <small id="fileSize" class="text-muted"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="removeFile">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @error('document_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Document Info -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Document Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="categories_id" class="form-label">Category</label>
                                    <select class="form-select @error('categories_id') is-invalid @enderror"
                                            id="categories_id" name="categories_id">
                                        <option value="">Select category...</option>
                                        @foreach($categories as $id => $name)
                                            <option value="{{ $id }}" {{ old('categories_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('categories_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                    <input type="date" class="form-control @error('expires_at') is-invalid @enderror"
                                           id="expires_at" name="expires_at" value="{{ old('expires_at') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Employee Permissions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">Associated Client <span class="text-danger">*</span></label>
                                <select class="form-select client-select @error('client_id') is-invalid @enderror"
                                        id="client_id" name="client_id" required>
                                    <option value="">Select a client...</option>
                                    @foreach($clients as $id => $name)
                                        <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="permission-section mb-4">
                            <h5 class="mb-3">🔒 Access Permissions</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_public"><strong>Public Document</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_confidential" name="is_confidential" value="1" {{ old('is_confidential') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_confidential"><strong>Confidential Document</strong></label>
                                    </div>
                                </div>
                            </div>

                            {{-- <div id="specificUsers" style="{{ old('is_public') ? 'display: none;' : '' }}">
                                <label class="form-label">Grant Access to Specific Users</label>
                                <div class="row">
                                    @foreach($users as $id => $name)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="access_permissions[]"
                                                       value="{{ $id }}" id="user_{{ $id }}"
                                                       {{ in_array($id, old('access_permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="user_{{ $id }}">{{ $name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div> --}}
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('employee.documents.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('document_file');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeFile = document.getElementById('removeFile');
            const titleInput = document.getElementById('title');
            const isPublicCheckbox = document.getElementById('is_public');
            // const specificUsersDiv = document.getElementById('specificUsers');

            // Drag and drop functionality
            uploadArea.addEventListener('click', () => fileInput.click());

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

            removeFile.addEventListener('click', function() {
                fileInput.value = '';
                filePreview.style.display = 'none';
                titleInput.value = '';
            });

            // Toggle specific users section based on public checkbox
            // isPublicCheckbox.addEventListener('change', function() {
            //     specificUsersDiv.style.display = this.checked ? 'none' : 'block';
            // });

            function handleFileSelect(file) {
                // Show file preview
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.style.display = 'block';

                // Auto-populate title if empty
                if (!titleInput.value) {
                    const nameWithoutExtension = file.name.replace(/\.[^/.]+$/, "");
                    titleInput.value = nameWithoutExtension.replace(/[-_]/g, ' ');
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Initialize Select2 for client dropdowns
            $('.client-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select a client...',
                allowClear: true,
                width: '100%'
            });

            // Debug form submission
            document.getElementById('uploadForm').addEventListener('submit', function() {
                console.log('is_public:', document.getElementById('is_public').value);
                console.log('is_confidential:', document.getElementById('is_confidential').value);
            });
        });
    </script>
@endpush