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
            margin: 0; padding: 0; min-height: 100vh; 
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('/images/bg-1.png') no-repeat center center fixed;
            background-size: cover; font-family: 'Inter', sans-serif; color: white;
            display: flex; flex-direction: column;
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

        .brand-logo span { color: #feb83e; }

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
            cursor: pointer;
        }

        .nav-pill-menu a:hover, .nav-pill-menu a.active {
            background: rgba(255, 255, 255, 0.2);
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

        .main-content { flex-grow: 1; display: flex; flex-direction: column; }
    </style>
    @yield('styles')
</head>
<body>

    <nav class="custom-navbar">
        <a href="{{ route('dashboard') }}" class="brand-logo">Task<span>Hive</span></a>
        
        <div class="nav-pill-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
            <a href="{{ route('services.categories') }}" class="{{ request()->routeIs('services.categories') ? 'active' : '' }}">Categories</a>
            
            @auth
                @if(Auth::user()->role === 'provider')
                    {{-- PROVIDER VIEW --}}
                    <a href="{{ route('provider.show', Auth::id()) }}" class="{{ request()->routeIs('provider.show') ? 'active' : '' }}">Profile</a>
                    <a href="{{ route('services.create') }}" class="text-warning {{ request()->routeIs('services.create') ? 'active' : '' }}">Post Service</a>
                @else
                    {{-- CUSTOMER VIEW --}}
                    <a href="{{ route('customer.profile') }}" class="{{ request()->routeIs('customer.profile') ? 'active' : '' }}">History</a>
                    <a onclick="findProvidersNearMe()">Providers</a>
                @endif
            @else
                {{-- GUEST VIEW (Not logged in) --}}
                <a href="{{ route('login') }}">Profile</a>
                <a onclick="findProvidersNearMe()">Providers</a>
            @endauth
        </div>

        <div class="d-flex align-items-center gap-3">
            @auth
                @if(Auth::user()->role === 'admin')
                    @php
                        $recentComplaints = \App\Models\Complaint::with('user')->latest()->take(5)->get();
                    @endphp
                    <div class="dropdown">
                      <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Complaints <span class="badge bg-danger">{{ $recentComplaints->count() }}</span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                        @forelse($recentComplaints as $c)
                          @php
                            $providerName = '—';
                            $customerName = $c->user->name ?? '—';
                            if ($c->target_type === 'provider') {
                                $provider = \App\Models\User::find($c->target_id);
                                $providerName = $provider?->name ?? '—';
                            } elseif ($c->target_type === 'booking') {
                                $booking = \App\Models\Tracking::with('provider')->find($c->target_id);
                                $providerName = $booking?->provider?->name ?? '—';
                            } else {
                                $providerName = ucfirst($c->target_type);
                            }
                          @endphp
                          <li class="dropdown-item">
                            <a href="{{ route('admin.complaint.show', $c->id) }}" class="text-decoration-none">
                              <strong>{{ $providerName }}</strong><br/>
                              <small>{{ $customerName }}</small>
                            </a>
                          </li>
                          <li><hr class="dropdown-divider"></li>
                        @empty
                          <li class="dropdown-item">No complaints</li>
                        @endforelse
                        <li><a class="dropdown-item text-center" href="{{ route('admin.complaints') }}">View all</a></li>
                      </ul>
                    </div>
                @endif
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

    {{-- HIDDEN FORM FOR GEOLOCATION --}}
    <form id="locationSearchForm" action="{{ route('providers.search') }}" method="GET" style="display: none;">
        <input type="hidden" name="lat" id="customer_lat">
        <input type="hidden" name="lng" id="customer_lng">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function findProvidersNearMe() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('customer_lat').value = position.coords.latitude;
                document.getElementById('customer_lng').value = position.coords.longitude;
                document.getElementById('locationSearchForm').submit();
            }, function(error) {
                alert('We need your location to find providers near you.');
            });
        } else {
            alert("Your browser does not support geolocation.");
        }
    }
    </script>
</body>
</html>