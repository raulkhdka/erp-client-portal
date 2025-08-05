<!-- Total Forms Modal -->
<div class="modal fade modal-modern" id="totalFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>All Forms ({{ $forms->total() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if ($form->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Active Forms Modal -->
<div class="modal fade modal-modern" id="activeFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Active Forms ({{ $forms->where('is_active', true)->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('is_active', true) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Inactive Forms Modal -->
<div class="modal fade modal-modern" id="inactiveFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pause-circle me-2"></i>Inactive Forms ({{ $forms->where('is_active', false)->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('is_active', false) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-danger">Inactive</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- This Week Forms Modal -->
<div class="modal fade modal-modern" id="thisWeekFormsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Forms Created This Week ({{ $forms->where('created_at', '>=', now()->subDays(7))->count() }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($forms->where('created_at', '>=', now()->subDays(7)) as $form)
                <div class="modal-list-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $form->name }}</h6>
                            <small class="text-muted">{{ $form->fields->count() }} fields • Created {{ $form->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if ($form->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
