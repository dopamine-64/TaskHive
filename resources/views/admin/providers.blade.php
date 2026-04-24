@extends('layouts.dashboard')

@section('title', 'Manage Providers')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-white fw-bold mb-0"><i class="fas fa-user-tie me-2"></i> Manage Providers</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Hourly Rate</th>
                            <th class="px-4 py-3">Verified</th>
                            <th class="px-4 py-3">Joined</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $provider)
                        <tr>
                            <td class="px-4 py-3 fw-bold">{{ $provider->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $provider->email }}</td>
                            <td class="px-4 py-3">
                                @if($provider->providerProfile && $provider->providerProfile->hourly_rate)
                                    ৳{{ number_format($provider->providerProfile->hourly_rate, 2) }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($provider->providerProfile && $provider->providerProfile->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-secondary">Not Verified</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $provider->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.provider.verify', $provider->id) }}" class="btn btn-sm btn-info rounded-pill px-3">
                                    <i class="fas fa-check-circle me-1"></i> Toggle Verify
                                </a>
                                <form action="{{ route('admin.provider.delete', $provider->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="return confirm('Delete this provider? All their services and data will be removed.')">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">No providers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $providers->links() }}
    </div>
</div>
@endsection