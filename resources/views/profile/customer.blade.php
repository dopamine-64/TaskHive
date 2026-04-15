@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="fw-bold mb-4">My Bookings & Activity</h2>

            @if($activeBooking)
                {{-- Active Booking: Only shows if provider accepted --}}
                <div class="card p-4 mb-4" style="background: rgba(40, 167, 69, 0.1); border: 1px solid #28a745; border-radius: 20px; backdrop-filter: blur(15px);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-success fw-bold m-0">Active Booking: {{ $activeBooking->provider_name }}</h4>
                            <p class="m-0 text-white-50">Current Status: Accepted</p>
                        </div>
                        <a href="{{ route('tracking.live', $activeBooking->id) }}" class="btn btn-success rounded-pill px-4">Go to Live Map</a>
                    </div>
                </div>
            @endif

            <h3 class="mt-5 mb-3 text-warning">Service History</h3>
            @forelse($completedBookings as $booking)
                <div class="card p-3 mb-2" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; backdrop-filter: blur(15px);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0 text-white">{{ $booking->provider_name }}</h5>
                            <small class="text-white-50">Completed</small>
                        </div>
                        <button class="btn btn-outline-warning btn-sm rounded-pill px-3">Leave Review</button>
                    </div>
                </div>
            @empty
                <p class="text-white-50">No completed services found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection