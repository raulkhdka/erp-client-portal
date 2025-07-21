{{-- This partial is included by create.blade.php and edit.blade.php --}}
{{-- It expects $index (string or int) and $field (object or null) --}}

<div class="card mb-3 field-item">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <span class="field-title">{{ $field->field_label ?? 'New Field' }}</span>
        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
            <i class="fas fa-trash me-1"></i> Remove
        </button>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Hidden Field ID for existing fields (used in edit) --}}
            <input type="hidden" class="field-id" name="fields[{{ $index }}][field_id]" value="{{ $field->id ?? '' }}">

            <div class="col-md-6">
                <label class="form-label">Field Label *</label>
                <input type="text" class="form-control field-label" name="fields[{{ $index }}][field_label]"
                       value="{{ $field->field_label ?? '' }}" required autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="form-label">Field Type *</label>
                <select class="form-control field-type" name="fields[{{ $index }}][field_type]" required autocomplete="off">
                    <option value="">Select Type</option>
                    <option value="text" {{ ($field && $field->field_type == 'text') ? 'selected' : '' }}>Text</option>
                    <option value="email" {{ ($field && $field->field_type == 'email') ? 'selected' : '' }}>Email</option>
                    <option value="number" {{ ($field && $field->field_type == 'number') ? 'selected' : '' }}>Number</option>
                    <option value="date" {{ ($field && $field->field_type == 'date') ? 'selected' : '' }}>Date</option>
                    <option value="textarea" {{ ($field && $field->field_type == 'textarea') ? 'selected' : '' }}>Textarea</option>
                    <option value="select" {{ ($field && $field->field_type == 'select') ? 'selected' : '' }}>Select</option>
                    <option value="checkbox" {{ ($field && $field->field_type == 'checkbox') ? 'selected' : '' }}>Checkbox</option>
                    <option value="radio" {{ ($field && $field->field_type == 'radio') ? 'selected' : '' }}>Radio</option>
                    <option value="file" {{ ($field && $field->field_type == 'file') ? 'selected' : '' }}>File</option>
                </select>
            </div>
            <div class="col-md-6">
                <div class="form-check mt-2">
                    <input class="form-check-input field-required" type="checkbox" name="fields[{{ $index }}][is_required]" value="1" {{ ($field && $field->is_required) ? 'checked' : '' }} autocomplete="off">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Field Order</label>
                <input type="number" class="form-control field-order" name="fields[{{ $index }}][sort_order]"
                       value="{{ $field->sort_order ?? ($index !== '__INDEX__' ? (int)$index + 1 : 1) }}" min="1" autocomplete="off">
            </div>
            <div class="col-md-12">
                <label class="form-label">Field Options (for select, checkbox, radio - one per line)</label>
                <textarea class="form-control field-options" name="fields[{{ $index }}][field_options]" rows="3"
                          placeholder="Option 1
Option 2
Option 3" autocomplete="off">{{ ($field && $field->field_options && is_array($field->field_options)) ? implode("\n", $field->field_options) : '' }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Placeholder (optional)</label>
                <input type="text" class="form-control field-placeholder" name="fields[{{ $index }}][placeholder]"
                       value="{{ $field->placeholder ?? '' }}" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="form-label">Help Text (optional)</label>
                <textarea class="form-control field-help-text" name="fields[{{ $index }}][help_text]" rows="2" autocomplete="off">{{ $field->help_text ?? '' }}</textarea>
            </div>
            {{-- You can add input for validation_rules here if you want to allow dynamic rule input --}}
            {{-- For simplicity, I've left validation_rules as a server-side concept for now --}}
        </div>
    </div>
</div>