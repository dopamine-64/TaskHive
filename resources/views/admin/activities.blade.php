@extends('layouts.dashboard')

@section('title', 'System Activities')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-white fw-bold mb-0"><i class="fas fa-history me-2"></i> System Activities</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light rounded-pill px-4">Back to Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Time</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Entity</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td class="px-4 py-3">{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3"><span class="badge bg-secondary">{{ $activity->action }}</span></td>
                            <td class="px-4 py-3">{{ $activity->entity_type }} #{{ $activity->entity_id }}</td>
                            <td class="px-4 py-3">{{ $activity->description }}</td>
                            <td class="px-4 py-3">{{ $activity->admin->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5">No activities recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-end">
        {{ $activities->links() }}
    </div>
</div>
@endsection