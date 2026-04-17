@extends('layouts.app')
@section('title', 'TaskHive | Match Results')

@section('styles')
<style>
    .hero-section { text-align: center; padding: 45px 0 30px; color: white; }
    .hero-title { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    .service-card {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
        border-radius: 18px; backdrop-filter: blur(10px); color: white; padding: 20px; height: 100%;
    }
    .score-chip {
        background: #28a745; color: #fff; font-weight: 700; border-radius: 50px; padding: 4px 12px; font-size: 12px;
    }
    .meta { color: rgba(255,255,255,0.85); font-size: 13px; }
    .explain { font-size: 12px; color: rgba(255,255,255,0.75); border-top: 1px dashed rgba(255,255,255,0.2); margin-top: 10px; padding-top: 10px; }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Provider Match Results</h1>
        <p style="opacity: 0.9;">Top matches ranked by weighted smart scoring</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        @forelse(($matches ?? []) as $provider)
            <div class="col-md-4">
                <div class="service-card shadow">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0">{{ $provider['provider_name'] ?? 'Provider' }}</h5>
                        <span class="score-chip">{{ number_format($provider['total_score'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="meta">Distance: {{ number_format($provider['distance_km'] ?? 0, 2) }} km</div>
                    <div class="meta">Rating: {{ number_format($provider['average_rating'] ?? 0, 2) }} / 5</div>
                    <div class="meta">Price: ৳{{ number_format($provider['effective_rate'] ?? 0, 0) }}</div>
                    <div class="explain">
                        Location {{ number_format($provider['location_score'] ?? 0, 1) }} |
                        Rating {{ number_format($provider['rating_score'] ?? 0, 1) }} |
                        Price {{ number_format($provider['price_score'] ?? 0, 1) }} |
                        Skills {{ number_format($provider['skills_score'] ?? 0, 1) }} |
                        History {{ number_format($provider['history_score'] ?? 0, 1) }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-white-50">No matching providers found.</div>
        @endforelse
    </div>
</div>
@endsection
