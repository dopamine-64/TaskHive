@extends('layouts.app')
@section('title', 'TaskHive | Search Results')

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
    
    .hero-title em {
        font-style: italic;
        color: #ffd700;
    }
    
    .hero-subtitle {
        font-size: 18px;
        opacity: 0.9;
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
    
    .service-card h5 {
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .service-card .text-muted {
        color: rgba(255,255,255,0.7) !important;
    }
    
    .price {
        font-size: 28px;
        font-weight: 700;
        color: #ffd700;
        margin: 15px 0;
    }
    
    .badge-category {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-block;
        width: fit-content;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        color: white;
    }
    
    .empty-state i {
        font-size: 60px;
        margin-bottom: 20px;
        color: #ffd700;
    }
    
    .empty-state h4 {
        font-size: 24px;
        font-weight: 600;
    }
    
    .back-link {
        margin-top: 40px;
        text-align: center;
    }
    
    .back-link a {
        color: white;
        text-decoration: none;
        opacity: 0.8;
    }
    
    .back-link a:hover {
        opacity: 1;
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

    /* --- CUSTOM LOCATION MODAL CSS --- */
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
        <h1 class="hero-title">Search Results</h1>
        <p class="hero-subtitle">Showing services that match your criteria</p>
    </div>
</div>

<div class="container text-center" style="z-index: 10; position: relative;">
    <div class="results-pill shadow-sm">
        <i class="fas fa-chart-line"></i> {{ $services->count() }} Services Found
    </div>

    <div class="row g-4">
        @forelse($services as $service)
        <div class="col-md-4">
            <div class="service-card shadow">
                <h5>{{ $service->title }}</h5>
                <p class="text-muted small mb-2 flex-grow-1">{{ Str::limit($service->description, 80) }}</p>
                
                <div class="small mb-2" style="color: rgba(255,255,255,0.9); font-weight: 500;">
                    <i class="fas fa-map-marker-alt me-1" style="color: #ff6b6b;"></i> {{ $service->location ?? 'Location not specified' }}
                </div>
                
                <div class="price">৳{{ number_format($service->price) }}</div>
                <span class="badge-category">{{ $service->category }}</span>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state shadow">
                <i class="fas fa-search"></i>
                <h4>No services found</h4>
                <p>Try different filters or go back to dashboard</p>
                <a href="{{ url('/dashboard') }}" style="background: #ffd700; color: #000; padding: 10px 30px; border-radius: 50px; text-decoration: none; display: inline-block; margin-top: 20px; font-weight: 600;">Back to Dashboard</a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $services->links() }}
    </div>

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
    
    // NEW UNIQUE KEY: This guarantees the popup will show right now even if you clicked 'Not Now' before.
    if (!sessionStorage.getItem('ask_location_on_services_page')) {
        setTimeout(() => {
            modal.classList.remove('d-none');
            void modal.offsetWidth; // Trigger CSS reflow
            modal.classList.add('show');
        }, 300); // Trigger quickly (300ms) after page load
    }
});

function closeLocationModal() {
    const modal = document.getElementById('customLocationModal');
    modal.classList.remove('show');
    
    // Wait for the fade-out animation to finish, then hide entirely
    setTimeout(() => {
        modal.classList.add('d-none');
    }, 300);
    
    // Mark as asked so it doesn't repeatedly spam the user on refresh
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
                // Save decision and redirect with location data
                sessionStorage.setItem('ask_location_on_services_page', 'true');
                window.location.href = `/providers/search?lat=${position.coords.latitude}&lng=${position.coords.longitude}`;
            },
            function(error) {
                btnAccept.textContent = "Yes, Find Providers";
                btnAccept.disabled = false;
                errorBox.classList.remove('d-none');
                errorBox.textContent = "Location access denied. Please allow location permissions in your browser.";
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