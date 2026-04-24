@extends('layouts.app')

@section('title', 'Complaints')

@section('content')
<div class="container mt-4 text-dark" style="background: rgba(255,255,255,0.95); color:#111; padding: 1.5rem; border-radius:8px;">
    <h3 class="mb-3">User Complaints</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Target</th>
                <th>Description</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->user->name ?? '—' }} ({{ $c->user->email ?? '—' }})</td>
                    <td>{{ $c->target_type ?? '—' }} {{ $c->target_id ? "(#{$c->target_id})" : '' }}</td>
                    <td style="max-width:360px; white-space:pre-wrap;">{{ Str::limit($c->description, 140) }}</td>
                    <td>{{ ucfirst($c->status) }}</td>
                    <td>{{ $c->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.complaint.show', $c->id) }}" class="btn btn-sm btn-primary">View</a>
                        <form action="{{ route('admin.complaint.delete', $c->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete complaint?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No complaints found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">{{ $complaints->links() }}</div>
</div>
@endsection
