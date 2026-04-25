@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white fw-bold">Reports & Analytics</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light rounded-pill">Back to Dashboard</a>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Revenue Trends (Last 6 Months)</h5>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Booking Status</h5>
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Top Performing Providers</h5>
                    <canvas id="providersChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Most Requested Services</h5>
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Render Revenue Chart
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueLabels) !!},
                datasets: [{
                    label: 'Total Revenue (৳)',
                    data: {!! json_encode($revenueValues) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });

        // 2. Render Booking Status Chart
        const bookCtx = document.getElementById('bookingChart').getContext('2d');
        const bookingStats = {!! json_encode($bookingStats) !!};
        new Chart(bookCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(bookingStats),
                datasets: [{
                    data: Object.values(bookingStats),
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d']
                }]
            },
            options: { responsive: true }
        });
        // --- 3. Render Top Providers Chart ---
        // Safely extract the names and counts from the PHP collection
        const providerLabels = {!! json_encode($topProviders->map(function($item) { return $item->provider->name ?? 'Unknown'; })) !!};
        const providerData = {!! json_encode($topProviders->pluck('total_completed')) !!};

        const provCtx = document.getElementById('providersChart').getContext('2d');
        new Chart(provCtx, {
            type: 'bar',
            data: {
                labels: providerLabels,
                datasets: [{
                    label: 'Completed Jobs',
                    data: providerData,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)', // Nice bootstrap blue
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 5 // Gives the bars slightly rounded tops
                }]
            },
            options: { 
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // --- 4. Render Service Comparisons Chart ---
        // Safely extract the service titles and counts from the PHP collection
        const serviceLabels = {!! json_encode($serviceStats->map(function($item) { return $item->service->title ?? 'Unknown'; })) !!};
        const serviceData = {!! json_encode($serviceStats->pluck('total_requests')) !!};

        const servCtx = document.getElementById('servicesChart').getContext('2d');
        new Chart(servCtx, {
            type: 'bar', // A horizontal bar chart looks great for long service names!
            data: {
                labels: serviceLabels,
                datasets: [{
                    label: 'Total Requests',
                    data: serviceData,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)', // Nice bootstrap warning yellow
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: { 
                responsive: true,
                indexAxis: 'y', // This magical line flips the bar chart horizontally!
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
</script>
@endsection