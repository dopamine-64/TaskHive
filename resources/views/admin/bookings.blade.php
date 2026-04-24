@extends('layouts.dashboard')

@section('title', 'Manage Bookings')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-white fw-bold mb-0"><i class="fas fa-calendar-alt me-2"></i> Manage Bookings</h2>
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
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Provider</th>
                            <th class="px-4 py-3">Date / Time</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3">{{ $booking->id }}</td>
                            <td class="px-4 py-3">{{ $booking->customer->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $booking->provider->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}<br>
                                <small class="text-muted">{{ date('h:i A', strtotime($booking->booking_time)) }}</small>
                            </td>
                            <td class="px-4 py-3">৳{{ number_format($booking->amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm w-auto" style="min-width: 120px;">
                                        <option value="requested" @if($booking->status == 'requested') selected @endif>Requested</option>
                                        <option value="accepted" @if($booking->status == 'accepted') selected @endif>Accepted</option>
                                        <option value="in_progress" @if($booking->status == 'in_progress') selected @endif>In Progress</option>
                                        <option value="completed" @if($booking->status == 'completed') selected @endif>Completed</option>
                                        <option value="cancelled" @if($booking->status == 'cancelled') selected @endif>Cancelled</option>
                                        <option value="declined" @if($booking->status == 'declined') selected @endif>Declined</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                @if($booking->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <form action="{{ route('admin.booking.delete', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Delete this booking record?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $bookings->links() }}
    </div>
</div>
@endsection