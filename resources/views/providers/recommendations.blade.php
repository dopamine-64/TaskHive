@extends('layouts.app')
@section('title', 'TaskHive | Smart Recommendations')

@section('styles')
<style>
    .hero-section { text-align: center; padding: 45px 0 30px; color: white; }
    .hero-title { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    .results-pill {
        background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);
        border-radius: 50px; padding: 10px 24px; display: inline-block; color: white; font-weight: 600;
    }
    .service-card {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
        border-radius: 18px; backdrop-filter: blur(10px); color: white; padding: 20px; height: 100%;
    }
    .score-chip {
        background: #ffd700; color: #000; font-weight: 700; border-radius: 50px; padding: 4px 12px; font-size: 12px;
    }
    .meta { color: rgba(255,255,255,0.85); font-size: 13px; }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Recommended Providers</h1>
        <p style="opacity: 0.9;">Smart matches based on location, skills, rating, price, and history</p>
    </div>
</div>

<div class="container pb-5">
    <div class="text-center mb-4">
        <span class="results-pill">{{ count($providers ?? []) }} Recommendation{{ count($providers ?? []) === 1 ? '' : 's' }}</span>
    </div>

    <div class="row g-4">
        @forelse(($providers ?? []) as $provider)
            <div class="col-md-4">
                <div class="service-card shadow">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0">{{ $provider['provider_name'] ?? 'Provider' }}</h5>
                        <span class="score-chip">{{ number_format($provider['total_score'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="meta mb-1">Distance: {{ number_format($provider['distance_km'] ?? 0, 2) }} km</div>
                    <div class="meta mb-1">Rating: {{ number_format($provider['average_rating'] ?? 0, 2) }} / 5</div>
                    <div class="meta">Price: ৳{{ number_format($provider['effective_rate'] ?? 0, 0) }}</div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-white-50">No recommendations available yet.</div>
        @endforelse
    </div>
</div>
@endsection
