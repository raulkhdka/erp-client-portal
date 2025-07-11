@extends('layouts.app')

@section('title', 'Edit Dynamic Form - ' . $form->name)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Dynamic Form</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('dynamic-forms.show', $form->id) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>View Form
            </a>
        </div>
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('dynamic-forms.update', $form->id) }}" id="dynamicFormEdit">
                    @csrf
                    @method('PUT')

                    <!-- Form Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Form Information</h5>
                        </div>
                        <div class="col-md-12">
                            <label for="name" class="form-label">Form Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $form->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $form->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $form->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Form is Active
                                </label>
                            </div>
                            <small class="text-muted">Active forms can receive submissions</small>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0">Form Fields</h5>
                                <button type="button" class="btn btn-sm btn-success" id="addField">
                                    <i class="fas fa-plus me-1"></i>Add Field
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="fieldsContainer">
                                @foreach($form->fields->sortBy('field_order') as $index => $field)
                                    <div class="card mb-3 field-item">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span class="field-title">{{ $field->field_label }}</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field->id }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Field Name *</label>
                                                    <input type="text" class="form-control field-name" name="fields[{{ $index }}][field_name]"
                                                           value="{{ $field->field_name }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Field Label *</label>
                                                    <input type="text" class="form-control field-label" name="fields[{{ $index }}][field_label]"
                                                           value="{{ $field->field_label }}" required>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <label class="form-label">Field Type *</label>
                                                    <select class="form-control field-type" name="fields[{{ $index }}][field_type]" required>
                                                        <option value="">Select Type</option>
                                                        <option value="text" {{ $field->field_type === 'text' ? 'selected' : '' }}>Text</option>
                                                        <option value="email" {{ $field->field_type === 'email' ? 'selected' : '' }}>Email</option>
                                                        <option value="number" {{ $field->field_type === 'number' ? 'selected' : '' }}>Number</option>
                                                        <option value="date" {{ $field->field_type === 'date' ? 'selected' : '' }}>Date</option>
                                                        <option value="textarea" {{ $field->field_type === 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                        <option value="select" {{ $field->field_type === 'select' ? 'selected' : '' }}>Select</option>
                                                        <option value="checkbox" {{ $field->field_type === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                        <option value="radio" {{ $field->field_type === 'radio' ? 'selected' : '' }}>Radio</option>
                                                        <option value="file" {{ $field->field_type === 'file' ? 'selected' : '' }}>File</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input field-required" type="checkbox" name="fields[{{ $index }}][is_required]"
                                                               value="1" {{ $field->is_required ? 'checked' : '' }}>
                                                        <label class="form-check-label">Required Field</label>
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-label">Field Order</label>
                                                        <input type="number" class="form-control" name="fields[{{ $index }}][field_order]"
                                                               value="{{ $field->field_order }}" min="1">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <label class="form-label">Field Options (for select, checkbox, radio - one per line)</label>
                                                    <textarea class="form-control field-options" name="fields[{{ $index }}][field_options]" rows="3"
                                                              placeholder="Option 1&#10;Option 2&#10;Option 3">{{ $field->field_options ? implode("\n", json_decode($field->field_options, true)) : '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dynamic-forms.show', $form->id) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Form Info -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Current Form Info</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ $form->name }}</h6>
                        <small class="text-muted">{{ $form->fields->count() }} fields</small>
                    </div>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="h5 mb-0">{{ $form->responses->count() }}</div>
                            <small class="text-muted">Responses</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="h5 mb-0">
                            <span class="badge bg-{{ $form->is_active ? 'success' : 'secondary' }}">
                                {{ $form->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <small class="text-muted">Status</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Guidelines -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Edit Guidelines</h6>
            </div>
            <div class="card-body">
                <h6>Important Notes</h6>
                <ul class="small text-muted mb-3">
                    <li>Modifying fields may affect existing responses</li>
                    <li>Deleting fields will remove their data from responses</li>
                    <li>Field names should be unique within the form</li>
                    <li>Required fields cannot be left empty by users</li>
                </ul>

                <h6>Field Types</h6>
                <ul class="small text-muted mb-3">
                    <li><strong>Text:</strong> Single line input</li>
                    <li><strong>Email:</strong> Email validation</li>
                    <li><strong>Number:</strong> Numeric input</li>
                    <li><strong>Date:</strong> Date picker</li>
                    <li><strong>Textarea:</strong> Multi-line text</li>
                    <li><strong>Select/Checkbox/Radio:</strong> Need options</li>
                    <li><strong>File:</strong> File upload</li>
                </ul>

                <div class="alert alert-info small mb-0">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Tip:</strong> Use field order to control the sequence of fields in the form.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Template -->
<template id="fieldTemplate">
    <div class="card mb-3 field-item">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="field-title">New Field</span>
            <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Field Name *</label>
                    <input type="text" class="form-control field-name" name="fields[][field_name]" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Field Label *</label>
                    <input type="text" class="form-control field-label" name="fields[][field_label]" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Field Type *</label>
                    <select class="form-control field-type" name="fields[][field_type]" required>
                        <option value="">Select Type</option>
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="textarea">Textarea</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="radio">Radio</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <div class="form-check">
                        <input class="form-check-input field-required" type="checkbox" name="fields[][is_required]" value="1">
                        <label class="form-check-label">Required Field</label>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Field Order</label>
                        <input type="number" class="form-control" name="fields[][field_order]" value="1" min="1">
                    </div>
                </div>
                <div class="col-md-12 mt-3">
                    <label class="form-label">Field Options (for select, checkbox, radio - one per line)</label>
                    <textarea class="form-control field-options" name="fields[][field_options]" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                </div>
            </div>
        </div>
    </div>
</template>

@section('scripts')
<script>
let fieldIndex = {{ $form->fields->count() }};

document.getElementById('addField').addEventListener('click', function() {
    const template = document.getElementById('fieldTemplate');
    const clone = template.content.cloneNode(true);

    // Update field names with unique indices
    const inputs = clone.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.name) {
            input.name = input.name.replace('[]', `[${fieldIndex}]`);
        }
    });

    document.getElementById('fieldsContainer').appendChild(clone);
    fieldIndex++;

    // Add event listener to remove button
    const removeBtn = document.getElementById('fieldsContainer').lastElementChild.querySelector('.remove-field');
    removeBtn.addEventListener('click', function() {
        this.closest('.field-item').remove();
    });

    // Add event listener to field name input for dynamic title update
    const fieldNameInput = document.getElementById('fieldsContainer').lastElementChild.querySelector('.field-name');
    const fieldTitle = document.getElementById('fieldsContainer').lastElementChild.querySelector('.field-title');
    fieldNameInput.addEventListener('input', function() {
        fieldTitle.textContent = this.value || 'New Field';
    });
});

// Add event listeners to existing fields
document.querySelectorAll('.remove-field').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove this field? This action cannot be undone.')) {
            this.closest('.field-item').remove();
        }
    });
});

// Add event listeners to existing field name inputs
document.querySelectorAll('.field-name').forEach(input => {
    const fieldTitle = input.closest('.field-item').querySelector('.field-title');
    input.addEventListener('input', function() {
        fieldTitle.textContent = this.value || 'New Field';
    });
});

// Form validation
document.getElementById('dynamicFormEdit').addEventListener('submit', function(e) {
    const fieldsContainer = document.getElementById('fieldsContainer');
    if (fieldsContainer.children.length === 0) {
        e.preventDefault();
        alert('Please add at least one field to the form.');
        return false;
    }

    // Validate that all required fields are filled
    const requiredInputs = fieldsContainer.querySelectorAll('input[required], select[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
@endsection
@endsection
