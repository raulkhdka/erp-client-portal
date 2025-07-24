@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="alert alert-info" role="alert">
        Category editing is handled via a modal on the <a href="{{ route('document-categories.index') }}">Document Categories List</a> page (or the category's detail page).
    </div>
    <a href="{{ route('document-categories.index') }}" class="btn btn-primary">Go to Document Categories List</a>
</div>
@endsection