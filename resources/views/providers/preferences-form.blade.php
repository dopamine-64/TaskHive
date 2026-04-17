@extends('layouts.app')
@section('title', 'TaskHive | Matching Preferences')

@section('styles')
<style>
    .hero-section { text-align: center; padding: 45px 0 30px; color: white; }
    .hero-title { font-size: 42px; font-weight: 700; margin-bottom: 10px; }
    .glass-card {
        max-width: 760px; margin: 0 auto 40px;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
        border-radius: 18px; backdrop-filter: blur(10px); color: white; padding: 24px;
    }
    .form-control, .form-select {
        background: rgba(255,255,255,0.14); color: white; border: 1px solid rgba(255,255,255,0.3);
    }
    .form-control::placeholder { color: rgba(255,255,255,0.75); }
    .btn-save {
        background: #ffd700; color: #000; font-weight: 600; border: none; border-radius: 50px; padding: 10px 24px;
    }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Matching Preferences</h1>
        <p style="opacity: 0.9;">Customize budget, radius, and preferred categories</p>
    </div>
</div>

<div class="container pb-5">
    <div class="glass-card shadow">
        <form method="POST" action="{{ url('/api/user/preferences') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Minimum Budget (BDT)</label>
                    <input type="number" class="form-control" name="preferred_price_min" step="0.01" min="0" value="{{ old('preferred_price_min', $preferences->preferred_price_min ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maximum Budget (BDT)</label>
                    <input type="number" class="form-control" name="preferred_price_max" step="0.01" min="0" value="{{ old('preferred_price_max', $preferences->preferred_price_max ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Preferred Radius (km)</label>
                    <input type="number" class="form-control" name="preferred_radius_km" step="0.1" min="0.1" value="{{ old('preferred_radius_km', $preferences->preferred_radius_km ?? 10) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Preferred Categories (comma separated)</label>
                    <input type="text" class="form-control" name="preferred_categories_text" placeholder="plumbing,electrical,cleaning" value="{{ old('preferred_categories_text', isset($preferences) ? implode(',', $preferences->preferred_categories ?? []) : '') }}">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="auto_match_enabled" value="1" id="auto_match_enabled" {{ old('auto_match_enabled', $preferences->auto_match_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_match_enabled">Enable automatic recommendation generation</label>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn-save">Save Preferences</button>
            </div>
        </form>
    </div>
</div>
@endsection
