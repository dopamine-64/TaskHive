@extends('layouts.app')

@section('title', 'Invoice #' . $booking->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4" style="position: relative; overflow: hidden;">
                {{-- PAID Watermark --}}
                @if($booking->payment_status == 'paid')
                    <div style="position: absolute; top: 30%; left: 20%; font-size: 100px; color: rgba(40, 167, 69, 0.05); transform: rotate(-30deg); font-weight: 900; pointer-events: none; z-index: 1;">
                        PAID
                    </div>
                @endif

                <div class="card-body p-5" style="z-index: 2; position: relative;">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
                        <div>
                            <h2 class="fw-bold text-primary mb-0">TaskHive</h2>
                            <small class="text-muted">Official Booking Invoice</small>
                        </div>
                        <div class="text-end">
                            <h4 class="text-muted mb-0">INVOICE</h4>
                            <strong>#INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
                            <small>Date: {{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y') }}</small>
                        </div>
                    </div>

                    {{-- Billing Details --}}
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase small fw-bold">Billed To (Customer):</h6>
                            <h5 class="fw-bold text-dark mb-1">{{ $booking->customer->name ?? 'Customer' }}</h5>
                            <p class="text-muted small mb-0">{{ $booking->customer->email ?? '' }}</p>
                            <p class="text-muted small mb-0">{{ $booking->address }}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                            <h6 class="text-muted text-uppercase small fw-bold">Service Provider:</h6>
                            <h5 class="fw-bold text-dark mb-1">{{ $booking->provider->name ?? 'Provider' }}</h5>
                            <p class="text-muted small mb-0">{{ $booking->provider->email ?? '' }}</p>
                        </div>
                    </div>

                    {{-- Invoice Table --}}
                    <table class="table table-borderless mb-5">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 rounded-start">Description</th>
                                <th class="py-3 px-4 text-center">Schedule</th>
                                <th class="py-3 px-4 text-end rounded-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3 px-4">
                                    <h6 class="mb-0 fw-bold">{{ $booking->service->title ?? 'Service Booking' }}</h6>
                                </td>
                                <td class="py-3 px-4 text-center text-muted small">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }} <br>
                                    {{ date('h:i A', strtotime($booking->booking_time)) }}
                                </td>
                                <td class="py-3 px-4 text-end fw-bold">৳{{ number_format($booking->amount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="2" class="text-end py-3 px-4 fw-bold">Total Paid:</td>
                                <td class="text-end py-3 px-4 text-success fw-bold fs-5">৳{{ number_format($booking->amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Transaction Details --}}
                    @if($transaction)
                        <div class="bg-light p-3 rounded-3 small text-muted text-center">
                            <strong>Transaction ID:</strong> {{ $transaction->transaction_id }} &nbsp;|&nbsp; 
                            <strong>Payment Method:</strong> {{ $transaction->payment_method ?? 'SSLCommerz' }} &nbsp;|&nbsp; 
                            <strong>Status:</strong> <span class="text-success fw-bold">SUCCESS</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="text-center mt-4 d-print-none">
                <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 me-2 shadow-sm">
                    <i class="fas fa-print me-1"></i> Print / Save PDF
                </button>
                
                {{-- Dynamic Back Button based on User Role --}}
                @if(auth()->id() == $booking->customer_id)
                    <a href="{{ route('customer.profile') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Back to Profile</a>
                @elseif(auth()->id() == $booking->provider_id)
                    {{-- Note: if your provider dashboard is named differently, you can change 'dashboard' to 'profile.dashboard' below --}}
                    <a href="{{ route('provider.show', Auth::id()) }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Back to Dashboard</a>
                @else
                    <a href="{{ url('/') }}" class="btn btn-light rounded-pill px-4 shadow-sm border">Back to Home</a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background-color: white !important; }
        .navbar, footer, .d-print-none { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
@endsection