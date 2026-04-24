@extends('layouts.app')

@section('title', 'Submit Complaint')

@section('content')
<div class="container mt-5 text-dark" style="background: rgba(255,255,255,0.95); color: #111; padding: 2rem; border-radius: 8px; max-width: 800px; margin: 3rem auto;">
    <h3 class="mb-3">Submit a Complaint / Dispute</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('complaints.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Related to (optional)</label>
            <input name="target_type" class="form-control" value="{{ old('target_type', request('target_type')) }}" placeholder="service, booking, provider, user">
        </div>

        <div class="mb-3">
            <label class="form-label">Related item ID (optional)</label>
            <input name="target_id" type="number" class="form-control" value="{{ old('target_id', request('target_id')) }}" placeholder="e.g. booking id or service id">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="6" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Evidence / Notes (optional)</label>
            <textarea name="evidence" rows="3" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
            <button class="btn btn-primary">Submit Complaint</button>
        </div>
    </form>
</div>
@endsection
