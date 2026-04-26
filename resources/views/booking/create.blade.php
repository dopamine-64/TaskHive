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
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ number_format($service->price) }} Tk</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="booking_date" class="form-control rounded-pill" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"> Time</label>
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
                            <label class="form-label fw-semibold"> Address</label>
                            <textarea name="address" class="form-control rounded-3" rows="2" 
                                      placeholder="Full address where service is needed" required></textarea>
                        </div>

                        {{-- REWARD POINTS SECTION --}}
                        @auth
                            @php($points = auth()->user()->reward_points ?? 0)
                            @if($points >= 100)
                                <div class="mb-4 p-3 bg-light rounded-4 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="fw-semibold text-dark">
                                            <i class="fas fa-star text-warning me-1"></i> Reward Points
                                        </label>
                                        <span class="badge bg-primary rounded-pill">{{ number_format($points) }} points available</span>
                                    </div>
                                    <div class="input-group">
                                        <input type="number" name="redeem_points" id="redeem_points" class="form-control rounded-start-pill" 
                                               value="0" min="0" max="{{ $points }}" step="100" placeholder="Points to redeem">
                                        <span class="input-group-text bg-white rounded-end-pill">points</span>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            10 points = 1 Tk discount. You will save 
                                            <span id="discount_amount" class="fw-bold text-success">0</span> Tk.
                                        </small>
                                    </div>
                                    <div class="mt-1 text-end">
                                        <span class="badge bg-light text-dark">Final price: <span id="final_price">{{ number_format($service->price) }}</span> Tk</span>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-secondary small rounded-pill mb-4">
                                    Earn reward points on completed bookings. You need at least 100 points to get a discount. 
                                    <a href="{{ route('customer.profile') }}" class="alert-link">View your points</a>
                                </div>
                            @endif
                        @endauth

                        <button type="submit" class="btn w-100 py-2 rounded-pill fw-bold" 
                                style="background: linear-gradient(135deg, #1a1a2e, #16213e); color: white;">
                            Confirm Booking
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('customer.profile') }}" class="text-muted text-decoration-none small">
                            ← Back to My Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pointsInput = document.getElementById('redeem_points');
        if (!pointsInput) return;

        const basePrice = {{ $service->price }};
        const discountSpan = document.getElementById('discount_amount');
        const finalPriceSpan = document.getElementById('final_price');

        function updateDiscount() {
            let points = parseInt(pointsInput.value) || 0;
            let discount = Math.floor(points / 10);
            if (discount > basePrice) discount = basePrice;
            let final = basePrice - discount;
            discountSpan.textContent = discount;
            finalPriceSpan.textContent = final.toFixed(0);
        }

        pointsInput.addEventListener('input', updateDiscount);
        updateDiscount(); // initial
    });
</script>
@endsection