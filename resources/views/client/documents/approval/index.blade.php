@extends('layouts.app')

@section('title', 'Document Approvals')

@section('content')
    <h1 class="mt-4">Pending Document Approvals</h1>

    @if($documents->isEmpty())
        <p>No documents pending approval.</p>
    @else
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Client</th>
                    <th>Uploaded By</th>
                    <th>File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td>{{ $document->name }}</td>
                        <td>{{ $document->client->name }}</td>
                        <td>{{ $document->uploadedBy->name }}</td>
                        <td><a href="{{ route('client.documents.download', $document->id) }}" target="_blank">View</a></td>
                        <td>
                            <form method="POST" action="{{ route('documents.approve', $document) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Approve this document?')">Approve</button>
                            </form>

                            <form method="POST" action="{{ route('documents.reject', $document) }}" class="d-inline ms-2">
                                @csrf
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Reject this document?')">Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $documents->links() }}
    @endif
@endsection
