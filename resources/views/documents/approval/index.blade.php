@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Pending Document Approvals</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($documents->count() > 0)
        <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2">Client</th>
                    <th class="px-4 py-2">Uploaded At</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $document->title }}</td>
                        <td class="px-4 py-2">{{ $document->client->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $document->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 flex space-x-2">
                            <button onclick="openModal({{ $document->id }}, '{{ $document->title }}')"
                                class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600">
                                Review
                            </button>

                            <a href="{{ route('documents.download', $document->id) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Download
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    @else
        <p>No pending documents found.</p>
    @endif
</div>

<!-- Modal -->
<div id="approvalModal" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 id="modalTitle" class="text-xl font-semibold mb-4">Review Document</h2>

        <div class="flex justify-between space-x-4">
            <button id="approveBtn"
                class="bg-green-500 w-1/2 text-white px-3 py-2 rounded hover:bg-green-600">
                Approve
            </button>

            <button id="rejectBtn"
                class="bg-red-500 w-1/2 text-white px-3 py-2 rounded hover:bg-red-600">
                Reject
            </button>
        </div>

        <button onclick="closeModal()" class="mt-4 text-gray-500 hover:underline">Cancel</button>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let selectedDocumentId = null;

    function openModal(id, title) {
        selectedDocumentId = id;
        document.getElementById('modalTitle').innerText = 'Review Document: ' + title;
        document.getElementById('approvalModal').classList.remove('hidden');
    }

    function closeModal() {
        selectedDocumentId = null;
        document.getElementById('approvalModal').classList.add('hidden');
    }

    document.getElementById('approveBtn').addEventListener('click', function() {
        if (selectedDocumentId) {
            axios.post('/document-approvals/' + selectedDocumentId + '/approve', {
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                window.location.reload();
            })
            .catch(error => {
                alert('Error approving document');
                console.error(error);
            });
        }
    });

    document.getElementById('rejectBtn').addEventListener('click', function() {
        if (selectedDocumentId) {
            axios.post('/document-approvals/' + selectedDocumentId + '/reject', {
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                window.location.reload();
            })
            .catch(error => {
                alert('Error rejecting document');
                console.error(error);
            });
        }
    });
</script>
@endsection
