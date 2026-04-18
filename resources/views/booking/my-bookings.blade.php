@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white m-0"><i class="fas fa-calendar-alt me-2"></i>My Bookings</h2>
        <a href="{{ url('/services') }}" class="btn btn-warning rounded-pill px-4">
            <i class="fas fa-search me-1"></i> Browse Services
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-pill" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $activeBookings = $bookings->whereIn('status', ['requested', 'accepted']);
        $pastBookings = $bookings->whereIn('status', ['completed', 'cancelled', 'declined']);
    @endphp

    {{-- Active Bookings Section --}}
    @if($activeBookings->count() > 0)
        <h4 class="text-white mt-4 mb-3"><i class="fas fa-clock me-2"></i>Active Bookings</h4>
        <div class="row">
            @foreach($activeBookings as $booking)
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0" style="background: rgba(255,255,255,0.96); border-radius: 20px; border-left: 4px solid #ffd700; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title text-dark mb-0">
                                <i class="fas fa-tools text-warning me-1"></i> 
                                {{ $booking->service->title ?? 'Service #'.$booking->service_id }}
                            </h5>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="fas fa-hourglass-half me-1"></i> {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        
                        <div class="small text-muted mb-2">{{ $booking->service->category ?? '' }}</div>
                        
                        <hr class="my-3">
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <small class="text-muted d-block"><i class="fas fa-calendar-alt"></i></small>
                                    <small class="fw-bold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-2 text-center">
                                    <small class="text-muted d-block"><i class="fas fa-clock"></i></small>
                                    <small class="fw-bold">{{ date('h:i A', strtotime($booking->booking_time)) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-2 bg-light rounded-3">
                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> Address</small>
                            <p class="mb-0 small fw-semibold">{{ $booking->address }}</p>
                        </div>
                        
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-hourglass-half me-1"></i> {{ $booking->duration ?? 60 }} min</small>
                        </div>
                        
                        @if($booking->status == 'requested')
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('booking.reschedule.form', $booking->id) }}" class="btn btn-warning btn-sm rounded-pill px-4 flex-fill" style="background: #ffc107; border: none; color: #000; font-weight: 500;">
                                <i class="fas fa-calendar-alt me-1"></i> Reschedule
                            </a>
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 w-100" style="background: #dc3545; border: none; font-weight: 500;" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                    <i class="fas fa-trash-alt me-1"></i> Cancel
                                </button>
                            </form>
                        </div>
                        @elseif($booking->status == 'accepted')
                        <div class="alert alert-success mt-3 mb-0 py-2 small rounded-pill text-center">
                            <i class="fas fa-check-circle me-1"></i> ✅ Provider has accepted your booking!
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Past Bookings Section --}}
    @if($pastBookings->count() > 0)
        <h4 class="text-white mt-4 mb-3"><i class="fas fa-history me-2"></i>Past Bookings</h4>
        <div class="row">
            @foreach($pastBookings as $booking)
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0" style="background: rgba(255,255,255,0.92); border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $booking->service->title ?? 'Service #'.$booking->service_id }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }} at {{ date('h:i A', strtotime($booking->booking_time)) }}</small>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-3">
                                @if($booking->status == 'cancelled') Cancelled
                                @elseif($booking->status == 'declined') Declined
                                @else Completed
                                @endif
                            </span>
                        </div>
                        @if($booking->status == 'cancelled')
                        <div class="text-muted small mt-1">You cancelled this booking</div>
                        @elseif($booking->status == 'declined')
                        <div class="text-muted small mt-1">Provider declined this booking</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    @if($bookings->count() == 0)
        <div class="text-center py-5" style="background: rgba(255,255,255,0.08); border-radius: 30px;">
            <i class="fas fa-calendar-times fa-4x mb-3 text-warning"></i>
            <h3 class="text-white">No Bookings Yet</h3>
            <p class="text-white-50 mb-4">You haven't made any bookings. Browse services to get started!</p>
            <a href="{{ url('/services') }}" class="btn btn-warning rounded-pill px-5 py-2">Browse Services</a>
        </div>
    @endif
</div>

<style>
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12) !important;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .btn-warning {
        background: #ffc107;
        border: none;
        color: #000;
        font-weight: 500;
    }
    .btn-warning:hover {
        background: #e0a800;
        transform: scale(1.02);
    }
    .btn-danger {
        background: #dc3545;
        border: none;
        font-weight: 500;
    }
    .btn-danger:hover {
        background: #c82333;
        transform: scale(1.02);
    }
</style>
@endsection