@extends('layouts.app')

@section('title', 'Upload New Document')

{{-- Define the navbar-title section here to display "Upload New Document" in the top navigation bar --}}
@section('page-navbar-title')
    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload New Document</h5>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 offset-lg-2"> {{-- Centering the form --}}
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Document Details</h5>
                </div>
                <div class="card-body">
                    {{-- Form for document upload --}}
                    {{-- Make sure your form has enctype="multipart/form-data" for file uploads --}}
                    <form action="{{ route('clients.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="document_file" class="form-label">Select Document File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file" required>
                            @error('document_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Max size: 10MB.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('clients.documents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Documents
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Upload Document
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
    // Any specific JavaScript for this page can go here if needed.
    // For example, file size/type validation on client side before submission.
});
</script>
@endpush