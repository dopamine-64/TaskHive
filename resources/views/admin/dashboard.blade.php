@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold mb-0">Control Panel</h2>
        <p class="text-white-50">Welcome back, System Admin.</p>
    </div>
    <div>
        <a href="{{ route('admin.reports') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
            View Reports & Analytics
        </a>
    </div>
</div>

    {{-- Statistical Cards --}}
    <div class="row g-4 mb-5">
        {{-- Revenue Card --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-white-50 text-uppercase fw-bold small mb-1">Total Revenue</p>
                        <h3 class="fw-bold mb-0">৳{{ number_format($revenue, 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bookings Card --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase fw-bold small mb-1">Total Bookings</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalBookings }}</h3>
                        <small class="text-warning fw-bold"><i class="fas fa-clock"></i> {{ $pendingRequests }} Pending</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users & Providers Card --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users text-info me-2"></i>
                            <span class="text-muted fw-bold text-uppercase small">Network Size</span>
                        </div>
                    </div>
                    <div class="row text-center border-top pt-3 mt-1">
                        <div class="col-6 border-end">
                            <h4 class="fw-bold text-dark mb-0">{{ $totalUsers }}</h4>
                            <small class="text-muted">Customers</small>
                        </div>
                        <div class="col-6">
                            <h4 class="fw-bold text-dark mb-0">{{ $totalProviders }}</h4>
                            <small class="text-muted">Providers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Services Card --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-concierge-bell fa-2x text-success"></i>
                    </div>
                    <div>
                        <p class="text-muted text-uppercase fw-bold small mb-1">Total Services</p>
                        <h3 class="fw-bold text-dark mb-0">{{ $totalServices }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Complaints Summary --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase fw-bold small mb-1">Complaints</p>
                            <h3 class="fw-bold text-dark mb-0">{{ $complaintsCount ?? 0 }}</h3>
                            <small class="text-muted">Recent complaints submitted by customers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <h5 class="mb-3">Recent Complaints</h5>
                    @if(isset($recentComplaints) && $recentComplaints->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentComplaints as $rc)
                                @php
                                    $providerName = '—';
                                    if ($rc->target_type === 'provider') {
                                        $providerName = \App\Models\User::find($rc->target_id)?->name ?? '—';
                                    } elseif ($rc->target_type === 'booking') {
                                        $booking = \App\Models\Tracking::with('provider')->find($rc->target_id);
                                        $providerName = $booking?->provider?->name ?? '—';
                                    }
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">{{ $providerName }}</div>
                                        <div class="text-muted small">From: {{ $rc->user->name ?? '—' }} — {{ ucfirst($rc->status) }}</div>
                                        <div class="text-muted small">{{ Str::limit($rc->description, 80) }}</div>
                                    </div>
                                    <div class="text-end">
                                        <a href="{{ route('admin.complaint.show', $rc->id) }}" class="btn btn-sm btn-primary">View</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No recent complaints.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection