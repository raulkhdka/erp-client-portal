@extends('layouts.app')

@section('content')
    <div class="py-4 px-3">
        <div class="d-flex justify-content-between align-items-center mb-3 px-3">
            <h2><i class="fas fa-folder me-2"></i> Document Category Details</h2>
            <div>
                <a href="{{ route('document-categories.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>

                <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                    <i class="fas fa-edit"></i> Edit Category
                </button>
                <form action="{{ route('document-categories.destroy', $documentCategory->id) }}" method="POST" class="d-inline-block"
                    onsubmit="return confirm('Are you sure you want to delete this category?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Category
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm mx-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Name</th>
                                <td>{{ $documentCategory->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Description</th>
                                <td>{{ $documentCategory->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Icon</th>
                                <td class="text-center">
                                    @if ($documentCategory->icon)
                                        <i class="{{ $documentCategory->icon }}" style="font-size: 1.2em;"></i>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Color</th>
                                <td class="text-center">
                                    @if ($documentCategory->color)
                                        <span class="badge"
                                            style="background-color: {{ $documentCategory->color }}; color: {{ \App\Helpers\ColorHelper::getTextColor($documentCategory->color) ?? '#ffffff' }};">
                                            {{ $documentCategory->color }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Active</th>
                                <td class="text-center">
                                    @if ($documentCategory->is_active)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Sort Order</th>
                                <td class="text-center">{{ $documentCategory->sort_order }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Documents</th>
                                <td class="text-center">{{ $documentCategory->documents_count }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Document Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" method="POST" action="{{ route('document-categories.update', $documentCategory->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name" value="{{ old('name', $documentCategory->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description" name="description" rows="3">{{ old('description', $documentCategory->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon (Font Awesome Class)</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="edit_icon" name="icon" value="{{ old('icon', $documentCategory->icon) }}" placeholder="e.g., fas fa-folder">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#iconPickerModal" data-target-input="#edit_icon">
                                    <i class="fas fa-folder-open"></i> Pick Icon
                                </button>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_color" class="form-label">Color (Hex Code)</label>
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" id="edit_color" name="color" value="{{ old('color', $documentCategory->color) }}">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="edit_sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="edit_sort_order" name="sort_order" value="{{ old('sort_order', $documentCategory->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active" value="1" {{ old('is_active', $documentCategory->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_active">Is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="iconPickerModalLabel">Select an Icon</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="iconSearchInput" placeholder="Search icons...">
                    </div>
                    <div id="iconGrid" class="row row-cols-4 row-cols-md-6 row-cols-lg-8 g-2 text-center">
                        <!-- Icons will be loaded here by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editCategoryModal = document.getElementById('editCategoryModal');
            editCategoryModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var name = '{{ $documentCategory->name }}';
                var description = '{{ $documentCategory->description ?? '' }}';
                var icon = '{{ $documentCategory->icon ?? '' }}';
                var color = '{{ $documentCategory->color ?? '' }}';
                var isActive = {{ $documentCategory->is_active ? '1' : '0' }};
                var sortOrder = '{{ $documentCategory->sort_order }}';

                var modalForm = editCategoryModal.querySelector('#editCategoryForm');

                modalForm.querySelector('#edit_name').value = name;
                modalForm.querySelector('#edit_description').value = description;
                modalForm.querySelector('#edit_icon').value = icon;
                modalForm.querySelector('#edit_color').value = color;
                modalForm.querySelector('#edit_sort_order').value = sortOrder;
                modalForm.querySelector('#edit_is_active').checked = (isActive === 1);

                @if ($errors->any())
                    var editModalErrors = @json($errors->any());
                    if (editModalErrors) {
                        var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                        editModal.show();
                    }
                @endif
            });

            // Icon Picker Logic
            const iconInputMap = {};
            const fontAwesomeIcons = [
                'fas fa-folder', 'fas fa-file', 'fas fa-book', 'fas fa-address-book',
                'fas fa-camera', 'fas fa-car', 'fas fa-chart-bar', 'fas fa-cloud',
                'fas fa-code', 'fas fa-cog', 'fas fa-comments', 'fas fa-credit-card',
                'fas fa-database', 'fas fa-desktop', 'fas fa-dollar-sign', 'fas fa-envelope',
                'fas fa-exchange-alt', 'fas fa-exclamation-triangle', 'fas fa-eye', 'fas fa-female',
                'fas fa-gavel', 'fas fa-globe', 'fas fa-graduation-cap', 'fas fa-heart',
                'fas fa-home', 'fas fa-image', 'fas fa-inbox', 'fas fa-info-circle',
                'fas fa-key', 'fas fa-laptop', 'fas fa-lightbulb', 'fas fa-lock',
                'fas fa-map-marker-alt', 'fas fa-microchip', 'fas fa-mobile-alt', 'fas fa-music',
                'fas fa-newspaper', 'fas fa-paint-brush', 'fas fa-paper-plane', 'fas fa-phone',
                'fas fa-play-circle', 'fas fa-print', 'fas fa-question-circle', 'fas fa-rocket',
                'fas fa-search', 'fas fa-share-alt', 'fas fa-shopping-cart', 'fas fa-sign-in-alt',
                'fas fa-sign-out-alt', 'fas fa-sitemap', 'fas fa-sliders-h', 'fas fa-star',
                'fas fa-sync-alt', 'fas fa-tag', 'fas fa-tasks', 'fas fa-terminal',
                'fas fa-th', 'fas fa-thumbs-up', 'fas fa-ticket-alt', 'fas fa-times',
                'fas fa-trash', 'fas fa-truck', 'fas fa-undo', 'fas fa-upload',
                'fas fa-user', 'fas fa-user-circle', 'fas fa-video', 'fas fa-wallet',
                'fas fa-wifi', 'fas fa-wrench', 'fas fa-bell', 'fas fa-calendar-alt',
                'fas fa-clipboard', 'fas fa-comment', 'fas fa-download', 'fas fa-edit',
                'fas fa-filter', 'fas fa-link', 'fas fa-list', 'fas fa-paperclip',
                'fas fa-plus', 'fas fa-minus', 'fas fa-eye-slash', 'fas fa-location-dot',
                'fas fa-magnifying-glass', 'fas fa-power-off', 'fas fa-shield-alt', 'fas fa-chart-line',
                'fas fa-clipboard-check', 'fas fa-dollar-sign', 'fas fa-euro-sign', 'fas fa-pound-sign',
                'fas fa-rupee-sign', 'fas fa-yen-sign', 'fas fa-coins', 'fas fa-piggy-bank',
                'fas fa-file-invoice', 'fas fa-receipt', 'fas fa-chart-pie', 'fas fa-chart-bar',
                'fas fa-book-open', 'fas fa-briefcase', 'fas fa-building', 'fas fa-university',
                'fas fa-calculator', 'fas fa-cash-register', 'fas fa-file-excel', 'fas fa-file-csv',
                'fas fa-handshake', 'fas fa-users', 'fas fa-user-tie', 'fas fa-phone-alt',
                'fas fa-clipboard-list', 'fas fa-concierge-bell', 'fas fa-file-alt', 'fas fa-tags'
            ];

            const iconGrid = document.getElementById('iconGrid');
            const iconSearchInput = document.getElementById('iconSearchInput');
            let currentTargetInput = null;

            function renderIcons(iconsToRender) {
                iconGrid.innerHTML = '';
                iconsToRender.forEach(iconClass => {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col icon-item mb-2';
                    colDiv.innerHTML = `
                        <div class="card h-100 p-2 border-0 shadow-sm icon-card" data-icon="${iconClass}">
                            <i class="${iconClass} fa-2x"></i>
                            <small class="mt-1 text-muted text-truncate">${iconClass.replace('fas fa-', '')}</small>
                        </div>
                    `;
                    iconGrid.appendChild(colDiv);
                });
            }

            renderIcons(fontAwesomeIcons);

            iconSearchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredIcons = fontAwesomeIcons.filter(iconClass => iconClass.includes(searchTerm));
                renderIcons(filteredIcons);
            });

            document.querySelectorAll('[data-bs-target="#iconPickerModal"]').forEach(button => {
                button.addEventListener('click', function() {
                    const targetInputSelector = this.getAttribute('data-target-input');
                    currentTargetInput = document.querySelector(targetInputSelector);
                    iconSearchInput.value = '';
                    renderIcons(fontAwesomeIcons);
                });
            });

            iconGrid.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                const card = event.target.closest('.icon-card');
                if (card && currentTargetInput) {
                    const selectedIconClass = card.getAttribute('data-icon');
                    currentTargetInput.value = selectedIconClass;

                    const iconPickerModal = bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'));
                    if (iconPickerModal) {
                        iconPickerModal.hide();
                    }

                    const parentModal = currentTargetInput.closest('.modal');
                    if (parentModal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(parentModal);
                        if (bootstrapModal) {
                            bootstrapModal.show();
                        }
                    }
                }
            });

            ['editCategoryForm'].forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', function(event) {
                        const submitButton = event.submitter;
                        if (!submitButton || !submitButton.classList.contains('btn-warning')) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });

                    form.closest('.modal').addEventListener('hidden.bs.modal', function(event) {
                        if (!event.target.querySelector('[type="submit"]:focus')) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                }
            });
        });
    </script>
    @endpush
@endsection