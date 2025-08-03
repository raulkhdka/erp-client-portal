@extends('layouts.app')

@section('title', 'Edit Document')

@section('breadcrumb')
    <a href="{{ route('admin.documents.index') }}">Documents</a>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>View Document
        </a>
        <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Documents
        </a>
    </div>
@endsection

@section('content')
<div class="container mt-5">
    <h2>Edit Document</h2>

    {{-- This section will contain just a button to open the modal --}}
    <p>Click the button below to edit the document details.</p>
    <button type="button" class="btn btn-primary" onclick="openEditModal()">Edit Document</button>
</div>

{{-- Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="editModalForm" class="modal-content" enctype="multipart/form-data">
            @csrf
            {{-- We will send the PUT method via Axios, so no @method('PUT') here --}}

            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                {{-- Hidden input for method spoofing for Axios PUT request --}}
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label for="modalTitle">Document Name</label>
                    <input type="text" name="title" id="modalTitle" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="modalDescription">Description (optional)</label>
                    <textarea name="description" id="modalDescription" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="modalFile">File (optional)</label>
                    <input type="file" name="file" id="modalFile" class="form-control-file">
                    <div id="modalFileName" class="mt-2"></div>
                </div>

                <div class="form-group">
                    <label for="modalCategory">Category</label>
                    <select name="categories_id" id="modalCategory" class="form-control">
                        <option value="">-- Select --</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalClient">Client</label>
                    <select name="client_id" id="modalClient" class="form-control">
                        <option value="">-- Select --</option>
                        @foreach ($clients as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalEmployee">Assigned Employee</label>
                    <select name="employee_id" id="modalEmployee" class="form-control">
                        <option value="">-- None --</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Access Permissions --}}
                <div class="form-group">
                    <label>Access Permissions</label>
                    <div>
                        <input type="checkbox" name="is_public" id="modalPublic" class="form-check-input" value="1">
                        <label for="modalPublic" class="form-check-label mr-3">Public</label>

                        <input type="checkbox" name="is_confidential" id="modalConfidential" class="form-check-input" value="1">
                        <label for="modalConfidential" class="form-check-label">Confidential</label>
                    </div>
                    <small class="form-text text-muted">
                        If neither is selected, the document will only be accessible to the assigned client, assigned employee, and internal staff.
                    </small>
                </div>
                {{-- You might also have specific user access permissions here if needed --}}
                {{-- Example:
                <div class="form-group">
                    <label for="modalAccessPermissions">Specific User Access</label>
                    <select name="access_permissions[]" id="modalAccessPermissions" class="form-control" multiple>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Select specific users who can access this document.</small>
                </div>
                --}}


                <div class="form-group mt-2">
                    <label for="modalExpires">Expiration Date</label>
                    <input type="date" name="expires_at" id="modalExpires" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="submitModalForm()" class="btn btn-success">Update Document</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include jQuery for Bootstrap Modals if not already included in layouts.app --}}
{{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script> --}}

{{-- Axios CDN --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatically open the modal when the page loads
        openEditModal();
    });

    function openEditModal() {
        const doc = @json($document); // Pass the document data to JavaScript

        // Populate modal fields
        document.getElementById('modalTitle').value = doc.title || '';
        document.getElementById('modalDescription').value = doc.description || '';
        document.getElementById('modalFileName').innerHTML = `<strong>Current File:</strong> ${doc.file_name}`;
        document.getElementById('modalCategory').value = doc.categories_id || '';
        document.getElementById('modalClient').value = doc.client_id || '';
        document.getElementById('modalEmployee').value = doc.employee_id || '';
        document.getElementById('modalPublic').checked = doc.is_public;
        document.getElementById('modalConfidential').checked = doc.is_confidential;
        document.getElementById('modalExpires').value = doc.expires_at ? doc.expires_at.split(' ')[0] : '';

        // Handle specific user access permissions if you add them
        // if (doc.access_permissions && Array.isArray(doc.access_permissions)) {
        //     const selectElement = document.getElementById('modalAccessPermissions');
        //     Array.from(selectElement.options).forEach(option => {
        //         option.selected = doc.access_permissions.includes(parseInt(option.value));
        //     });
        // }

        $('#editModal').modal('show'); // Show the Bootstrap modal
    }

    function submitModalForm() {
        const form = document.getElementById('editModalForm');
        const formData = new FormData(form);

        // Add _method spoofing for PUT request as FormData doesn't natively support PUT
        // This is handled by the hidden input: <input type="hidden" name="_method" value="PUT">

        axios.post("{{ route('admin.documents.update', $document->id) }}", formData, {
            headers: {
                'Content-Type': 'multipart/form-data' // Important for file uploads
            }
        })
        .then(response => {
            // Success: Redirect to the index page with a success message
            window.location.href = "{{ route('admin.documents.index') }}?message=Document+updated+successfully.";
        })
        .catch(error => {
            console.error('Error updating document:', error.response || error);
            let errorMessage = 'Failed to update document.';
            if (error.response && error.response.data && error.response.data.errors) {
                // Display validation errors from Laravel
                errorMessage += '\n\nValidation Errors:\n';
                for (const key in error.response.data.errors) {
                    errorMessage += `- ${error.response.data.errors[key].join(', ')}\n`;
                }
            } else if (error.response && error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            }
            alert(errorMessage);
        });
    }

    // You can add a listener to redirect or perform actions when the modal is closed without submitting
    $('#editModal').on('hidden.bs.modal', function (e) {
        // If the user navigates directly to /documents/{id}/edit, and then closes the modal
        // they might be stuck on a blank page. You might want to redirect them to index.
        // Or, if this 'edit' page is always accessed via a button on the index page,
        // then this might not be necessary.
        // For now, if they close the modal, we'll redirect them back to the index page.
        if (!document.getElementById('editModal').dataset.submitted) { // Prevent redirect if form was submitted
             window.location.href = "{{ route('admin.documents.index') }}";
        }
    });

</script>
@endpush
