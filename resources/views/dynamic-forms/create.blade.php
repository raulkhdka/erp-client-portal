@extends('layouts.app')

@section('title', 'Create Dynamic Form')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus me-2"></i>Create Dynamic Form</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('dynamic-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Forms
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('dynamic-forms.store') }}" id="dynamicFormCreate">
                    @csrf

                    <!-- Form Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Form Information</h5>
                        </div>
                        <div class="col-md-12">
                            <label for="name" class="form-label">Form Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                <!-- Dynamic fields will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dynamic-forms.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
            </div>
            <div class="card-body">
                <h6>Creating Dynamic Forms</h6>
                <p class="small text-muted mb-3">
                    Dynamic forms allow you to create custom data collection forms for clients.
                    These forms can be shared with clients to gather specific information.
                </p>

                <h6>Field Types</h6>
                <ul class="small text-muted mb-3">
                    <li><strong>Text:</strong> Single line text input</li>
                    <li><strong>Email:</strong> Email address validation</li>
                    <li><strong>Number:</strong> Numeric input only</li>
                    <li><strong>Date:</strong> Date picker</li>
                    <li><strong>Textarea:</strong> Multi-line text</li>
                    <li><strong>Select:</strong> Dropdown menu</li>
                    <li><strong>Checkbox:</strong> Multiple selection</li>
                    <li><strong>Radio:</strong> Single selection</li>
                    <li><strong>File:</strong> File upload</li>
                </ul>

                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Note:</strong> You must add at least one field to create a form.
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-0">
                    <li>Use clear, descriptive field labels</li>
                    <li>Mark required fields appropriately</li>
                    <li>Group related fields together</li>
                    <li>Test your form before sharing</li>
                    <li>Consider the order of fields for better UX</li>
                </ul>
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
let fieldIndex = 0;

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

// Add initial field
document.getElementById('addField').click();

// Form validation
document.getElementById('dynamicFormCreate').addEventListener('submit', function(e) {
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
