@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Available Providers Near You</h2>

    @if($providers->isEmpty())
        <div class="alert alert-warning">Sorry, no providers currently service your exact location.</div>
    @else
        <div class="row">
            @foreach($providers as $provider)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $provider->user->name ?? 'Unknown Provider' }}
                                @if($provider->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @endif
                            </h5>
                            
                            <p class="card-text text-muted">
                                <strong>Distance:</strong> {{ number_format($provider->distance, 1) }} km away<br>
                                <strong>Rate:</strong> {{ $provider->hourly_rate ?? $provider->fixed_rate ?? 'Negotiable' }}<br>
                                <strong>Rating:</strong> ⭐ {{ $provider->average_rating }} ({{ $provider->total_ratings }} reviews)
                            </p>
                            
                            <a href="{{ route('providers.show', $provider->id) }}" class="btn btn-primary w-100">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection