@extends('layouts.app')

@section('title', 'Dashboard - TaskHive')

@section('content')
    <div class="hero-section">
        <h1 class="hero-title">Find Expert Services<br>Instantly <em>& Enjoy</em></h1>
        <p class="hero-subtitle">Explore top-rated professionals across top categories from Home Maintenance to Web Development and Creative Arts.</p>

        <form method="GET" action="http://127.0.0.1:8000/services">
            <div class="search-glass-panel">
                <div class="search-field">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="City or Area...">
                </div>
                <div class="search-field">
                    <label>Service Type</label>
                    <input type="text" name="category" placeholder="e.g. Plumber, Designer...">
                </div>
                <div class="search-field">
                    <label>Price Range</label>
                    <input type="text" name="price_range" placeholder="Min - Max">
                </div>
                <button type="submit" class="search-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </form>

        <div class="stats-container">
            <div class="stat-item">
                <h3>0+</h3>
                <p>Categories</p>
            </div>
            <div class="stat-item">
                <h3>0K+</h3>
                <p>Services Booked</p>
            </div>
            <div class="stat-item">
                <h3>0K+</h3>
                <p>Happy Users</p>
            </div>
            <div class="stat-item">
                <h3>0.0</h3>
                <p>Average Rating</p>
            </div>
        </div>
    </div>
@endsection