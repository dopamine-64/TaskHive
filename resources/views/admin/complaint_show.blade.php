@extends('layouts.dashboard')

@section('title', 'Complaint #'.$complaint->id)

@section('content')
<div class="container mt-4 text-dark" style="background: rgba(255,255,255,0.95); color:#111; padding: 1.5rem; border-radius:8px; max-width:900px;">
    <h3 class="mb-3">Complaint #{{ $complaint->id }}</h3>

    <p><strong>From:</strong> {{ $complaint->user->name ?? '—' }} ({{ $complaint->user->email ?? '—' }})</p>
    <p><strong>Related to:</strong> {{ $complaint->target_type ?? '—' }} {{ $complaint->target_id ? "(#{$complaint->target_id})" : '' }}</p>
    <hr>
    <h5>Description</h5>
    <p style="white-space:pre-wrap;">{{ $complaint->description }}</p>

    @if($complaint->evidence)
        <hr>
        <h5>Evidence / Notes</h5>
        <p style="white-space:pre-wrap;">{{ $complaint->evidence }}</p>
    @endif

    <hr>
    <form method="POST" action="{{ route('admin.complaint.update', $complaint->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="under_review" {{ $complaint->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="dismissed" {{ $complaint->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Admin notes (optional)</label>
            <textarea name="admin_notes" class="form-control" rows="4">{{ $complaint->admin_notes }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.complaints') }}" class="btn btn-secondary">Back</a>
            <button class="btn btn-success">Update</button>
        </div>
    </form>

</div>
@endsection
