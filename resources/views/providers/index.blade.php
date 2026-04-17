@extends('layouts.app')
@section('title', 'TaskHive | Nearby Providers')

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
    
    .hero-section {
        text-align: center;
        padding: 60px 0 40px;
        color: white;
        position: relative;
        z-index: 10;
    }
    
    .hero-title {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .results-pill {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 10px 30px;
        display: inline-block;
        margin-bottom: 40px;
        font-weight: 600;
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .service-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 25px;
        transition: 0.3s;
        text-align: left;
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .service-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.2);
    }

    .recommended-card {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.25), rgba(255, 255, 255, 0.12));
        border: 1px solid rgba(255, 215, 0, 0.45);
    }

    .match-pill {
        display: inline-block;
        background: #ffd700;
        color: #000;
        border-radius: 50px;
        padding: 4px 12px;
        font-weight: 700;
        font-size: 12px;
    }

    .section-title {
        color: #ffd700;
        font-size: 1.5rem;
        font-weight: 700;
        text-align: left;
        margin-bottom: 20px;
    }
    
    .btn-view-provider {
        background: #ffd700;
        color: #000;
        font-weight: 600;
        border-radius: 50px;
        text-decoration: none;
        padding: 10px 0;
        text-align: center;
        transition: 0.3s;
        border: none;
        display: block;
        width: 100%;
    }

    .btn-view-provider:hover {
        background: #e6c200;
        color: #000;
        transform: scale(1.02);
    }

    .btn-book-provider {
        background: #28a745;
        color: #fff;
        font-weight: 600;
        border-radius: 50px;
        text-decoration: none;
        padding: 10px 0;
        text-align: center;
        transition: 0.3s;
        border: none;
        display: block;
        width: 100%;
    }

    .btn-book-provider:hover {
        background: #218838;
        color: #fff;
        transform: scale(1.02);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        color: white;
    }
    
    .pagination {
        justify-content: center;
    }
    
    .page-link {
        background: rgba(255,255,255,0.15);
        border: none;
        color: white;
        margin: 0 5px;
        border-radius: 10px;
    }
    
    .page-item.active .page-link {
        background: #ffd700;
        color: #000;
    }

    .custom-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px);
        z-index: 9999; display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .custom-modal-overlay.show { opacity: 1; }
    .custom-modal-box {
        background: rgba(20, 20, 20, 0.95); border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px; padding: 40px 30px; max-width: 400px;
        text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        transform: translateY(20px); transition: transform 0.3s ease;
    }
    .custom-modal-overlay.show .custom-modal-box { transform: translateY(0); }
    .modal-icon { font-size: 40px; margin-bottom: 15px; }
    .modal-title { color: #ffffff; font-size: 1.5rem; font-weight: 600; margin-bottom: 10px; font-family: 'Poppins', sans-serif; }
    .modal-text { color: #a0a0a0; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.5; }
    .modal-buttons { display: flex; gap: 15px; }
    .btn-dismiss {
        flex: 1; padding: 12px; background: rgba(255,255,255,0.1); color: #fff;
        border: none; border-radius: 30px; cursor: pointer; font-weight: 500; transition: background 0.2s;
    }
    .btn-dismiss:hover { background: rgba(255,255,255,0.2); }
    .btn-accept {
        flex: 1; padding: 12px; background: #d4af37; color: #000;
        border: none; border-radius: 30px; cursor: pointer; font-weight: 600; transition: transform 0.2s;
    }
    .btn-accept:hover { transform: scale(1.05); }
    .modal-error { color: #ff6b6b; font-size: 0.85rem; margin-bottom: 15px; }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Nearby Providers</h1>
        <p class="hero-subtitle" style="opacity: 0.9;">Showing the best experts in your area</p>
    </div>
</div>

<div class="container text-center" style="z-index: 10; position: relative;">
    @php
        $recommendedProviders = $recommendedProviders ?? collect();
        $totalProviders = $totalProviders ?? ($providers->total() + $recommendedProviders->count());
        $isCustomer = $isCustomer ?? false;
    @endphp
    
    @if($totalProviders > 0)
        <div class="results-pill shadow-sm">
            <i class="fas fa-users"></i> {{ $totalProviders }} Providers Found
        </div>
    @endif

    @if($isCustomer && $recommendedProviders->count() > 0)
        <h2 class="section-title mt-2">Recommended for You</h2>
        <div class="row g-4 mb-5">
            @foreach($recommendedProviders as $provider)
                <div class="col-md-4">
                    <div class="service-card recommended-card shadow">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0">{{ $provider->user->name ?? 'Unknown Provider' }}</h5>
                            <span class="match-pill">{{ number_format($provider->match_percentage ?? 0, 1) }}% Match</span>
                        </div>

                        <p class="text-muted small mb-2 flex-grow-1" style="color: rgba(255,255,255,0.8) !important;">
                            {{ Str::limit($provider->bio, 80) ?? 'No bio available.' }}
                        </p>

                        <div class="small mb-2" style="color: rgba(255,255,255,0.95); font-weight: 500;">
                            <i class="fas fa-map-marker-alt me-1" style="color: #ff6b6b;"></i>
                            {{ $provider->service_area ?? $provider->latest_service_location ?? 'Location not specified' }}

                            <br><small style="color: #ffd700;">({{ $provider->distance !== null ? number_format($provider->distance, 1).' km away' : 'Unknown' }})</small>
                        </div>

                        <div class="small mb-3 text-start" style="color: rgba(255,255,255,0.85);">
                            <div>Location: {{ number_format($provider->match_breakdown['location'] ?? 0, 1) }}</div>
                            <div>Rating: {{ number_format($provider->match_breakdown['rating'] ?? 0, 1) }}</div>
                            <div>Price: {{ number_format($provider->match_breakdown['price'] ?? 0, 1) }}</div>
                            <div>Skills: {{ number_format($provider->match_breakdown['skills'] ?? 0, 1) }}</div>
                        </div>

                        <div class="mt-auto pt-3 d-flex flex-column gap-2">
                            <a href="{{ route('provider.show', $provider->user_id) }}" class="btn-view-provider">
                                View Profile
                            </a>

                            <form action="{{ route('tracking.initiate', $provider->user_id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn-book-provider">
                                    <i class="fas fa-calendar-check me-1"></i> Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="section-title">All Providers</h2>
    <div class="row g-4">
        @forelse($providers as $provider)
        <div class="col-md-4">
            <div class="service-card shadow">
                <h5>{{ $provider->user->name ?? 'Unknown Provider' }}</h5>
                
                <p class="text-muted small mb-2 flex-grow-1" style="color: rgba(255,255,255,0.7) !important;">
                    {{ Str::limit($provider->bio, 80) ?? 'No bio available.' }}
                </p>
                
                <div class="small mb-2" style="color: rgba(255,255,255,0.9); font-weight: 500;">
                    <i class="fas fa-map-marker-alt me-1" style="color: #ff6b6b;"></i> 
                    {{ $provider->service_area ?? $provider->latest_service_location ?? 'Location not specified' }}
                    
                    <br><small style="color: #ffd700;">({{ $provider->distance !== null ? number_format($provider->distance, 1).' km away' : 'Unknown' }})</small>
                </div>
                
                <div class="mt-auto pt-3 d-flex flex-column gap-2">
                    <a href="{{ route('provider.show', $provider->user_id) }}" class="btn-view-provider">
                        View Profile
                    </a>

                    <form action="{{ route('tracking.initiate', $provider->user_id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-book-provider">
                            <i class="fas fa-calendar-check me-1"></i> Book Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state shadow">
                <i class="fas fa-search" style="font-size: 60px; margin-bottom: 20px; color: #ffd700;"></i>
                <h4 style="font-size: 24px; font-weight: 600;">No additional providers available</h4>
                <p>Try broadening your filters or updating your location.</p>
                <a href="{{ url('/dashboard') }}" style="background: #ffd700; color: #000; padding: 10px 30px; border-radius: 50px; text-decoration: none; display: inline-block; margin-top: 20px; font-weight: 600;">Back to Dashboard</a>
            </div>
        </div>
        @endforelse
    </div>

    @if($providers->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $providers->links() }}
    </div>
    @endif
</div>

<div id="customLocationModal" class="custom-modal-overlay d-none">
    <div class="custom-modal-box">
        <div class="modal-icon">📍</div>
        <h3 class="modal-title">Find Nearby Providers?</h3>
        <p class="modal-text">Would you like to use your live location to instantly find the best experts in your immediate area?</p>
        
        <div id="modalError" class="modal-error d-none"></div>

        <div class="modal-buttons">
            <button type="button" onclick="closeLocationModal()" class="btn-dismiss">Not Now</button>
            <button type="button" onclick="acceptLocationSearch()" id="btnAccept" class="btn-accept">Yes, Find Providers</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('customLocationModal');
    
    const urlParams = new URLSearchParams(window.location.search);
    const hasCoordinates = urlParams.has('lat') && urlParams.has('lng');
    const hasStoredLocation = @json(auth()->user()->latitude !== null && auth()->user()->longitude !== null);
    
    if (!sessionStorage.getItem('ask_location_on_services_page') && !hasCoordinates && !hasStoredLocation) {
        setTimeout(() => {
            modal.classList.remove('d-none');
            void modal.offsetWidth; 
            modal.classList.add('show');
        }, 300);
    }
});

function closeLocationModal() {
    const modal = document.getElementById('customLocationModal');
    modal.classList.remove('show');
    
    setTimeout(() => {
        modal.classList.add('d-none');
    }, 300);
    
    sessionStorage.setItem('ask_location_on_services_page', 'true');
}

function acceptLocationSearch() {
    const btnAccept = document.getElementById('btnAccept');
    const errorBox = document.getElementById('modalError');
    
    btnAccept.textContent = "Locating...";
    btnAccept.disabled = true;
    errorBox.classList.add('d-none');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                sessionStorage.setItem('ask_location_on_services_page', 'true');
                window.location.href = `/providers/search?lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
            },
            function(error) {
                btnAccept.textContent = "Yes, Find Providers";
                btnAccept.disabled = false;
                errorBox.classList.remove('d-none');
                errorBox.textContent = "Location access denied. Please allow location permissions in your browser settings.";
            }
        );
    } else {
        errorBox.classList.remove('d-none');
        errorBox.textContent = "Geolocation is not supported by your browser.";
        btnAccept.textContent = "Yes, Find Providers";
    }
}
</script>
@endsection
