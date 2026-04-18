@extends('layouts.app')

@section('title', 'Book Service')

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
                    
                    <h3 class="text-center mb-4 fw-bold" style="color: #1a1a2e;">📅 Book Service</h3>
                    
                    <div class="mb-4 pb-2 border-bottom">
                        <h5 class="fw-bold mb-1">{{ $service->title }}</h5>
                        <p class="text-muted small mb-2">{{ $service->description }}</p>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">💰 {{ number_format($service->price) }} Tk</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">📅 Date</label>
                            <input type="date" name="booking_date" class="form-control rounded-pill" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">⏰ Time</label>
                            <select name="booking_time" class="form-select rounded-pill" required>
                                <option value="">Select time</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">📍 Address</label>
                            <textarea name="address" class="form-control rounded-3" rows="2" 
                                      placeholder="Full address where service is needed" required></textarea>
                        </div>

                        <button type="submit" class="btn w-100 py-2 rounded-pill fw-bold" 
                                style="background: linear-gradient(135deg, #1a1a2e, #16213e); color: white;">
                            ✅ Confirm Booking
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('customer.profile') }}" class="text-muted text-decoration-none small">
                            ← Back to My Bookingsdas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection