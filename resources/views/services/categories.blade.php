@extends('layouts.app')
@section('title', 'TaskHive | Available Services')

@section('styles')
<style>
    /* I added !important to your body styles to ensure they override the default layout correctly */
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
    
    .service-card h5 {
        font-weight: 700;
        margin-bottom: 10px;
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
    
    .btn-book-now {
        background: linear-gradient(135deg, #ffd700, #ffb347);
        color: #000;
        border: none;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        width: 100%;
        margin-top: 15px;
        transition: transform 0.2s;
    }
    
    .btn-book-now:hover {
        transform: scale(1.02);
        color: #000;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        color: white;
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
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title"><i class="fas fa-list"></i> Available Services</h1>
        <p>Browse all services from our trusted providers</p>
    </div>
</div>

<div class="container text-center" style="z-index: 10; position: relative;">
    <div class="results-pill shadow-sm">
        <i class="fas fa-chart-line"></i> {{ $services->count() }} Services Available
    </div>

    <div class="row g-4">
        @forelse($services as $service)
        <div class="col-md-4">
            <div class="service-card shadow">
                <h5>{{ $service->title }}</h5>
                <p class="text-white-50 small mb-2 flex-grow-1">{{ Str::limit($service->description, 80) }}</p>
                
                <div class="small mb-2" style="color: rgba(255,255,255,0.9); font-weight: 500;">
                    <i class="fas fa-map-marker-alt me-1" style="color: #ff6b6b;"></i> {{ $service->location ?? 'Location not specified' }}
                </div>

                <div class="price">৳{{ number_format($service->price) }}</div>
                <span class="badge-category">{{ $service->category }}</span>
                
                <!-- BOOK NOW BUTTON - Only for customers -->
                @if(auth()->user()->role == 'user')
                    <a href="{{ route('booking.create', $service->id) }}" class="btn-book-now">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state shadow">
                <i class="fas fa-search fa-3x mb-3 text-white-50"></i>
                <h4>No services available</h4>
                <p>Please check back later</p>
                <a href="{{ url('/dashboard') }}" style="background: #ffd700; color: #000; padding: 10px 30px; border-radius: 50px; text-decoration: none; display: inline-block; margin-top: 20px; font-weight: 600;">Back to Dashboard</a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-5">
        {{ $services->links() }}
    </div>

</div>
@endsection