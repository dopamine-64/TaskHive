@extends('layouts.app')
@section('title', 'Browse Services')

@section('styles')
<style>
    body {
        /* Adds the dark overlay and background image */
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
    }
</style>
@endsection

@section('content')
<div class="container py-5" style="z-index: 10;">
    <h2 class="text-white mb-4" style="font-family: 'Playfair Display', serif;">Available Services</h2>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow" id="success-alert" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($services as $service)
            <div class="col-md-3">
                <div class="card h-100 shadow" style="background: rgba(255,255,255,0.95); border: none; border-radius: 12px;">
                    <div class="card-body text-dark">
                        <span class="badge mb-2" style="background-color: #005c4b;">{{ $service->category }}</span>
                        <h5 class="card-title fw-bold">{{ $service->title }}</h5>
                        <h6 class="card-subtitle mb-1 text-muted">By {{ $service->provider->name }}</h6>
                        
                        {{-- Location display added here --}}
                        <div class="mb-2 text-secondary small" style="font-weight: 500;">
                            📍 {{ $service->location }}
                        </div>

                        <p class="card-text small text-truncate">{{ $service->description }}</p>
                        
                        <h4 class="mt-3 text-success" style="color: #005c4b !important;">৳{{ number_format($service->price, 0) }} Tk</h4>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <button class="btn w-100 text-white" style="background-color: #005c4b; border-radius: 20px;">Buy</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alertBox = document.getElementById('success-alert');
        if (alertBox) {
            setTimeout(() => {
                const alert = new bootstrap.Alert(alertBox);
                alert.close();
            }, 2000);
        }
    });
</script>
@endsection