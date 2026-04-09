<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaskHive')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh; 
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('images/bg-1.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .custom-navbar {
            padding: 2rem 6rem;
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .brand-logo {
            justify-self: start;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: white;
            text-decoration: none;
        }

        .brand-logo span {
            color: #feb83e;
        }

        .nav-pill-menu {
            justify-self: center;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            display: flex;
            gap: 1.5rem;
        }

        .nav-pill-menu a {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            transition: background 0.3s;
        }

        .nav-pill-menu a:hover, .nav-pill-menu a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .custom-navbar .d-flex.align-items-center {
            justify-self: end; 
        }

        .btn-white {
            background-color: white;
            color: black;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            text-decoration: none;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

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

        .search-field input::placeholder { color: rgba(255, 255, 255, 0.5); }

        .search-btn {
            background: white;
            color: black;
            border: none;
            border-radius: 10px;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            margin-left: 1rem;
            transition: transform 0.2s;
        }

        .search-btn:hover { transform: scale(1.05); }

        .stats-container {
            display: flex;
            justify-content: center;
            gap: 4rem;
            padding-bottom: 3rem;
        }

        .stat-item h3 {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }

        .stat-item p {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            text-transform: capitalize;
        }
    </style>
    @yield('styles')
</head>
<body>

    <nav class="custom-navbar">
        <a href="{{ route('dashboard') }}" class="brand-logo">Task<span>Hive</span></a>
        
        <div class="nav-pill-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
            <a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.index') ? 'active' : '' }}">Categories</a>
            <a href="#">Providers</a>
            <a href="#">About</a>
            @if(auth()->check() && auth()->user()->role === 'provider')
                <a href="{{ route('services.create') }}" class="text-warning {{ request()->routeIs('services.create') ? 'active' : '' }}">Post Service</a>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            @auth
                <span class="d-none d-md-inline" style="font-size: 0.8rem;">Hello, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-white">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>