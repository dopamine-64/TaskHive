{{-- Show Provider Profile --}}
@extends('layouts.app')

@section('title', $user->name . ' - Provider Profile')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
    }

    .profile-header {
        background-color: #f0f4f3 !important;
        border: 1px solid #e1e8e5;
        border-radius: 16px !important;
        padding: 2rem;
        margin: 2rem 0;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
    }

    .profile-name {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #005c4b;
    }

    .rating-stars {
        font-size: 1.2rem;
        color: #005c4b;
        margin: 0.5rem 0;
    }

    .rating-stars span {
        font-weight: 600;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin: 2rem 0;
    }

    .stat {
        text-align: center;
        background-color: rgba(0, 92, 75, 0.05);
        border: 1px solid rgba(0, 92, 75, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #005c4b;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #3b5249;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .profile-section {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #e1e8e5;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #005c4b;
        font-family: 'Poppins', sans-serif;
    }

    .bio-text {
        line-height: 1.6;
        color: #2c3e38;
    }

    .skills-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .skill-badge {
        background: rgba(0, 92, 75, 0.15);
        border: 1px solid rgba(0, 92, 75, 0.3);
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        color: #005c4b;
        font-weight: 500;
    }

    .rating-card {
        background: rgba(255, 255, 255, 0.5);
        border: 1px solid rgba(0, 92, 75, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        margin: 1rem 0;
    }

    .reviewer-name {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #005c4b;
    }

    .review-text {
        color: #2c3e38;
        line-height: 1.5;
    }

    .review-text small {
        color: #3b5249;
    }

    .rating-form {
        background: rgba(0, 92, 75, 0.05);
        border: 1px solid rgba(0, 92, 75, 0.2);
        border-radius: 12px;
        padding: 2rem;
        margin: 2rem 0;
    }

    .form-group label {
        color: #3b5249;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        font-family: 'Poppins', sans-serif;
    }

    .form-control, .form-select {
        background-color: #ffffff !important;
        border: 1px solid #cdd6d2 !important;
        color: #2c3e38 !important;
        border-radius: 10px !important;
        padding: 0.75rem 1rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #005c4b !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 92, 75, 0.15) !important;
        outline: none;
        background-color: #ffffff !important;
        color: #2c3e38 !important;
    }

    .form-control::placeholder {
        color: #999;
    }

    .btn-primary {
        background: #005c4b !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        border-radius: 20px !important;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #004538 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 92, 75, 0.3) !important;
        color: white !important;
    }

    .btn-secondary {
        background: #f0f4f3 !important;
        border: 1px solid #cdd6d2 !important;
        color: #005c4b !important;
        border-radius: 20px !important;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #e8eceb !important;
        border-color: #005c4b !important;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: #dc3545 !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        border-radius: 8px !important;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: #c82333 !important;
        color: white !important;
    }

    .verified-badge {
        background: rgba(76, 175, 80, 0.15);
        border: 1px solid rgba(76, 175, 80, 0.4);
        color: #2e7d32;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-block;
        font-weight: 600;
    }

    .alert-success {
        background-color: rgba(76, 175, 80, 0.15);
        border: 1px solid rgba(76, 175, 80, 0.3);
        color: #2e7d32;
    }

    .btn-close {
        background-color: rgba(76, 175, 80, 0.3);
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container py-5" style="z-index: 10;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="profile-header" style="max-width: 900px; margin-left: auto; margin-right: auto;">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="profile-name">{{ $user->name }}</h1>
                @if($profile->is_verified)
                    <span class="verified-badge">✓ Verified Provider</span>
                @endif
            </div>
            @if(auth()->check() && auth()->id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
            @endif
        </div>

        <div class="rating-stars">
            @if($profile->total_ratings > 0)
                <span>⭐ <span>{{ number_format($profile->average_rating, 1) }}/5.0</span></span>
                <span style="color: #3b5249; font-size: 0.9rem;">
                    ({{ $profile->total_ratings }} {{ Str::plural('review', $profile->total_ratings) }})
                </span>
            @else
                <span style="color: #3b5249;">No ratings yet</span>
            @endif
        </div>
    </div>

    <div class="profile-stats" style="max-width: 900px; margin-left: auto; margin-right: auto;">
        <div class="stat">
            <div class="stat-value">{{ $profile->experience_years ?? 0 }}+</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat">
            <div class="stat-value">{{ $profile->total_ratings }}</div>
            <div class="stat-label">Ratings</div>
        </div>
        <div class="stat">
            <div class="stat-value">{{ count($profile->skills ?? []) }}</div>
            <div class="stat-label">Skills</div>
        </div>
        <div class="stat">
            <div class="stat-value">{{ $user->services()->count() ?? 0 }}</div>
            <div class="stat-label">Services</div>
        </div>
    </div>

    <div style="max-width: 900px; margin-left: auto; margin-right: auto;">
        @if($profile->bio)
            <div class="profile-section">
                <h3 class="section-title">About</h3>
                <p class="bio-text">{{ $profile->bio }}</p>
            </div>
        @endif

        @if($profile->skills && count($profile->skills) > 0)
            <div class="profile-section">
                <h3 class="section-title">Skills</h3>
                <div class="skills-list">
                    @foreach($profile->skills as $skill)
                        <span class="skill-badge">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($profile->hourly_rate || $profile->fixed_rate)
            <div class="profile-section">
                <h3 class="section-title">Pricing</h3>
                @if($profile->hourly_rate)
                    <p style="color: #2c3e38;"><strong>Hourly Rate:</strong> ${{ $profile->hourly_rate }}/hr</p>
                @endif
                @if($profile->fixed_rate)
                    <p style="color: #2c3e38;"><strong>Fixed Rate:</strong> ${{ $profile->fixed_rate }}</p>
                @endif
            </div>
        @endif

        @if($profile->service_area)
            <div class="profile-section">
                <h3 class="section-title">Service Area</h3>
                <p style="color: #2c3e38;">{{ $profile->service_area }}
                    @if($profile->service_radius_km)
                        ({{ $profile->service_radius_km }} km radius)
                    @endif
                </p>
            </div>
        @endif

        @if($profile->certifications)
            <div class="profile-section">
                <h3 class="section-title">Certifications</h3>
                <p style="color: #2c3e38;">{{ $profile->certifications }}</p>
            </div>
        @endif

        @if(auth()->check() && auth()->id() !== $user->id)
            <div class="rating-form">
                <h3 class="section-title">Leave a Rating</h3>
                <form action="{{ route('rating.store', $user->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select name="rating" id="rating" class="form-control" required>
                            <option value="">Select a rating</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5 Stars - Excellent</option>
                            <option value="4">⭐⭐⭐⭐ 4 Stars - Good</option>
                            <option value="3">⭐⭐⭐ 3 Stars - Average</option>
                            <option value="2">⭐⭐ 2 Stars - Poor</option>
                            <option value="1">⭐ 1 Star - Very Poor</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="review" class="form-label">Review (Optional)</label>
                        <textarea name="review" id="review" class="form-control" rows="4" 
                            placeholder="Share your experience..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                </form>
            </div>
        @endif

        @if($ratings->count() > 0)
            <div class="profile-section">
                <h3 class="section-title">Reviews ({{ $profile->total_ratings }})</h3>
                
                @foreach($ratings as $rating)
                    <div class="rating-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="reviewer-name">{{ $rating->reviewer->name }}</p>
                                <p style="color: #005c4b; margin-bottom: 0;">
                                    @for($i = 0; $i < $rating->rating; $i++)
                                        ⭐
                                    @endfor
                                </p>
                            </div>
                            @if(auth()->check() && (auth()->id() === $rating->reviewer_id || auth()->id() === $user->id))
                                <form action="{{ route('rating.destroy', $rating) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this rating?');">Delete</button>
                                </form>
                            @endif
                        </div>
                        @if($rating->review)
                            <p class="review-text">{{ $rating->review }}</p>
                        @endif
                        <small style="color: #3b5249;">{{ $rating->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach

                {{ $ratings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
