@extends('layouts.dashboard')

@section('title', 'Manage Services')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-white fw-bold mb-0"><i class="fas fa-concierge-bell me-2"></i> Manage Services</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light rounded-pill px-4">Back to Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Provider</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr>
                            <td>{{ $service->id }}</td>
                            <td>{{ $service->title }}</td>
                            <td>{{ $service->provider->name ?? 'N/A' }}</td>
                            <td>{{ $service->category }}</td>
                            <td>৳{{ number_format($service->price, 2) }}</td>
                            <td>{{ $service->location }}</td>
                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.service.edit', $service->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <a href="{{ route('admin.service.toggle', $service->id) }}" class="btn btn-sm btn-warning rounded-pill px-3">
                                    <i class="fas fa-power-off me-1"></i> {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                </a>
                                <form action="{{ route('admin.service.delete', $service->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="return confirm('Delete this service?')">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5">No services found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
</div>
@endsection