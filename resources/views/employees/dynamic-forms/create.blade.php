@extends('layouts.app')

@section('title', 'Create My Dynamic Form')

@section('breadcrumb')
    <a href="{{ route('employees.dynamic-forms.index') }}">My Dynamic Forms</a>
    <span class="breadcrumb-item active">Create</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to My Forms
        </a>
    </div>
@endsection

@section('styles')
<style>
/* Existing styles remain unchanged */
.preview-container {
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    min-height: 400px;
    position: relative;
}

.preview-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 0.5rem 0.5rem 0 0;
    text-align: center;
}

.preview-body {
    padding: 2rem;
}

.preview-form {
    max-width: 600px;
    margin: 0 auto;
}

.preview-field {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.preview-field:hover {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.preview-field-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.preview-field-required {
    color: #dc3545;
    margin-left: 0.25rem;
}

.preview-field-help {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
    font-style: italic;
}

.preview-empty {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.preview-empty i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

.preview-submit-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    color: white;
}

.preview-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: white;
}

.device-selector {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    justify-content: center;
}

.device-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.device-btn.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.device-btn:hover {
    border-color: #007bff;
    color: #007bff;
}

.device-btn.active:hover {
    color: white;
}

.preview-device-frame {
    max-width: 100%;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.preview-device-frame.mobile {
    max-width: 375px;
}

.preview-device-frame.tablet {
    max-width: 768px;
}

.preview-device-frame.desktop {
    max-width: 100%;
}

#previewColumn {
    display: none;
}

.preview-stats {
    display: flex;
    justify-content: space-around;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.preview-stat {
    text-align: center;
}

.preview-stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.preview-stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.field-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.field-item:hover {
    border-left-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.field-item.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
}

.drag-handle {
    cursor: grab;
    color: #6c757d;
}

.drag-handle:active {
    cursor: grabbing;
}

.field-collapsed .collapse-content {
    display: none;
}

.field-preview {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-top: 0.5rem;
}

.validation-error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.field-type-icon {
    width: 20px;
    height: 20px;
    margin-right: 8px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
}

.fullscreen-preview {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
    overflow-y: auto;
    padding: 2rem;
    display: none;
}

.fullscreen-preview .preview-container {
    max-width: 800px;
    margin: 0 auto;
}

.fullscreen-close {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 10000;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.fullscreen-close:hover {
    background: #c82333;
    transform: scale(1.1);
}

.preview-toggle {
    position: sticky;
    top: 20px;
    z-index: 100;
    margin-bottom: 1rem;
}

.preview-mode-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: bold;
}

@media (max-width: 768px) {
    .preview-stats {
        flex-direction: column;
        gap: 1rem;
    }

    .device-selector {
        flex-wrap: wrap;
    }

    .preview-body {
        padding: 1rem;
    }
}
</style>
@endsection

@section('content')
<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Fullscreen Preview -->
<div class="fullscreen-preview" id="fullscreenPreview">
    <button class="fullscreen-close" id="fullscreenClose" title="Close Fullscreen">
        <i class="fas fa-times"></i>
    </button>
    <div class="preview-container">
        <div class="preview-header">
            <h2 id="fullscreenPreviewTitle">Form Preview</h2>
            <p id="fullscreenPreviewDescription" class="mb-0 opacity-75"></p>
        </div>
        <div class="preview-body">
            <div id="fullscreenPreviewContent" class="preview-form">
                <!-- Fullscreen preview content -->
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus-circle me-2 text-primary"></i>
        Create My Dynamic Form
        <span class="draft-indicator" id="draftIndicator">(Draft)</span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-info" id="togglePreview">
                <i class="fas fa-eye me-2"></i>Show Live Preview
            </button>
            <button type="button" class="btn btn-outline-secondary" id="fullscreenBtn">
                <i class="fas fa-expand me-2"></i>Fullscreen
            </button>
        </div>
        <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to My Forms
        </a>
    </div>
</div>

<!-- Progress Bar -->
<div class="progress mb-4" style="height: 4px;">
    <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
</div>

<!-- Error Display -->
<div class="alert alert-danger alert-dismissible fade" role="alert" id="errorAlert" style="display: none;">
    <ul class="mb-0" id="errorList"></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Success Display -->
<div class="alert alert-success alert-dismissible fade" role="alert" id="successAlert" style="display: none;">
    <span id="successMessage"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<div class="row">
    <!-- Form Builder Column -->
    <div class="col-lg-6" id="builderColumn">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Form Builder
                    <span class="badge bg-secondary ms-2" id="fieldCount">0 fields</span>
                </h5>
            </div>

            <div class="card-body">
                <form id="dynamicFormCreate" novalidate>
                    @csrf

                    <!-- Basic Form Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-bold">
                                Form Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name"
                                   value="{{ old('name') }}" required autocomplete="off"
                                   placeholder="Enter a descriptive form name">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    <span class="badge bg-success" id="statusBadge">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Describe what this form is for...">{{ old('description') }}</textarea>
                        <div class="form-text">This will be shown to users at the top of your form</div>
                    </div>

                    <!-- Form Fields Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-list-ul me-2 text-primary"></i>
                                Form Fields
                            </h6>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm" id="addField">
                                    <i class="fas fa-plus me-1"></i>Add Field
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        Quick Add
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-field-type="text"><i class="fas fa-font me-2"></i>Text Field</a></li>
                                        <li><a class="dropdown-item" href="#" data-field-type="email"><i class="fas fa-envelope me-2"></i>Email Field</a></li>
                                        <li><a class="dropdown-item" href="#" data-field-type="textarea"><i class="fas fa-align-left me-2"></i>Textarea</a></li>
                                        <li><a class="dropdown-item" href="#" data-field-type="select"><i class="fas fa-list me-2"></i>Select Dropdown</a></li>
                                        <li><a class="dropdown-item" href="#" data-field-type="radio"><i class="fas fa-dot-circle me-2"></i>Radio Buttons</a></li>
                                        <li><a class="dropdown-item" href="#" data-field-type="checkbox"><i class="fas fa-check-square me-2"></i>Checkboxes</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Container for dynamic form fields -->
                        <div id="formFieldsContainer"></div>

                        <!-- Fields Container -->
                        <div id="fieldsContainer" class="sortable-container">
                            <div class="text-center py-5 text-muted" id="emptyState">
                                <i class="fas fa-plus-circle fa-3x mb-3 opacity-50"></i>
                                <h6>No fields added yet</h6>
                                <p class="small">Click "Add Field" to start building your form</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <div>
                            <button type="button" class="btn btn-outline-info" id="saveAsDraft">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('employees.dynamic-forms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>Create Form
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Preview Column -->
    <div class="col-lg-6" id="previewColumn" style="display: none;">
        <div class="preview-toggle">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2 text-info"></i>Live Preview
                </h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-info" id="refreshPreview">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="hidePreview">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Device Selector -->
        <div class="device-selector mb-3">
            <button type="button" class="device-btn active" data-device="desktop">
                <i class="fas fa-desktop me-1"></i> Desktop
            </button>
            <button type="button" class="device-btn" data-device="tablet">
                <i class="fas fa-tablet-alt me-1"></i> Tablet
            </button>
            <button type="button" class="device-btn" data-device="mobile">
                <i class="fas fa-mobile-alt me-1"></i> Mobile
            </button>
        </div>

        <!-- Preview Stats -->
        <div class="preview-stats">
            <div class="preview-stat">
                <div class="preview-stat-number" id="previewTotalFields">0</div>
                <div class="preview-stat-label">Total Fields</div>
            </div>
            <div class="preview-stat">
                <div class="preview-stat-number" id="previewRequiredFields">0</div>
                <div class="preview-stat-label">Required</div>
            </div>
            <div class="preview-stat">
                <div class="preview-stat-number" id="previewOptionalFields">0</div>
                <div class="preview-stat-label">Optional</div>
            </div>
            <div class="preview-stat">
                <div class="preview-stat-number" id="previewEstimatedTime">0</div>
                <div class="preview-stat-label">Est. Time (min)</div>
            </div>
        </div>

        <!-- Preview Container -->
        <div class="preview-device-frame desktop" id="previewDeviceFrame">
            <div class="preview-container">
                <div class="preview-mode-indicator">LIVE PREVIEW</div>
                <div class="preview-header">
                    <h2 id="previewTitle">Form Preview</h2>
                    <p id="previewDescription" class="mb-0 opacity-75">Add a description to see it here</p>
                </div>
                <div class="preview-body">
                    <div id="previewContent" class="preview-form">
                        <div class="preview-empty">
                            <i class="fas fa-eye"></i>
                            <h5>No preview available</h5>
                            <p>Add some fields to see the live preview</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (shown when preview is hidden) -->
    <div class="col-lg-6" id="sidebarColumn">
        <!-- Form Statistics -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-info bg-opacity-10">
                <h6 class="mb-0 text-info">
                    <i class="fas fa-chart-bar me-2"></i>Form Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 mb-0 text-primary" id="totalFields">0</div>
                        <small class="text-muted">Fields</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0 text-success" id="requiredFields">0</div>
                        <small class="text-muted">Required</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0 text-info" id="optionalFields">0</div>
                        <small class="text-muted">Optional</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary bg-opacity-10">
                <h6 class="mb-0 text-primary">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm quick-add-field" data-field-type="text">
                        <i class="fas fa-font me-2"></i>Add Text Field
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm quick-add-field" data-field-type="email">
                        <i class="fas fa-envelope me-2"></i>Add Email Field
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm quick-add-field" data-field-type="select">
                        <i class="fas fa-list me-2"></i>Add Select Field
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm quick-add-field" data-field-type="textarea">
                        <i class="fas fa-align-left me-2"></i>Add Textarea
                    </button>
                </div>
            </div>
        </div>

        <!-- Guidelines Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning bg-opacity-10 border-warning">
                <h6 class="mb-0 text-warning-emphasis">
                    <i class="fas fa-info-circle me-2"></i>Field Guidelines
                </h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-3 list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Each field requires a clear label</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Options needed for Select/Radio/Checkbox</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Use drag handles to reorder fields</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Preview your form before saving</li>
                </ul>
                <div class="alert alert-info small mb-0">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Pro Tip:</strong> Use the live preview to see changes in real-time!
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Template -->
<template id="fieldTemplate">
    <div class="field-item card mb-3 border-start border-4 border-primary border-opacity-25" data-field-index="__INDEX__">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-grip-vertical drag-handle me-2" title="Drag to reorder" aria-label="Drag to reorder field" role="button"></i>
                <span class="field-type-icon">📝</span>
                <strong class="field-title">New Field</strong>
                <span class="badge bg-secondary ms-2 field-type-badge">Text</span>
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary btn-sm toggle-collapse" title="Collapse/Expand">
                    <i class="fas fa-chevron-up"></i>
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm duplicate-field" title="Duplicate Field">
                    <i class="fas fa-copy"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm remove-field" title="Remove Field">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body collapse-content">
            <div class="row g-3">
                <input type="hidden" class="field-id" name="fields[__INDEX__][field_id]" value="">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Field Label <span class="text-danger">*</span></label>
                    <input type="text" class="form-control field-label" name="fields[__INDEX__][field_label]" placeholder="Enter field label" required autocomplete="off">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Field Type <span class="text-danger">*</span></label>
                    <select class="form-control field-type" name="fields[__INDEX__][field_type]" required autocomplete="off">
                        <option value="">Select Type</option>
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="textarea">Textarea</option>
                        <option value="select">Select</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="file">File</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input class="form-check-input field-required" type="checkbox" name="fields[__INDEX__][is_required]" value="1" autocomplete="off">
                        <label class="form-check-label">Required Field</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Field Order</label>
                    <input type="number" class="form-control field-order" name="fields[__INDEX__][sort_order]" value="__INDEX__" min="1" autocomplete="off">
                </div>
                <div class="col-md-12 mb-3 field-options-group" style="display: none;">
                    <label for="fields[__INDEX__][field_options]" class="form-label fw-bold">Field Options (one per line) <span class="text-danger">*</span></label>
                    <textarea class="form-control field-options" name="fields[__INDEX__][field_options]" rows="3" placeholder="Option 1\nOption 2\nOption 3" autocomplete="off"></textarea>
                    <small class="form-text text-muted">Enter each option on a new line (e.g., Option 1, Option 2)</small>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Placeholder (optional)</label>
                    <input type="text" class="form-control field-placeholder" name="fields[__INDEX__][placeholder]" placeholder="Enter placeholder text" autocomplete="off">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Help Text (optional)</label>
                    <textarea class="form-control field-help-text" name="fields[__INDEX__][help_text]" rows="2" autocomplete="off"></textarea>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<!-- Axios CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
class DynamicFormBuilder {
    constructor() {
        this.fieldIndex = {{ old('fields') ? count(old('fields')) : 0 }};
        this.fields = [];
        this.sortable = null;
        this.currentDevice = 'desktop';
        this.previewVisible = false;
        this.isDraft = false;
        this.isSavingAsDraft = false;

        this.init();
        this.bindEvents();
        this.loadOldFields();
        this.initSortable();
        this.updatePreview();
    }

    init() {
        this.elements = {
            form: document.getElementById('dynamicFormCreate'),
            fieldsContainer: document.getElementById('fieldsContainer'),
            addFieldBtn: document.getElementById('addField'),
            templateElement: document.getElementById('fieldTemplate'),
            emptyState: document.getElementById('emptyState'),
            fieldCount: document.getElementById('fieldCount'),
            totalFields: document.getElementById('totalFields'),
            requiredFields: document.getElementById('requiredFields'),
            optionalFields: document.getElementById('optionalFields'),
            previewContent: document.getElementById('previewContent'),
            previewTitle: document.getElementById('previewTitle'),
            previewDescription: document.getElementById('previewDescription'),
            errorAlert: document.getElementById('errorAlert'),
            successAlert: document.getElementById('successAlert'),
            progressBar: document.getElementById('progressBar'),
            previewDeviceFrame: document.getElementById('previewDeviceFrame'),
            fullscreenPreview: document.getElementById('fullscreenPreview'),
            fullscreenPreviewContent: document.getElementById('fullscreenPreviewContent'),
            fullscreenPreviewTitle: document.getElementById('fullscreenPreviewTitle'),
            fullscreenPreviewDescription: document.getElementById('fullscreenPreviewDescription'),
            builderColumn: document.getElementById('builderColumn'),
            previewColumn: document.getElementById('previewColumn'),
            sidebarColumn: document.getElementById('sidebarColumn'),
            togglePreviewBtn: document.getElementById('togglePreview'),
            draftIndicator: document.getElementById('draftIndicator'),
            saveAsDraftBtn: document.getElementById('saveAsDraft')
        };

        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    bindEvents() {
        this.elements.addFieldBtn?.addEventListener('click', () => this.addField());
        document.querySelectorAll('[data-field-type]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.addField(e.target.closest('[data-field-type]').dataset.fieldType);
            });
        });
        document.querySelectorAll('.quick-add-field').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.addField(e.target.dataset.fieldType);
            });
        });
        this.elements.form?.addEventListener('submit', (e) => {
            console.log('Form submit triggered, isSavingAsDraft:', this.isSavingAsDraft, 'is_active:', this.elements.form.querySelector('#is_active')?.checked);
            this.handleSubmit(e);
        });
        this.elements.saveAsDraftBtn?.addEventListener('click', () => {
            this.isSavingAsDraft = true;
            console.log('Save as Draft clicked, isSavingAsDraft:', this.isSavingAsDraft);
            this.saveAsDraft();
        });
        this.elements.togglePreviewBtn?.addEventListener('click', () => this.togglePreview());
        document.getElementById('hidePreview')?.addEventListener('click', () => this.hidePreview());
        document.getElementById('refreshPreview')?.addEventListener('click', () => this.updatePreview());
        document.getElementById('fullscreenBtn')?.addEventListener('click', () => this.showFullscreenPreview());
        document.getElementById('fullscreenClose')?.addEventListener('click', () => this.hideFullscreenPreview());
        document.querySelectorAll('.device-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.changeDevice(e.target.closest('.device-btn').dataset.device));
        });
        document.getElementById('name')?.addEventListener('input', () => this.updatePreview());
        document.getElementById('description')?.addEventListener('input', () => this.updatePreview());
        document.getElementById('is_active')?.addEventListener('change', (e) => {
            const badge = document.getElementById('statusBadge');
            if (e.target.checked) {
                badge.textContent = 'Active';
                badge.className = 'badge bg-success';
                this.elements.draftIndicator.textContent = '';
            } else {
                badge.textContent = 'Inactive';
                badge.className = 'badge bg-secondary';
                this.elements.draftIndicator.textContent = '(Draft)';
            }
            console.log('is_active changed, checked:', e.target.checked);
            this.updatePreview();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.elements.fullscreenPreview.style.display !== 'none') {
                this.hideFullscreenPreview();
            }
        });
    }

    initSortable() {
        if (this.elements.fieldsContainer) {
            this.sortable = Sortable.create(this.elements.fieldsContainer, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'dragging',
                onEnd: () => {
                    this.reorderFields();
                    this.updateStatistics();
                    this.updatePreview();
                }
            });
        }
    }

    togglePreview() {
        if (this.previewVisible) {
            this.hidePreview();
        } else {
            this.showPreview();
        }
    }

    showPreview() {
        this.previewVisible = true;
        this.elements.builderColumn.className = 'col-lg-6';
        this.elements.previewColumn.style.display = 'block';
        this.elements.sidebarColumn.style.display = 'none';
        this.elements.togglePreviewBtn.innerHTML = '<i class="fas fa-eye-slash me-2"></i>Hide Live Preview';
        this.elements.togglePreviewBtn.className = 'btn btn-info';
        this.updatePreview();
        this.showToast('Live preview enabled!', 'success');
    }

    hidePreview() {
        this.previewVisible = false;
        this.elements.builderColumn.className = 'col-lg-6';
        this.elements.previewColumn.style.display = 'none';
        this.elements.sidebarColumn.style.display = 'block';
        this.elements.togglePreviewBtn.innerHTML = '<i class="fas fa-eye me-2"></i>Show Live Preview';
        this.elements.togglePreviewBtn.className = 'btn btn-outline-info';
        this.showToast('Live preview disabled', 'info');
    }

    changeDevice(device) {
        this.currentDevice = device;
        document.querySelectorAll('.device-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-device="${device}"]`).classList.add('active');
        this.elements.previewDeviceFrame.className = `preview-device-frame ${device}`;
        this.showToast(`Switched to ${device} view`, 'info');
    }

    showFullscreenPreview() {
        this.elements.fullscreenPreview.style.display = 'block';
        this.updatePreview();
        this.showToast('Fullscreen preview enabled', 'info');
    }

    hideFullscreenPreview() {
        this.elements.fullscreenPreview.style.display = 'none';
        this.showToast('Fullscreen preview closed', 'info');
    }

    addField(type = 'text', fieldData = null) {
        if (!this.elements.templateElement || !this.elements.fieldsContainer) {
            console.error('Required elements not found');
            return;
        }

        let templateContent = this.elements.templateElement.innerHTML;
        templateContent = templateContent.replace(/__INDEX__/g, this.fieldIndex);

        const wrapper = document.createElement('div');
        wrapper.innerHTML = templateContent;
        const fieldCard = wrapper.firstElementChild;

        if (this.elements.emptyState) {
            this.elements.emptyState.style.display = 'none';
        }

        this.elements.fieldsContainer.appendChild(fieldCard);

        const typeSelect = fieldCard.querySelector('.field-type');
        if (typeSelect) {
            typeSelect.value = type;
            this.handleFieldTypeChange(typeSelect);
        }

        if (fieldData) {
            this.populateField(fieldCard, fieldData);
        }

        this.attachFieldListeners(fieldCard);

        this.fieldIndex++;
        this.updateStatistics();
        this.updatePreview();

        fieldCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        this.showToast(`${this.getFieldTypeLabel(type)} field added!`, 'success');

        if (this.fieldIndex === 1 && !this.previewVisible) {
            setTimeout(() => this.showPreview(), 500);
        }
    }

    populateField(fieldCard, fieldData) {
        const selectors = {
            '.field-id': fieldData.field_id || '',
            '.field-label': fieldData.field_label || '',
            '.field-type': fieldData.field_type || 'text',
            '.field-order': fieldData.sort_order || this.fieldIndex,
            '.field-options': Array.isArray(fieldData.field_options)
                ? fieldData.field_options.join('\n')
                : (fieldData.field_options || ''),
            '.field-placeholder': fieldData.placeholder || '',
            '.field-help-text': fieldData.help_text || ''
        };

        Object.entries(selectors).forEach(([selector, value]) => {
            const element = fieldCard.querySelector(selector);
            if (element) {
                element.value = value;
            }
        });

        const requiredCheckbox = fieldCard.querySelector('.field-required');
        if (requiredCheckbox && fieldData.is_required) {
            requiredCheckbox.checked = true;
        }

        const titleElement = fieldCard.querySelector('.field-title');
        if (titleElement) {
            titleElement.textContent = fieldData.field_label || 'New Field';
        }

        this.handleFieldTypeChange(fieldCard.querySelector('.field-type'));
    }

    attachFieldListeners(fieldCard) {
        const removeBtn = fieldCard.querySelector('.remove-field');
        removeBtn?.addEventListener('click', () => this.removeField(fieldCard));

        const duplicateBtn = fieldCard.querySelector('.duplicate-field');
        duplicateBtn?.addEventListener('click', () => this.duplicateField(fieldCard));

        const toggleBtn = fieldCard.querySelector('.toggle-collapse');
        toggleBtn?.addEventListener('click', () => this.toggleFieldCollapse(fieldCard));

        const labelInput = fieldCard.querySelector('.field-label');
        labelInput?.addEventListener('input', (e) => {
            const title = fieldCard.querySelector('.field-title');
            if (title) {
                title.textContent = e.target.value || 'New Field';
            }
            this.updatePreview();
        });

        const typeSelect = fieldCard.querySelector('.field-type');
        typeSelect?.addEventListener('change', (e) => this.handleFieldTypeChange(e.target));

        const inputs = fieldCard.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => this.updatePreview());
        });

        const requiredCheckbox = fieldCard.querySelector('.field-required');
        requiredCheckbox?.addEventListener('change', () => {
            this.updateStatistics();
            this.updatePreview();
        });
    }

    handleFieldTypeChange(selectElement) {
        const fieldCard = selectElement.closest('.field-item');
        const optionsContainer = fieldCard.querySelector('.field-options-group');
        const typeBadge = fieldCard.querySelector('.field-type-badge');
        const typeIcon = fieldCard.querySelector('.field-type-icon');

        const fieldType = selectElement.value;
        const needsOptions = ['select', 'radio', 'checkbox'].includes(fieldType);

        if (optionsContainer) {
            optionsContainer.style.display = needsOptions ? 'block' : 'none';
            const optionsTextarea = optionsContainer.querySelector('.field-options');
            if (optionsTextarea) {
                optionsTextarea.required = needsOptions;
            }
        }

        if (typeBadge) {
            typeBadge.textContent = this.getFieldTypeLabel(fieldType);
        }

        if (typeIcon) {
            typeIcon.textContent = this.getFieldTypeIcon(fieldType);
        }

        this.updatePreview();
    }

    getFieldTypeLabel(type) {
        const labels = {
            'text': 'Text',
            'email': 'Email',
            'number': 'Number',
            'date': 'Date',
            'textarea': 'Textarea',
            'select': 'Select',
            'radio': 'Radio',
            'checkbox': 'Checkbox',
            'file': 'File'
        };
        return labels[type] || 'Text';
    }

    getFieldTypeIcon(type) {
        const icons = {
            'text': '📝',
            'email': '📧',
            'number': '🔢',
            'date': '📅',
            'textarea': '📄',
            'select': '📋',
            'radio': '🔘',
            'checkbox': '☑️',
            'file': '📎'
        };
        return icons[type] || '📝';
    }

    removeField(fieldCard) {
        if (confirm('Are you sure you want to remove this field?')) {
            fieldCard.remove();
            this.updateStatistics();
            this.updatePreview();
            if (this.elements.fieldsContainer.children.length === 0) {
                this.elements.emptyState.style.display = 'block';
            }
            this.showToast('Field removed successfully!', 'warning');
        }
    }

    duplicateField(fieldCard) {
        const fieldData = this.extractFieldData(fieldCard);
        fieldData.field_label = fieldData.field_label + ' (Copy)';
        this.addField(fieldData.field_type, fieldData);
    }

    toggleFieldCollapse(fieldCard) {
        const content = fieldCard.querySelector('.collapse-content');
        const icon = fieldCard.querySelector('.toggle-collapse i');
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.className = 'fas fa-chevron-up';
            fieldCard.classList.remove('field-collapsed');
        } else {
            content.style.display = 'none';
            icon.className = 'fas fa-chevron-down';
            fieldCard.classList.add('field-collapsed');
        }
    }

    extractFieldData(fieldCard) {
        return {
            field_id: fieldCard.querySelector('.field-id')?.value || '',
            field_label: fieldCard.querySelector('.field-label')?.value || '',
            field_type: fieldCard.querySelector('.field-type')?.value || 'text',
            is_required: fieldCard.querySelector('.field-required')?.checked || false,
            sort_order: fieldCard.querySelector('.field-order')?.value || 0,
            field_options: fieldCard.querySelector('.field-options')?.value || '',
            placeholder: fieldCard.querySelector('.field-placeholder')?.value || '',
            help_text: fieldCard.querySelector('.field-help-text')?.value || ''
        };
    }

    updateStatistics() {
        const fields = this.elements.fieldsContainer.querySelectorAll('.field-item');
        const total = fields.length;
        let required = 0;
        fields.forEach(field => {
            if (field.querySelector('.field-required')?.checked) {
                required++;
            }
        });
        const optional = total - required;
        const estimatedTime = Math.ceil(total * 0.5);

        if (this.elements.fieldCount) this.elements.fieldCount.textContent = `${total} field${total !== 1 ? 's' : ''}`;
        if (this.elements.totalFields) this.elements.totalFields.textContent = total;
        if (this.elements.requiredFields) this.elements.requiredFields.textContent = required;
        if (this.elements.optionalFields) this.elements.optionalFields.textContent = optional;

        document.getElementById('previewTotalFields').textContent = total;
        document.getElementById('previewRequiredFields').textContent = required;
        document.getElementById('previewOptionalFields').textContent = optional;
        document.getElementById('previewEstimatedTime').textContent = estimatedTime;

        if (this.elements.progressBar) {
            const progress = total > 0 ? Math.min(33 + (total * 10), 100) : 33;
            this.elements.progressBar.style.width = progress + '%';
        }
    }

    reorderFields() {
        const fields = this.elements.fieldsContainer.querySelectorAll('.field-item');
        fields.forEach((field, index) => {
            const orderInput = field.querySelector('.field-order');
            if (orderInput) {
                orderInput.value = index + 1;
            }
        });
    }

    updatePreview() {
        const formName = document.getElementById('name')?.value || 'Form Preview';
        const formDescription = document.getElementById('description')?.value || 'Add a description to see it here';

        if (this.elements.previewTitle) {
            this.elements.previewTitle.textContent = formName;
        }
        if (this.elements.previewDescription) {
            this.elements.previewDescription.textContent = formDescription;
            this.elements.previewDescription.style.display = formDescription.trim() ? 'block' : 'none';
        }
        if (this.elements.fullscreenPreviewTitle) {
            this.elements.fullscreenPreviewTitle.textContent = formName;
        }
        if (this.elements.fullscreenPreviewDescription) {
            this.elements.fullscreenPreviewDescription.textContent = formDescription;
        }

        const fields = this.elements.fieldsContainer.querySelectorAll('.field-item');
        let previewHTML = '';

        if (fields.length === 0) {
            previewHTML = `
                <div class="preview-empty">
                    <i class="fas fa-eye"></i>
                    <h5>No preview available</h5>
                    <p>Add some fields to see the live preview</p>
                    <button type="button" class="btn btn-primary btn-sm mt-2" onclick="document.getElementById('addField').click()">
                        <i class="fas fa-plus me-1"></i>Add Your First Field
                    </button>
                </div>
            `;
        } else {
            previewHTML = '<form class="needs-validation" novalidate>';
            const sortedFields = Array.from(fields).sort((a, b) => {
                const orderA = parseInt(a.querySelector('.field-order')?.value || 0);
                const orderB = parseInt(b.querySelector('.field-order')?.value || 0);
                return orderA - orderB;
            });

            sortedFields.forEach(field => {
                const fieldData = this.extractFieldData(field);
                previewHTML += this.generateFieldHTML(fieldData);
            });

            previewHTML += `
                <div class="d-grid gap-2 mt-4">
                    <button type="button" class="btn preview-submit-btn btn-lg" disabled aria-disabled="true">
                        <i class="fas fa-paper-plane me-2"></i>Submit Form (Preview)
                    </button>
                </div>
            </form>`;
        }

        if (this.elements.previewContent) {
            this.elements.previewContent.innerHTML = previewHTML;
        }
        if (this.elements.fullscreenPreviewContent) {
            this.elements.fullscreenPreviewContent.innerHTML = previewHTML;
        }
    }

    generateFieldHTML(fieldData) {
        const { field_label, field_type, is_required, placeholder, help_text, field_options } = fieldData;
        const requiredAttr = is_required ? 'required' : '';
        const requiredMark = is_required ? '<span class="preview-field-required">*</span>' : '';

        let fieldHTML = `
            <div class="preview-field">
                <label class="preview-field-label">${field_label || 'Untitled Field'} ${requiredMark}</label>
        `;

        switch (field_type) {
            case 'textarea':
                fieldHTML += `<textarea class="form-control" placeholder="${placeholder}" ${requiredAttr} rows="4"></textarea>`;
                break;
            case 'select':
                const selectOptions = field_options ? field_options.split('\n').filter(opt => opt.trim()) : ['Option 1', 'Option 2'];
                fieldHTML += `<select class="form-select" ${requiredAttr}>
                    <option value="">${placeholder || 'Choose...'}</option>
                    ${selectOptions.map(opt => `<option value="${opt.trim()}">${opt.trim()}</option>`).join('')}
                </select>`;
                break;
            case 'radio':
                const radioOptions = field_options ? field_options.split('\n').filter(opt => opt.trim()) : ['Option 1', 'Option 2'];
                const radioName = (field_label || 'field').toLowerCase().replace(/\s+/g, '_') + '_' + Date.now();
                radioOptions.forEach((opt, index) => {
                    fieldHTML += `
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="${radioName}" id="radio_${radioName}_${index}" ${requiredAttr}>
                            <label class="form-check-label" for="radio_${radioName}_${index}">${opt.trim()}</label>
                        </div>
                    `;
                });
                break;
            case 'checkbox':
                const checkboxOptions = field_options ? field_options.split('\n').filter(opt => opt.trim()) : ['Option 1', 'Option 2'];
                checkboxOptions.forEach((opt, index) => {
                    const checkboxId = `checkbox_${(field_label || 'field').toLowerCase().replace(/\s+/g, '_')}_${index}_${Date.now()}`;
                    fieldHTML += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="${checkboxId}">
                            <label class="form-check-label" for="${checkboxId}">${opt.trim()}</label>
                        </div>
                    `;
                });
                break;
            default:
                fieldHTML += `<input type="${field_type}" class="form-control" placeholder="${placeholder}" ${requiredAttr}>`;
        }

        if (help_text) {
            fieldHTML += `<div class="preview-field-help">${help_text}</div>`;
        }

        fieldHTML += '</div>';
        return fieldHTML;
    }

    validateForm() {
        const errors = {};
        const nameInput = document.getElementById('name');
        if (!nameInput.value.trim()) {
            errors.name = 'Form name is required';
            nameInput.classList.add('validation-error');
        } else {
            nameInput.classList.remove('validation-error');
        }

        const fields = this.elements.fieldsContainer.querySelectorAll('.field-item');
        if (fields.length === 0) {
            errors.fields = 'At least one field is required';
        }

        fields.forEach((field, index) => {
            const labelInput = field.querySelector('.field-label');
            const typeSelect = field.querySelector('.field-type');
            const optionsTextarea = field.querySelector('.field-options');
            const orderInput = field.querySelector('.field-order');

            if (!labelInput.value.trim()) {
                errors[`field_${index}_label`] = `Field ${index + 1} label is required`;
                labelInput.classList.add('validation-error');
            } else {
                labelInput.classList.remove('validation-error');
            }

            if (!typeSelect.value) {
                errors[`field_${index}_type`] = `Field ${index + 1} type is required`;
                typeSelect.classList.add('validation-error');
            } else {
                typeSelect.classList.remove('validation-error');
            }

            if (!orderInput.value || isNaN(orderInput.value) || parseInt(orderInput.value) < 0) {
                errors[`field_${index}_order`] = `Field ${index + 1} order must be a valid number ≥ 0`;
                orderInput.classList.add('validation-error');
            } else {
                orderInput.classList.remove('validation-error');
            }

            const needsOptions = ['select', 'radio', 'checkbox'].includes(typeSelect.value);
            if (needsOptions && (!optionsTextarea.value.trim() || optionsTextarea.value.split('\n').filter(opt => opt.trim()).length < 2)) {
                errors[`field_${index}_options`] = `Field ${index + 1} requires at least 2 options`;
                optionsTextarea.classList.add('validation-error');
            } else if (optionsTextarea) {
                optionsTextarea.classList.remove('validation-error');
            }
        });

        console.log('Frontend validation result:', {
            isValid: Object.keys(errors).length === 0,
            errors: errors
        });
        return { isValid: Object.keys(errors).length === 0, errors };
    }

    showErrors(errors) {
        const errorList = document.getElementById('errorList');
        const errorAlert = this.elements.errorAlert;

        if (errorList && errorAlert) {
            errorList.innerHTML = '';
            Object.entries(errors).forEach(([key, error]) => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            errorAlert.classList.add('show');
            errorAlert.style.display = 'block';
            errorAlert.scrollIntoView({ behavior: 'smooth' });
        }
    }

    hideErrors() {
        const errorAlert = this.elements.errorAlert;
        if (errorAlert) {
            errorAlert.classList.remove('show');
            errorAlert.style.display = 'none';
        }
    }

    showSuccess(message) {
        const successAlert = this.elements.successAlert;
        const successMessage = document.getElementById('successMessage');

        if (successAlert && successMessage) {
            successMessage.textContent = message;
            successAlert.classList.add('show');
            successAlert.style.display = 'block';
            successAlert.scrollIntoView({ behavior: 'smooth' });
        }
    }

    showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        const toastId = 'toast_' + Date.now();
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        }[type] || 'bg-info';

        const toastHTML = `
            <div class="toast ${bgClass} text-white" id="${toastId}" role="alert">
                <div class="toast-body d-flex justify-content-between align-items-center">
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    saveAsDraft() {
        this.isSavingAsDraft = true;
        console.log('Save as Draft clicked, isSavingAsDraft:', this.isSavingAsDraft);
        this.elements.form.dispatchEvent(new Event('submit'));
    }

    async handleSubmit(e) {
        e.preventDefault();

        const validation = this.validateForm();
        if (!validation.isValid) {
            this.showErrors(validation.errors);
            this.showToast('Please fix the errors before submitting.', 'error');
            return;
        }

        this.hideErrors();
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

        try {
            const formData = new FormData(this.elements.form);
            const isActiveChecked = this.elements.form.querySelector('#is_active')?.checked || false;
            const isDraftValue = this.isSavingAsDraft || !isActiveChecked;
            formData.append('is_draft', isDraftValue ? '1' : '0');

            // Log FormData contents
            const formDataEntries = {};
            for (let [key, value] of formData.entries()) {
                formDataEntries[key] = value instanceof File ? `[File: ${value.name}]` : value;
            }
            console.log('FormData being sent:', JSON.stringify(formDataEntries, null, 2));

            const fieldItems = this.elements.fieldsContainer.querySelectorAll('.field-item');
            fieldItems.forEach((item, index) => {
                const type = item.querySelector('.field-type').value;
                if (['select', 'radio', 'checkbox'].includes(type)) {
                    const optionsInput = item.querySelector('.field-options');
                    if (optionsInput && optionsInput.value.trim()) {
                        const cleanedOptions = optionsInput.value
                            .split('\n')
                            .map(opt => opt.replace(/[\x00-\x1F\x7F]/g, '').trim())
                            .filter(opt => opt)
                            .join('\n');
                        formData.set(`fields[${index}][field_options]`, cleanedOptions);
                    }
                }
            });

            console.log('Cleaned FormData for submission:', JSON.stringify([...formData.entries()].reduce((obj, [key, value]) => {
                obj[key] = value instanceof File ? `[File: ${value.name}]` : value;
                return obj;
            }, {}), null, 2));

            const response = await axios.post('{{ route("employees.dynamic-forms.store") }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }

            console.log('Server response:', JSON.stringify(response.data, null, 2));

            if (response.data.success) {
                this.showSuccess(response.data.message);
                this.showToast(response.data.message, 'success');
                setTimeout(() => {
                    window.location.href = response.data.redirect || '{{ route("employees.dynamic-forms.index") }}';
                }, 2000);
            } else {
                console.error('Server returned failure:', response.data);
                this.showErrors(response.data.errors || { general: 'An error occurred while creating the form' });
                this.showToast('Form creation failed.', 'error');
            }
        } catch (error) {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
            console.error('Form submission error:', error);
            console.log('Full server response:', JSON.stringify(error.response?.data, null, 2));
            let errors = { general: 'An unexpected error occurred. Please try again.' };
            if (error.response?.status === 422) {
                errors = error.response.data.errors || { general: 'Validation failed. Please check your inputs.' };
                console.log('Validation errors from server:', JSON.stringify(errors, null, 2));
                this.showErrors(errors);
                this.showToast('Validation failed. Please check your inputs.', 'error');
            } else if (error.response?.status === 500) {
                errors = { general: 'Server error. Please try again later.' };
                console.log('Server error details:', error.response?.data);
                this.showErrors(errors);
                this.showToast('Server error. Please try again later.', 'error');
            } else {
                console.error('Unexpected error:', error.message);
                this.showErrors(errors);
                this.showToast('An unexpected error occurred.', 'error');
            }
        }
    }

    loadOldFields() {
        @if (old('fields'))
            const oldFields = @json(old('fields'));
            oldFields.forEach((fieldData, index) => {
                this.addField(fieldData.field_type, fieldData);
            });
        @endif
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.formBuilder = new DynamicFormBuilder();
});
</script>
@endpush
@endsection