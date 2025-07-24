@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="alert alert-info" role="alert">
        Category creation is handled via a modal on the <a href="{{ route('document-categories.index') }}">Document Categories List</a> page.
    </div>
    <a href="{{ route('document-categories.index') }}" class="btn btn-primary">Go to Document Categories List</a>
</div>
@endsection