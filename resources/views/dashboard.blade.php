@extends('layouts.app')

@section('content')
<style>
    .hero-section {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 0 2rem;
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 4.5rem;
        font-weight: 600;
        line-height: 1.1;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .hero-title em { font-style: italic; }

    .hero-subtitle {
        font-size: 1rem;
        font-weight: 300;
        max-width: 600px;
        margin: 0 auto 2rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .search-glass-panel {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 15px;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        max-width: 800px;
        width: 100%;
        margin: 2rem auto 4rem;
    }

    .search-field {
        flex: 1;
        padding: 0.5rem 1.5rem;
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        text-align: left;
    }

    .search-field:last-of-type { border-right: none; }

    .search-field label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.2rem;
    }

    .search-field input {
        background: transparent;
        border: none;
        color: white;
        font-size: 0.95rem;
        width: 100%;
        outline: none;
    }

    .search-btn {
        background: white;
        color: black;
        border: none;
        border-radius: 10px;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        margin-left: 1rem;
        transition: transform 0.2s;
    }

    .stats-container {
        display: flex;
        justify-content: center;
        gap: 4rem;
        padding-bottom: 3rem;
    }

    .stat-item h3 { font-size: 1.8rem; font-weight: 500; margin-bottom: 0.2rem; }
    .stat-item p { font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); margin: 0; text-transform: capitalize; }
</style>

<div class="hero-section">
    <h1 class="hero-title">Find Expert Services <br>Instantly <em>& Enjoy</em></h1>
    <p class="hero-subtitle">
        Explore top-rated professionals across top categories from Home Maintenance to Web Development and Creative Arts.
    </p>

    <!-- FIXED: Added FORM tag with method GET and action -->
    <form method="GET" action="{{ url('/services') }}">
        <div class="search-glass-panel">
            <div class="search-field">
                <label>Location</label>
                <!-- FIXED: Added name="location" -->
                <input type="text" name="location" placeholder="City or Area...">
            </div>
            <div class="search-field">
                <label>Service Type</label>
                <!-- FIXED: Added name="category" -->
                <input type="text" name="category" placeholder="e.g. Plumber, Designer..">
            </div>
            <div class="search-field">
                <label>Price Range</label>
                <!-- FIXED: Added name="price_range" -->
                <input type="text" name="price_range" placeholder="Min - Max">
            </div>
            <!-- FIXED: Changed button to type="submit" -->
            <button type="submit" class="search-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>
    </form>
        </form>
    

    
    <div class="stats-container">
    <div class="stats-container">
        <div class="stat-item"><h3>0+</h3><p>Categories</p></div>
        <div class="stat-item"><h3>0K+</h3><p>Services Booked</p></div>
        <div class="stat-item"><h3>0K+</h3><p>Happy Users</p></div>
        <div class="stat-item"><h3>0.0</h3><p>Average Rating</p></div>
    </div>
</div>
@endsection