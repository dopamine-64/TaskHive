@extends('layouts.app')

@section('styles')
<style>
    body {
        font-family: 'Poppins', sans-serif !important;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
        min-height: 100vh;
        margin: 0;
        padding-bottom: 50px;
    }
</style>
@endsection

@section('content')
<div class="container py-5" style="z-index: 10; position: relative;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white mb-0" style="font-weight: 600;">Providers in Your Area</h2>
        <button onclick="findProvidersNearMe()" id="searchBtn" class="btn shadow-sm px-4 py-2" style="background-color: #f8f9fa; color: #005c4b; border-radius: 30px; font-weight: 600;">
            <i class="fas fa-location-arrow me-2"></i><span id="btnText">Update My Location</span>
            <div id="btnSpinner" class="spinner-border spinner-border-sm text-success d-none ms-2" role="status"></div>
        </button>
    </div>

    <form id="locationSearchForm" action="{{ route('providers.search') }}" method="GET" class="d-none">
        <input type="hidden" name="lat" id="customer_lat">
        <input type="hidden" name="lng" id="customer_lng">
    </form>

    <div id="locationError" class="alert alert-danger shadow-sm d-none" style="border-radius: 12px;"></div>

    <hr style="border-color: rgba(255,255,255,0.2);">

    @if(empty($customerLat) || empty($customerLng))
        <div class="alert shadow-sm text-center" style="background-color: rgba(255,255,255,0.95); border-radius: 12px; color: #3b5249;">
            <strong>📍 We need your location!</strong> Please click the button above and allow location access to see providers near you.
        </div>
    @elseif($providers->isEmpty())
        <div class="alert shadow-sm text-center" style="background-color: rgba(255,255,255,0.95); border-radius: 12px; color: #856404; border-left: 5px solid #ffc107;">
            We couldn't find any providers whose service radius covers your exact location. Try checking back later!
        </div>
    @else
        <div class="row g-4 mt-2">
            @foreach($providers as $provider)
                <div class="col-md-4">
                    <div class="card h-100 shadow-lg d-flex flex-column" style="background: rgba(255,255,255,0.95); border: none; border-radius: 16px;">
                        <div class="card-body text-dark p-4 flex-grow-1">
                            <h5 class="card-title fw-bold mb-3" style="color: #005c4b;">
                                {{ $provider->user->name ?? 'Unknown Provider' }}
                                @if($provider->is_verified)
                                    <span class="badge shadow-sm ms-1" style="background-color: #005c4b; font-size: 0.75rem; vertical-align: middle;">✓ Verified</span>
                                @endif
                            </h5>
                            
                            <div class="mb-3">
                                <span class="badge me-1" style="background-color: #3b5249; font-weight: 500;">⭐ {{ $provider->average_rating ?? '0.0' }} ({{ $provider->total_ratings ?? '0' }})</span>
                                <span class="badge" style="background-color: #e1e8e5; color: #005c4b; font-weight: 600;">📍 {{ number_format($provider->distance, 1) }} km away</span>
                            </div>

                            <p class="card-text mb-2" style="font-size: 0.95rem; color: #4a5568;">
                                <strong>Service Area:</strong> {{ $provider->service_area ?? 'N/A' }}
                            </p>
                            <p class="card-text mb-3" style="font-size: 0.95rem; color: #4a5568;">
                                <strong>Rate:</strong> ৳{{ $provider->hourly_rate ?? $provider->fixed_rate ?? 'Negotiable' }}
                            </p>
                            
                            <p class="card-text text-muted small mt-4 border-top pt-3" style="border-color: #cdd6d2 !important;">
                                Provider's Max Radius: {{ $provider->service_radius_km ?? '0' }} km
                            </p>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 pb-4 px-4 pt-0 mt-auto">
                            <a href="{{ route('provider.show', $provider->user_id) }}" class="btn w-100 text-white shadow-sm" style="background-color: #005c4b; border-radius: 20px; font-weight: 500; transition: all 0.3s ease;">
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
    @if(empty($customerLat))
        findProvidersNearMe();
    @endif
};
</script>
@endsection