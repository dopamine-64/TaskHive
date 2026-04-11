{{-- Provider Profile Dashboard --}}
@extends('layouts.app')

@section('title', 'My Profile Dashboard')

@section('styles')
<style>
    .dashboard-container {
        padding: 2rem;
    }

    .dashboard-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(254, 184, 62, 0.1);
        border: 1px solid rgba(254, 184, 62, 0.3);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 600;
        color: #feb83e;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: white;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid rgba(254, 184, 62, 0.3);
        padding-bottom: 0.5rem;
    }

    .rating-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .reviewer-name {
        font-weight: 600;
        color: white;
    }

    .rating-stars {
        color: #feb83e;
        font-size: 1rem;
    }

    .rating-review {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .no-data {
        text-align: center;
        padding: 2rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .btn-primary {
        background: #feb83e;
        border: none;
        color: black;
        font-weight: 600;
        border-radius: 8px;
    }

    .btn-primary:hover {
        background: #ffc857;
        color: black;
    }

    .profile-incomplete {
        background: rgba(255, 193, 7, 0.2);
        border: 1px solid rgba(255, 193, 7, 0.5);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        color: #ffc857;
    }
</style>
@endsection

@section('content')
<div class="container-fluid dashboard-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 style="color: white;">Provider Dashboard</h1>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
    </div>

    @if(!$profile || !$profile->bio || !$profile->skills || count($profile->skills) == 0)
        <div class="profile-incomplete">
            <strong>⚠️ Complete Your Profile</strong>
            <p class="mb-0">Add a bio and skills to help customers find you more easily.</p>
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $profile->total_ratings ?? 0 }}</div>
            <div class="stat-label">Total Ratings</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $profile->total_ratings > 0 ? number_format($profile->average_rating, 1) : '-' }}</div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $profile->experience_years ?? 0 }}+</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ count($profile->skills ?? []) }}</div>
            <div class="stat-label">Skills</div>
        </div>
    </div>

    <!-- Profile Summary -->
    <div class="dashboard-card">
        <h2 class="section-title">Profile Information</h2>
        
        @if($profile && $profile->bio)
            <div class="mb-3">
                <strong style="color: rgba(255, 255, 255, 0.9);">Bio:</strong>
                <p style="color: rgba(255, 255, 255, 0.8);">{{ $profile->bio }}</p>
            </div>
        @endif

        @if($profile && $profile->skills && count($profile->skills) > 0)
            <div class="mb-3">
                <strong style="color: rgba(255, 255, 255, 0.9);">Skills:</strong>
                <div style="margin-top: 0.5rem;">
                    @foreach($profile->skills as $skill)
                        <span style="
                            display: inline-block;
                            background: rgba(254, 184, 62, 0.2);
                            border: 1px solid rgba(254, 184, 62, 0.5);
                            color: #feb83e;
                            padding: 0.3rem 0.8rem;
                            border-radius: 15px;
                            margin: 0.3rem;
                            font-size: 0.85rem;
                        ">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($profile && ($profile->hourly_rate || $profile->fixed_rate))
            <div class="mb-3">
                <strong style="color: rgba(255, 255, 255, 0.9);">Pricing:</strong>
                <p style="color: rgba(255, 255, 255, 0.8);">
                    @if($profile->hourly_rate)
                        Hourly: ৳{{ $profile->hourly_rate }}/hr
                    @endif
                    @if($profile->hourly_rate && $profile->fixed_rate) | @endif
                    @if($profile->fixed_rate)
                        Fixed: ৳{{ $profile->fixed_rate }}
                    @endif
                </p>
            </div>
        @endif

        @if($profile && $profile->service_area)
            <div class="mb-3">
                <strong style="color: rgba(255, 255, 255, 0.9);">Service Area:</strong>
                <p style="color: rgba(255, 255, 255, 0.8);">
                    {{ $profile->service_area }}
                    @if($profile->service_radius_km)
                        ({{ $profile->service_radius_km }} km radius)
                    @endif
                </p>
            </div>
        @endif

        @if($profile && $profile->certifications)
            <div class="mb-3">
                <strong style="color: rgba(255, 255, 255, 0.9);">Certifications:</strong>
                <p style="color: rgba(255, 255, 255, 0.8);">{{ $profile->certifications }}</p>
            </div>
        @endif
    </div>

    <!-- Recent Ratings -->
    <div class="dashboard-card">
        <h2 class="section-title">Recent Ratings</h2>

        @if($recentRatings && count($recentRatings) > 0)
            @foreach($recentRatings as $rating)
                <div class="rating-item">
                    <div class="rating-header">
                        <span class="reviewer-name">{{ $rating->reviewer->name }}</span>
                        <span class="rating-stars">
                            @for($i = 0; $i < $rating->rating; $i++)
                                ⭐
                            @endfor
                        </span>
                    </div>
                    @if($rating->review)
                        <p class="rating-review">{{ $rating->review }}</p>
                    @endif
                    <small style="color: rgba(255, 255, 255, 0.5);">{{ $rating->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        @else
            <div class="no-data">
                <p>No ratings yet. Keep providing excellent service to earn your first rating!</p>
            </div>
        @endif
    </div>

    <!-- Quick Links -->
    <div class="dashboard-card">
        <h2 class="section-title">Quick Links</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="{{ route('provider.show', auth()->id()) }}" class="btn btn-primary w-100">View My Profile</a>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">Edit Profile</a>
            <a href="{{ route('services.index') }}" class="btn btn-primary w-100">View Services</a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">Main Dashboard</a>
        </div>
    </div>
</div>
@endsection
