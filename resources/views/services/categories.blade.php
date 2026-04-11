<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskHive | Available Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                        url('/images/bg-1.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 50px;
        }
        
        .hero-section {
            text-align: center;
            padding: 60px 0 40px;
            color: white;
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
</head>
<body>

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title"><i class="fas fa-list"></i> Available Services</h1>
        <p>Browse all services from our trusted providers</p>
    </div>
</div>

<div class="container text-center">
    <div class="results-pill">
        <i class="fas fa-chart-line"></i> {{ $services->count() }} Services Available
    </div>

    <div class="row g-4">
        @forelse($services as $service)
        <div class="col-md-4">
            <div class="service-card">
                <h5>{{ $service->title }}</h5>
                <p class="text-white-50 small">{{ Str::limit($service->description, 80) }}</p>
                <div class="price">${{ number_format($service->price) }}</div>
                <span class="badge-category">{{ $service->category }}</span>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h4>No services available</h4>
                <p>Please check back later</p>
                <a href="{{ url('/dashboard') }}" style="background: #ffd700; color: #000; padding: 10px 30px; border-radius: 50px; text-decoration: none; display: inline-block; margin-top: 20px;">Back to Dashboard</a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
    </div>

    <div class="back-link">
        <a href="{{ url('/dashboard') }}">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>