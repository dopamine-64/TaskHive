@extends('layouts.dashboard')

@section('title', 'Manage Users')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-white fw-bold mb-0"><i class="fas fa-users me-2"></i> Manage Users</h2>
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
                            <th class="px-4 py-3">Joined Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3 fw-bold">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                @if($user->is_banned)
                                    <span class="badge bg-danger">Banned</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.user.ban', $user->id) }}" class="btn btn-sm {{ $user->is_banned ? 'btn-success' : 'btn-warning' }} rounded-pill px-3">
                                    <i class="fas {{ $user->is_banned ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                    {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                </a>
                                <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $users->links() }}
    </div>
</div>
@endsection