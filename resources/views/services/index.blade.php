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
    
    {{-- Added an ID to the alert so JavaScript can find it --}}
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
                        <h6 class="card-subtitle mb-2 text-muted">By {{ $service->provider->name }}</h6>
                        <p class="card-text small text-truncate">{{ $service->description }}</p>
                        
                        {{-- Used number_format to remove decimals and add commas to large numbers --}}
                        <h4 class="mt-3 text-success">৳{{ number_format($service->price, 0) }} Tk</h4>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <button class="btn btn-outline-dark w-100" style="border-radius: 20px;">Buy</button>
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