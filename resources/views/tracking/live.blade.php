@extends('layouts.app')
@section('title', 'Service Booking | TaskHive')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif !important;
        background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
        color: white;
        min-height: 100vh;
        margin: 0;
        padding-bottom: 50px;
    }
    
    .tracking-container {
        margin-top: 40px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }
    
    #map {
        height: 500px;
        width: 100%;
        border-radius: 15px;
        border: 2px solid #28a745; 
        margin-top: 20px;
        z-index: 1; 
        display: none; 
        background: #ffffff; /* Sets a light base for the white map */
    }
    
    .status-badge {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .status-requested { background: #ffd700; color: #000; }
    .status-accepted, .status-in_progress { background: #28a745; color: #fff; }
    .status-completed { background: #17a2b8; color: #fff; }
    
    .provider-info { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
    
    .provider-avatar {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; color: #ffd700; border: 2px solid #ffd700;
    }

    .waiting-room, .review-room, .reveal-room {
        text-align: center;
        padding: 60px 20px;
        background: rgba(0,0,0,0.3);
        border-radius: 15px;
        border: 1px dashed rgba(255,215,0,0.5);
        margin-top: 20px;
    }

    .btn-reveal-map {
        background: #28a745;
        color: white;
        border: none;
        padding: 15px 35px;
        font-size: 18px;
        border-radius: 50px;
        font-weight: 600;
        margin-top: 20px;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
    }

    .btn-reveal-map:hover {
        background: #218838;
        transform: translateY(-3px);
        color: white;
    }

    .rating-select, .review-textarea {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 12px;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .rating-select option { color: black; }
    .btn-submit-review { background: #ffd700; color: #000; font-weight: 600; padding: 12px 30px; border-radius: 50px; border: none; }

    /* Fix to ensure FontAwesome icons show correctly on the white map */
    .custom-div-icon i {
        filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.3));
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="tracking-container">
                
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div class="provider-info m-0">
                        <div class="provider-avatar">
                            <i class="fas fa-user-hard-hat"></i>
                        </div>
                        <div>
                            <h3 class="m-0">
                                @if(Auth::id() == $tracking->provider_id)
                                    Customer: {{ $customer->name ?? 'User' }}
                                @else
                                    Provider: {{ $provider->name ?? 'Your Provider' }}
                                @endif
                            </h3>
                            <p class="text-muted m-0" style="color: rgba(255,255,255,0.7) !important;">
                                Booking ID #{{ $tracking->id }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <span class="status-badge status-{{ $tracking->status }}" id="statusBadge">
                            @if($tracking->status === 'requested')
                                <i class="fas fa-spinner fa-spin me-2"></i> Waiting for Acceptance
                            @elseif($tracking->status === 'accepted' || $tracking->status === 'in_progress')
                                <i class="fas fa-route me-2"></i> In Progress
                            @elseif($tracking->status === 'completed')
                                <i class="fas fa-check-circle me-2"></i> Job Completed
                            @endif
                        </span>
                    </div>
                </div>

                @if($tracking->status === 'requested')
                    <div class="waiting-room">
                        <i class="fas fa-hourglass-half mb-3" style="font-size: 50px; color: #ffd700;"></i>
                        <h2>Request Sent!</h2>
                        <p style="color: #ccc; font-size: 18px;">Please wait. When the request is accepted, the map will become available.</p>
                    </div>
                    <script>setTimeout(function(){ window.location.reload(); }, 5000);</script>

                @elseif($tracking->status === 'accepted' || $tracking->status === 'in_progress')
                    <div id="revealSection" class="reveal-room">
                        <i class="fas fa-check-circle mb-3" style="font-size: 50px; color: #28a745;"></i>
                        <h2 class="text-success">Connection Established!</h2>
                        <p style="color: #ccc;">The live tracking system is ready.</p>
                        
                        <button class="btn-reveal-map" onclick="revealMap()">
                            <i class="fas fa-map-marked-alt me-2"></i> Reveal Live Tracking Map
                        </button>
                    </div>

                    <div id="map"></div>

                @elseif($tracking->status === 'completed')
                    <div class="review-room">
                        <i class="fas fa-star mb-3" style="font-size: 50px; color: #ffd700;"></i>
                        <h2>Task Completed!</h2>
                        @if(Auth::id() == $tracking->customer_id)
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <form action="{{ route('rating.store', $provider->id) }}" method="POST">
                                        @csrf
                                        <select name="rating" class="rating-select" required>
                                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                            <option value="3">⭐⭐⭐ (3/5)</option>
                                        </select>
                                        <textarea name="review" class="review-textarea" placeholder="Write a review..." required></textarea>
                                        <button type="submit" class="btn-submit-review">Submit Feedback</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <p>Good job! The customer has been notified to leave a review.</p>
                        @endif
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

@if($tracking->status === 'accepted' || $tracking->status === 'in_progress')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function revealMap() {
        // Hide the reveal panel and show the map container
        document.getElementById('revealSection').style.display = 'none';
        const mapElement = document.getElementById('map');
        mapElement.style.display = 'block';

        const customerLat = {{ $customerLocation['lat'] }};
        const customerLng = {{ $customerLocation['lng'] }};
        let providerLat = {{ $tracking->current_lat ?? '23.8150' }};
        let providerLng = {{ $tracking->current_lng ?? '90.4200' }};

        // Initialize Map
        const map = L.map('map').setView([customerLat, customerLng], 14);

        // WHITE MAP TILES (Standard OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const customerIcon = L.divIcon({
            html: '<div style="font-size: 35px; color: #ff6b6b;"><i class="fas fa-map-marker-alt"></i></div>',
            className: 'custom-div-icon', iconSize: [35, 45], iconAnchor: [17, 45]
        });

        const providerIcon = L.divIcon({
            html: '<div style="font-size: 35px; color: #ffd700;"><i class="fas fa-motorcycle"></i></div>',
            className: 'custom-div-icon', iconSize: [35, 45], iconAnchor: [17, 45]
        });

        const customerMarker = L.marker([customerLat, customerLng], {icon: customerIcon}).addTo(map)
            .bindPopup('<b>Customer Location</b>');
        const providerMarker = L.marker([providerLat, providerLng], {icon: providerIcon}).addTo(map)
            .bindPopup('<b>Provider: {{ $provider->name }}</b>');

        const group = new L.featureGroup([customerMarker, providerMarker]);
        map.fitBounds(group.getBounds().pad(0.3));

        // --- FAST MOVEMENT LOGIC (50ms) ---
        const steps = 200; 
        const latStep = (customerLat - providerLat) / steps;
        const lngStep = (customerLng - providerLng) / steps;
        let currentStep = 0;

        const movementInterval = setInterval(() => {
            if (currentStep >= steps) {
                clearInterval(movementInterval);
                providerMarker.bindPopup('<b>Arrived!</b>').openPopup();
                return;
            }
            providerLat += latStep;
            providerLng += lngStep;
            providerMarker.setLatLng([providerLat, providerLng]);
            currentStep++;
        }, 50); 

        // Fix for Leaflet initialization in hidden div
        setTimeout(() => { map.invalidateSize(); }, 200);
    }
</script>
@endif
@endsection