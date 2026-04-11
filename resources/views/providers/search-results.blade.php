@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Providers in Your Area</h2>
        <button onclick="findProvidersNearMe()" id="searchBtn" class="btn btn-warning fw-bold text-dark">
            <span id="btnText">Update My Location</span>
            <div id="btnSpinner" class="spinner-border spinner-border-sm text-dark d-none" role="status"></div>
        </button>
    </div>

    <form id="locationSearchForm" action="{{ route('providers.search') }}" method="GET" class="d-none">
        <input type="hidden" name="lat" id="customer_lat">
        <input type="hidden" name="lng" id="customer_lng">
    </form>

    <div id="locationError" class="alert alert-danger d-none"></div>

    <hr>

    @if(!$customerLat || !$customerLng)
        <div class="alert alert-info text-center">
            <strong>We need your location!</strong> Please click the button above and allow location access to see providers near you.
        </div>
    @elseif($providers->isEmpty())
        <div class="alert alert-warning text-center">
            We couldn't find any providers whose service radius covers your exact location. Try checking back later!
        </div>
    @else
        <div class="row">
            @foreach($providers as $provider)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm bg-dark text-white border-secondary">
                        <div class="card-body">
                            <h5 class="card-title text-warning">
                                {{ $provider->user->name ?? 'Unknown Provider' }}
                                @if($provider->is_verified)
                                    <span class="badge bg-success ms-2 fs-6">✓ Verified</span>
                                @endif
                            </h5>
                            
                            <div class="mb-3">
                                <span class="badge bg-secondary">⭐ {{ $provider->average_rating }} ({{ $provider->total_ratings }})</span>
                                <span class="badge bg-info text-dark">{{ number_format($provider->distance, 1) }} km away</span>
                            </div>

                            <p class="card-text mb-1"><strong>Service Area:</strong> {{ $provider->service_area ?? 'N/A' }}</p>
                            <p class="card-text mb-1"><strong>Rate:</strong> {{ $provider->hourly_rate ?? $provider->fixed_rate ?? 'Negotiable' }}</p>
                            <p class="card-text text-muted small mt-3 border-top border-secondary pt-2">
                                Provider's Max Radius: {{ $provider->service_radius_km }} km
                            </p>
                            
                            <a href="{{ route('provider.show', $provider->user_id) }}" class="btn btn-outline-light w-100 mt-2">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
function findProvidersNearMe() {
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const searchBtn = document.getElementById('searchBtn');
    const errorBox = document.getElementById('locationError');

    // UI Loading State
    btnText.textContent = "Locating...";
    btnSpinner.classList.remove('d-none');
    searchBtn.disabled = true;
    errorBox.classList.add('d-none');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Success! Fill the hidden form and submit
                document.getElementById('customer_lat').value = position.coords.latitude;
                document.getElementById('customer_lng').value = position.coords.longitude;
                document.getElementById('locationSearchForm').submit();
            }, 
            function(error) {
                // Handle Errors (User denied access, etc.)
                btnText.textContent = "Update My Location";
                btnSpinner.classList.add('d-none');
                searchBtn.disabled = false;
                
                errorBox.classList.remove('d-none');
                if (error.code === error.PERMISSION_DENIED) {
                    errorBox.textContent = "Location access was denied. Please allow location access in your browser settings to find providers.";
                } else {
                    errorBox.textContent = "Unable to retrieve your location at this time. Please try again.";
                }
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        errorBox.classList.remove('d-none');
        errorBox.textContent = "Your browser does not support geolocation.";
    }
}

// Auto-trigger if they just landed on the page and haven't searched yet
window.onload = function() {
    @if(!$customerLat)
        findProvidersNearMe();
    @endif
};
</script>
@endsection