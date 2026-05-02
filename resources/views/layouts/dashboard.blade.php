<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - TaskHive')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0; padding: 0; min-height: 100vh; 
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('/images/bg-1.png') no-repeat center center fixed;
            background-size: cover; font-family: 'Inter', sans-serif; color: white;
            display: flex; flex-direction: column;
        }

        /* Custom navbar for dashboard (three‑dot menu) */
        .dashboard-navbar {
            padding: 1.5rem 6rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .brand-logo {
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: white;
            text-decoration: none;
        }

        .brand-logo span { color: #feb83e; }

        /* Three‑dot menu button */
        .menubar-btn {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 40px;
            padding: 0.5rem 1rem;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .menubar-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #feb83e;
        }

        /* Dropdown menu custom styling */
        .dropdown-menu-custom {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 0.5rem 0;
            min-width: 240px;
            margin-top: 10px;
        }

        .dropdown-menu-custom .dropdown-header {
            color: #feb83e;
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 0.5rem 1.2rem;
        }

        .dropdown-menu-custom .dropdown-item {
            color: white;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .dropdown-menu-custom .dropdown-item i {
            width: 1.5rem;
            margin-right: 0.5rem;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background: rgba(254, 184, 62, 0.2);
            color: #feb83e;
        }

        .dropdown-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Logout button */
        .btn-white {
            background-color: white;
            color: black;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-white:hover {
            background-color: #feb83e;
            color: black;
        }

        .main-content { flex-grow: 1; display: flex; flex-direction: column; padding: 2rem 0; }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-navbar { padding: 1rem 2rem; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <nav class="dashboard-navbar">
        <!-- Logo with role badge -->
        <div>
            <a href="{{ route('dashboard') }}" class="brand-logo">Task<span>Hive</span></a>
            @auth
                <span class="role-badge">{{ ucfirst(Auth::user()->role) }}</span>
            @endauth
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-md-inline" style="font-size: 0.9rem;">Hello, {{ Auth::user()->name }}</span>

            <!-- Three‑dot dropdown menu -->
            <div class="dropdown">
                <button class="menubar-btn" type="button" id="adminMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom" aria-labelledby="adminMenuDropdown">
                    <li><h6 class="dropdown-header">MANAGEMENT</h6></li>
                    <li><a class="dropdown-item" href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.providers') }}"><i class="fas fa-user-tie"></i> Manage Providers</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.bookings') }}"><i class="fas fa-calendar-alt"></i> Manage Bookings</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.services') }}"><i class="fas fa-check-circle"></i> Manage Services</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header">SYSTEM</h6></li>
                    <li><a class="dropdown-item" href="{{ route('admin.activities') }}"><i class="fas fa-history"></i> System Activities</a></li>
                </ul>
            </div>

            <!-- Logout form -->
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-white">Logout</button>
            </form>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    @yield('scripts')
</body>
</html>