@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>{{ $provider->user->name ?? 'Provider Profile' }}</h2>
                        @if($provider->is_verified)
                            <span class="badge bg-success fs-6">Verified Professional</span>
                        @endif
                    </div>

                    <p class="lead">{{ $provider->bio ?? 'No bio provided.' }}</p>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Experience:</strong> {{ $provider->experience_years }} years</p>
                            <p><strong>Hourly Rate:</strong> {{ $provider->hourly_rate ?? 'N/A' }}</p>
                            <p><strong>Fixed Rate:</strong> {{ $provider->fixed_rate ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Rating:</strong> ⭐ {{ $provider->average_rating }} ({{ $provider->total_ratings }} reviews)</p>
                            <p><strong>Service Radius:</strong> Up to {{ $provider->service_radius_km }} km</p>
                            <p><strong>General Area:</strong> {{ $provider->service_area ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if(!empty($provider->skills))
                        <div class="mb-3">
                            <strong>Skills:</strong>
                            @foreach($provider->skills as $skill)
                                <span class="badge bg-secondary me-1">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($provider->certifications)
                        <div class="mb-4">
                            <strong>Certifications:</strong>
                            <p>{{ $provider->certifications }}</p>
                        </div>
                    @endif

                    <a href="{{ route('providers.search') }}" class="btn btn-outline-secondary">Back to Search</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection