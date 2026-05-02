@extends('layouts.app')

@section('title', 'Reschedule Booking')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg" style="background: rgba(255,255,255,0.97); border-radius: 24px;">
                <div class="card-body p-4">
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-pill" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="text-center mb-4">
                        <i class="fas fa-calendar-alt fa-3x text-warning mb-2"></i>
                        <h3 class="fw-bold mb-0" style="color: #1a1a2e;">Reschedule</h3>
                        <p class="text-muted small">Change your booking date & time</p>
                    </div>

                    <form method="POST" action="{{ route('booking.reschedule', $booking->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold"> New Date</label>
                            <input type="date" name="booking_date" class="form-control rounded-pill" 
                                   value="{{ $booking->booking_date }}" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold"> New Time</label>
                            <select name="booking_time" class="form-select rounded-pill" required>
                                <option value="09:00" {{ $booking->booking_time == '09:00' ? 'selected' : '' }}>09:00 AM</option>
                                <option value="10:00" {{ $booking->booking_time == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                <option value="11:00" {{ $booking->booking_time == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                <option value="14:00" {{ $booking->booking_time == '14:00' ? 'selected' : '' }}>02:00 PM</option>
                                <option value="15:00" {{ $booking->booking_time == '15:00' ? 'selected' : '' }}>03:00 PM</option>
                                <option value="16:00" {{ $booking->booking_time == '16:00' ? 'selected' : '' }}>04:00 PM</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-2 rounded-pill fw-bold">
                            <i class="fas fa-save me-1"></i> Update Booking
                        </button>
                        
                        <a href="{{ route('customer.profile') }}" class="btn btn-link w-100 mt-2 text-muted text-decoration-none">
                            ← Back to My Bookings
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection