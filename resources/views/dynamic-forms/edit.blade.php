@extends('layouts.app')

@section('title', 'Edit Dynamic Form')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Dynamic Form: {{ $form->name }}</h1>
    <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Forms
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('dynamic-forms.update', $form->id) }}" id="dynamicFormEdit">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label">Form Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $form->name) }}" required autocomplete="off">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" autocomplete="off">{{ old('description', $form->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $form->is_active) ? 'checked' : '' }} autocomplete="off">
                            <label class="form-check-label" for="is_active">Form is Active</label>
                        </div>
                        <small class="text-muted">Active forms can receive submissions</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h5 class="mb-0">Form Fields</h5>
                            <button type="button" class="btn btn-sm btn-success" id="addField">
                                <i class="fas fa-plus me-1"></i>Add Field
                            </button>
                        </div>

                        <div id="fieldsContainer">
                            {{-- Existing fields will be appended here by JavaScript on load --}}
                            {{-- If old input exists (e.g., validation error), prioritize it --}}
                            @php
                                $fieldsToLoad = old('fields', $form->fields);
                            @endphp

                            @foreach ($fieldsToLoad as $index => $field)
                                @include('dynamic-forms._field_template', [
                                    'index' => $index,
                                    'field' => (object) $field // Cast to object for consistent access
                                ])
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('dynamic-forms.show', $form->id) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Field Guidelines</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-3">
                    <li>Each field requires a label. The "Field Name" will be auto-generated.</li>
                    <li>Options are required for "Select", "Checkbox", and "Radio" fields (one per line).</li>
                    <li>"Field Order" controls display order.</li>
                    <li>File uploads: Supported types (PDF, DOC, DOCX, JPG, PNG, GIF), max 10MB (add server-side validation).</li>
                </ul>
                <div class="alert alert-info small mb-0">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Tip:</strong> Use clear labels for better UX.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- This template will be cloned by JavaScript --}}
<template id="fieldTemplate">
    @include('dynamic-forms._field_template', ['index' => '__INDEX__', 'field' => null])
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate initial fieldIndex based on existing fields or old input
        let fieldIndex = {{ old('fields') ? count(old('fields')) : $form->fields->count() }};

        const addFieldBtn = document.getElementById('addField');
        const fieldsContainer = document.getElementById('fieldsContainer');
        const templateElement = document.getElementById('fieldTemplate');

        if (!addFieldBtn || !fieldsContainer || !templateElement) {
            console.error('Required elements not found: addFieldBtn, fieldsContainer, or fieldTemplate.');
            return;
        }

        // Function to add a new field (used for both initial load and add button)
        function addField(fieldData = null) {
            let templateContent = templateElement.innerHTML;
            templateContent = templateContent.replace(/__INDEX__/g, fieldIndex);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = templateContent;
            const fieldCard = wrapper.firstElementChild; // The actual card element

            fieldsContainer.appendChild(fieldCard);

            // Populate fields if fieldData is provided (for existing fields or old input re-population)
            if (fieldData) {
                fieldCard.querySelector('.field-id').value = fieldData.id || ''; // Hidden field for existing field ID
                fieldCard.querySelector('.field-label').value = fieldData.field_label || '';
                fieldCard.querySelector('.field-type').value = fieldData.field_type || '';
                if (fieldData.is_required) {
                    fieldCard.querySelector('.field-required').checked = true;
                }
                fieldCard.querySelector('.field-order').value = fieldData.sort_order || (fieldIndex + 1);
                fieldCard.querySelector('.field-options').value = (fieldData.field_options && Array.isArray(fieldData.field_options)) ? fieldData.field_options.join('\n') : (fieldData.field_options || '');
                fieldCard.querySelector('.field-placeholder').value = fieldData.placeholder || '';
                fieldCard.querySelector('.field-help-text').value = fieldData.help_text || '';
                fieldCard.querySelector('.field-title').textContent = fieldData.field_label || 'New Field';
            }

            // Attach event listeners for the newly added field
            attachFieldListeners(fieldCard);
            fieldIndex++; // Increment for the next field
        }

        // Function to attach listeners to a field card
        function attachFieldListeners(fieldCard) {
            // Remove functionality
            fieldCard.querySelector('.remove-field').addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this field?')) {
                    this.closest('.field-item').remove();
                }
            });

            // Title update functionality based on field label
            const labelInput = fieldCard.querySelector('.field-label');
            const titleSpan = fieldCard.querySelector('.field-title');
            if (labelInput && titleSpan) { // Check for existence
                labelInput.addEventListener('input', function() {
                    titleSpan.textContent = this.value || 'New Field';
                });
            }
        }

        // Add Field button click handler
        addFieldBtn.addEventListener('click', function() {
            addField();
        });

        // Attach listeners to fields that were initially loaded (either from $form->fields or old() input)
        fieldsContainer.querySelectorAll('.field-item').forEach(fieldCard => {
            attachFieldListeners(fieldCard);
        });

        // Form validation
        const form = document.getElementById('dynamicFormEdit');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Ensure at least one field is present
                if (fieldsContainer.children.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one field to the form before submitting.');
                    fieldsContainer.scrollIntoView({ behavior: 'smooth' });
                    return;
                }

                // Optional: Client-side validation for field names (e.g., unique within form)
            });
        }
    });
</script>
@endpush