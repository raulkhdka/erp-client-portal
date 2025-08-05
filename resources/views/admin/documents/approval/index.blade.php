@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Pending Document Approvals</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @php
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $isEmployee = $user->isEmployee();
    @endphp

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
                            <a href="{{ route('admin.documents.download', $document->id) }}"
                               class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Download
                            </a>

                            @if($isAdmin || $isEmployee)
                                <form action="{{ route('admin.documents.approve', $document->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                                            onclick="return confirm('Approve this document?')">
                                        Approve
                                    </button>
                                </form>

                                <form action="{{ route('admin.documents.reject', $document->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                            onclick="return confirm('Reject this document?')">
                                        Reject
                                    </button>
                                </form>
                            @endif

                            {{-- Optional: keep Review button to open modal if you want --}}
                            {{-- <button onclick="openModal({{ $document->id }}, '{{ $document->title }}')"
                                class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600">
                                Review
                            </button> --}}
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
