@extends('layouts.app') {{-- Or a public layout without admin navigation --}}

@section('title', $form->name)

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h2 class="mb-0">{{ $form->name }}</h2>
                        <p class="mb-0">{{ $form->description ?? '' }}</p>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">Please correct the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (!$form->is_active)
                            <div class="alert alert-warning text-center">
                                This form is currently inactive and cannot receive submissions.
                            </div>
                        @else
                            <form action="{{ route('dynamic-forms.public-submit', $form->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                @foreach ($form->fields->sortBy('sort_order') as $field)
                                    <div class="mb-3">
                                        <label for="{{ $field->field_name }}" class="form-label">
                                            {{ $field->field_label }}
                                            @if ($field->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @switch($field->field_type)
                                            @case('text')
                                                <input type="text"
                                                    class="form-control @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    value="{{ old($field->field_name) }}" placeholder="{{ $field->placeholder }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                            @break

                                            @case('email')
                                                <input type="email"
                                                    class="form-control @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    value="{{ old($field->field_name) }}" placeholder="{{ $field->placeholder }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                            @break

                                            @case('number')
                                                <input type="number"
                                                    class="form-control @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    value="{{ old($field->field_name) }}" placeholder="{{ $field->placeholder }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                            @break

                                            @case('date')
                                                <input type="date"
                                                    class="form-control @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    value="{{ old($field->field_name) }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                            @break

                                            @case('textarea')
                                                <textarea class="form-control @error($field->field_name) is-invalid @enderror" id="{{ $field->field_name }}"
                                                    name="{{ $field->field_name }}" rows="5" placeholder="{{ $field->placeholder }}"
                                                    {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_name) }}</textarea>
                                            @break

                                            @case('select')
                                                <select class="form-select @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                                    <option value="">{{ $field->placeholder ?: 'Select an option' }}</option>

                                                    @if ($field->field_options && is_array($field->field_options))
                                                        @foreach ($field->field_options as $option)
                                                            <option value="{{ $option }}"
                                                                {{ old($field->field_name) == $option ? 'selected' : '' }}>
                                                                {{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @break

                                            @case('checkbox')
                                                @if ($field->field_options && is_array($field->field_options))
                                                    @foreach ($field->field_options as $option)
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input @error($field->field_name) is-invalid @enderror"
                                                                type="checkbox" value="{{ $option }}"
                                                                id="{{ $field->field_name }}_{{ Str::slug($option) }}"
                                                                name="{{ $field->field_name }}[]"
                                                                {{ is_array(old($field->field_name)) && in_array($option, old($field->field_name)) ? 'checked' : '' }}
                                                                {{ $field->is_required ? 'required' : '' }}> {{-- Note: 'required' on individual checkboxes only works if at least one must be checked --}}
                                                            <label class="form-check-label"
                                                                for="{{ $field->field_name }}_{{ Str::slug($option) }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input @error($field->field_name) is-invalid @enderror"
                                                            type="checkbox" value="1" id="{{ $field->field_name }}"
                                                            name="{{ $field->field_name }}"
                                                            {{ old($field->field_name) ? 'checked' : '' }}
                                                            {{ $field->is_required ? 'required' : '' }}>
                                                        <label class="form-check-label" for="{{ $field->field_name }}">
                                                            {{ $field->field_label }}
                                                        </label>
                                                    </div>
                                                @endif
                                            @break

                                            @case('radio')
                                                @if ($field->field_options && is_array($field->field_options))
                                                    @foreach ($field->field_options as $option)
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input @error($field->field_name) is-invalid @enderror"
                                                                type="radio" value="{{ $option }}"
                                                                id="{{ $field->field_name }}_{{ Str::slug($option) }}"
                                                                name="{{ $field->field_name }}"
                                                                {{ old($field->field_name) == $option ? 'checked' : '' }}
                                                                {{ $field->is_required ? 'required' : '' }}>
                                                            <label class="form-check-label"
                                                                for="{{ $field->field_name }}_{{ Str::slug($option) }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @break

                                            @case('file')
                                                <input type="file"
                                                    class="form-control @error($field->field_name) is-invalid @enderror"
                                                    id="{{ $field->field_name }}" name="{{ $field->field_name }}"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                            @break
                                        @endswitch

                                        @if ($field->help_text)
                                            <div class="form-text">{{ $field->help_text }}</div>
                                        @endif
                                        @error($field->field_name)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">Submit Form</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
