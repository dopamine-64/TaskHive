@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-5">
    <style>
        .rating-stars {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 6px;
        }
        .rating-stars input { display: none; }
        .rating-stars label {
            cursor: pointer;
            font-size: 1.8rem;
            color: #d1d5db;
            transition: color .15s ease;
            margin: 0;
        }
        .rating-stars label:hover,
        .rating-stars label:hover ~ label,
        .rating-stars input:checked ~ label {
            color: #f59e0b;
        }
    </style>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold mb-0">My Bookings</h2>
        <a href="{{ url('/services') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="fas fa-plus me-1"></i> New Booking
        </a>
    </div>


    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert border-0 shadow-sm d-flex align-items-center mb-4" 
             style="background: #f0fff4; color: #276749; border-radius: 16px;">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert border-0 shadow-sm d-flex align-items-center mb-4" 
             style="background: #fff5f5; color: #c53030; border-radius: 16px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @php
        $activeBookings = $bookings->whereIn('status', ['requested', 'accepted', 'in_progress']);
        $pastBookings = $bookings->whereIn('status', ['completed', 'cancelled', 'declined']);
    @endphp

    {{-- 📋 ACTIVE BOOKINGS --}}
    @if($activeBookings->count() > 0)
        <h5 class="text-white-50 text-uppercase small fw-bold mb-3" style="letter-spacing: 1px;">Active Requests</h5>
        <div class="row">
            @foreach($activeBookings as $booking)
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="background: rgba(255,255,255,0.98); border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $booking->service_title ?? 'Service #'.$booking->service_id }}</h5>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-user-tie me-1"></i> Provider: {{ $booking->provider_name ?? 'Provider #'.$booking->provider_id }}
                                    {{-- ✅ Verified Provider Badge --}}
                                    @php
                                        $providerRecord = \App\Models\User::with('providerProfile')->find($booking->provider_id);
                                        $isVerified = $providerRecord && $providerRecord->providerProfile && $providerRecord->providerProfile->is_verified;
                                    @endphp
                                    @if($isVerified)
                                        <i class="fas fa-check-circle text-primary ms-1" data-bs-toggle="tooltip" title="Verified Provider"></i>
                                    @endif
                                </p>
                            </div>
                            
                            {{-- Colored Status Badge --}}
                            <span class="badge rounded-pill px-3 py-1" 
                                  style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;
                                         @if($booking->status == 'accepted')
                                            background-color: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4;
                                         @else
                                            background-color: #feebc8; color: #c05621; border: 1px solid #fbd38d;
                                         @endif">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </div>

                        {{-- Date, Time, AND Price --}}
                        <div class="row g-2 mb-3 bg-light rounded-3 p-3 text-center">
                            <div class="col-4 border-end">
                                <small class="text-muted d-block small text-uppercase fw-bold" style="font-size: 10px;">Date</small>
                                <span class="fw-semibold text-dark" style="font-size: 13px;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="col-4 border-end">
                                <small class="text-muted d-block small text-uppercase fw-bold" style="font-size: 10px;">Time</small>
                                <span class="fw-semibold text-dark" style="font-size: 13px;">{{ date('h:i A', strtotime($booking->booking_time)) }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block small text-uppercase fw-bold" style="font-size: 10px;">Price</small>
                                <span class="fw-bold text-success" style="font-size: 14px;">৳{{ number_format($booking->amount ?? 0, 0) }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <small class="text-muted d-block small text-uppercase fw-bold mb-1" style="font-size: 10px;">Location</small>
                            <p class="text-dark small mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $booking->address }}</p>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        @if($booking->status == 'requested')
                            <div class="d-flex gap-2">
                                <a href="{{ route('booking.reschedule.form', $booking->id) }}" class="btn btn-warning flex-fill rounded-pill fw-bold small py-2">Reschedule</a>
                                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="flex-fill">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold small py-2" onclick="return confirm('Cancel this booking?')">Cancel</button>
                                </form>
                                <a href="{{ route('tracking.live', $booking->id) }}" class="btn btn-info flex-fill rounded-pill fw-bold small py-2 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-map-marker-alt me-1"></i> Live Map
                                </a>
                            </div>
                            
                        @elseif($booking->status == 'accepted')
                            {{-- Payment Logic with TWO OPTIONS --}}
                            @if($booking->payment_status == 'paid')
                                <div class="alert alert-success mt-2 py-3 text-center rounded-4 mb-0 border-0 shadow-sm">
                                    <h6 class="mb-2 fw-bold text-success"><i class="fas fa-check-circle me-1"></i> Payment Completed!</h6>
                                    <a href="{{ route('invoice.show', $booking->id) }}" class="btn btn-sm btn-success rounded-pill px-4 fw-bold">View Invoice</a>
                                    <a href="{{ route('complaints.create', ['target_type' => 'booking', 'target_id' => $booking->id]) }}" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold ms-2">Complain</a>
                                    <a href="{{ route('tracking.live', $booking->id) }}" class="btn btn-sm btn-info rounded-pill px-4 fw-bold ms-2">
                                        <i class="fas fa-map-marker-alt me-1"></i> Live Map
                                    </a>
                                </div>
                            @else
                                <div class="text-success small text-center fw-bold mb-2">
                                    Provider accepted! Choose payment method:
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('payment.initiate', $booking->id) }}" class="btn btn-primary flex-fill rounded-pill fw-bold small py-2" style="font-size: 13px;">
                                        <i class="fas fa-credit-card me-1"></i> Pay Online
                                    </a>
                                    <form action="{{ route('wallet.pay', $booking->id) }}" method="POST" class="flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold small py-2" style="font-size: 13px;" 
                                            onclick="return confirm('Pay ৳{{ number_format($booking->amount ?? 0, 0) }} from your wallet?')">
                                            <i class="fas fa-wallet me-1"></i> Pay with Wallet
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- 📜 PAST BOOKINGS --}}
    @if($pastBookings->count() > 0)
        <h5 class="text-white-50 text-uppercase small fw-bold mt-4 mb-3" style="letter-spacing: 1px;">History</h5>
        <div class="row">
            @foreach($pastBookings as $booking)
            <div class="col-md-4 mb-3">
                <div class="card border-0" style="background: rgba(255,255,255,0.9); border-radius: 15px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark mb-0">{{ $booking->service_title ?? 'Service #'.$booking->service_id }}</h6>
                                <small class="text-muted">
                                    {{ $booking->provider_name ?? 'Provider #'.$booking->provider_id }}
                                    @php
                                        $providerRecord = \App\Models\User::with('providerProfile')->find($booking->provider_id);
                                        $isVerified = $providerRecord && $providerRecord->providerProfile && $providerRecord->providerProfile->is_verified;
                                    @endphp
                                    @if($isVerified)
                                        <i class="fas fa-check-circle text-primary ms-1" data-bs-toggle="tooltip" title="Verified Provider"></i>
                                    @endif
                                    •
                                    {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : 'Not set' }}
                                    •
                                    {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') : 'Not set' }}
                                </small>
                                {{-- Show points earned if completed and points_earned > 0 --}}
                                @if($booking->status == 'completed' && ($booking->points_earned ?? 0) > 0)
                                    <br><small class="text-warning"><i class="fas fa-star me-1"></i> +{{ $booking->points_earned }} points earned</small>
                                @endif
                            </div>
                            <span class="badge 
                                @if($booking->status == 'completed') bg-success
                                @elseif($booking->status == 'cancelled') bg-secondary
                                @elseif($booking->status == 'declined') bg-danger
                                @endif rounded-pill px-2 py-1" style="font-size: 10px;">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </div>
                        @if($booking->status === 'completed' && !in_array((int) $booking->id, $ratedTrackingIds ?? [], true))
                            <div class="mt-3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 open-rate-review-modal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rateReviewModal"
                                    data-action="{{ route('rating.store', ['providerId' => $booking->provider_id]) }}"
                                    data-tracking-id="{{ $booking->id }}"
                                    data-provider="{{ $booking->provider_name ?? 'Provider #'.$booking->provider_id }}"
                                    data-service="{{ $booking->service_title ?? 'Service #'.$booking->service_id }}"
                                >
                                    <i class="fas fa-star me-1"></i> Rate &amp; Review
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    @if($bookings->count() == 0)
        <div class="text-center py-5" style="background: rgba(255,255,255,0.05); border-radius: 40px; border: 2px dashed rgba(255,255,255,0.1);">
            <i class="fas fa-calendar-day fa-3x mb-3 text-white-50"></i>
            <h4 class="text-white">No bookings found</h4>
            <p class="text-white-50">You haven't booked any services yet.</p>
            <a href="{{ url('/services') }}" class="btn btn-warning rounded-pill px-5 mt-2 fw-bold">Find a Service</a>
        </div>
    @endif
</div>

<div class="modal fade" id="rateReviewModal" tabindex="-1" aria-labelledby="rateReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="rateReviewModalLabel">Rate &amp; Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rateReviewForm" method="POST" action="">
                @csrf
                <input type="hidden" name="tracking_id" id="rate-tracking-id" value="">
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="rateReviewContext"></p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Star Rating</label>
                        <div class="rating-stars">
                            <input type="radio" id="rate-star-5" name="rating" value="5" required><label for="rate-star-5">★</label>
                            <input type="radio" id="rate-star-4" name="rating" value="4"><label for="rate-star-4">★</label>
                            <input type="radio" id="rate-star-3" name="rating" value="3"><label for="rate-star-3">★</label>
                            <input type="radio" id="rate-star-2" name="rating" value="2"><label for="rate-star-2">★</label>
                            <input type="radio" id="rate-star-1" name="rating" value="1"><label for="rate-star-1">★</label>
                        </div>
                        @error('rating')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="rate-review-text" class="form-label fw-semibold">Review</label>
                        <textarea id="rate-review-text" name="review" rows="4" class="form-control" placeholder="Share your experience...">{{ old('review') }}</textarea>
                        @error('review')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('rateReviewForm');
        const context = document.getElementById('rateReviewContext');
        const trackingInput = document.getElementById('rate-tracking-id');
        const triggerButtons = document.querySelectorAll('.open-rate-review-modal');
        const starInputs = form ? form.querySelectorAll('input[name="rating"]') : [];

        triggerButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!form || !context) return;

                form.action = this.dataset.action || '';
                if (trackingInput) {
                    trackingInput.value = this.dataset.trackingId || '';
                }
                context.textContent = `Review for ${this.dataset.provider || 'Provider'} - ${this.dataset.service || 'Service'}`;

                starInputs.forEach(function (input) {
                    input.checked = false;
                });
            });
        });
    });
</script>

<script>
    // Initialize Bootstrap tooltips for verified badges
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endsection