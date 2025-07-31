@extends('layouts.app')

@section('title', 'Edit Document Category')

@section('breadcrumb')
    <a href="{{ route('documents.index') }}">Documents</a>
    <a href="{{ route('document-categories.index') }}">Categories</a>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('document-categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="alert alert-info" role="alert">
        Category editing is handled via a modal on the <a href="{{ route('document-categories.index') }}">Document Categories List</a> page (or the category's detail page).
    </div>
    <a href="{{ route('document-categories.index') }}" class="btn btn-primary">Go to Document Categories List</a>
</div>
@endsection
