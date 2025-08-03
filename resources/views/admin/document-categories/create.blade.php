@extends('layouts.app')

@section('title', 'Create Document Category')

@section('breadcrumb')
    <a href="{{ route('admin.documents.index') }}">Documents</a>
    <a href="{{ route('admin.document-categories.index') }}">Categories</a>
    <span class="breadcrumb-item active">Create</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.document-categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="alert alert-info" role="alert">
        Category creation is handled via a modal on the <a href="{{ route('admin.document-categories.index') }}">Document Categories List</a> page.
    </div>
    <a href="{{ route('admin.document-categories.index') }}" class="btn btn-primary">Go to Document Categories List</a>
</div>
@endsection
