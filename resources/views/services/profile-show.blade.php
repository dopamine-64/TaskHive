@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9)), 
                    url('/images/bg-1.png') no-repeat center center fixed;
        background-size: cover;
        color: white;
        font-family: 'Poppins', sans-serif;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .table-dark {
        background: transparent !important;
    }

    .badge-status {
        background-color: #79d7ed; 
        color: #000;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
    }

    /* Action Button Styles */
    .btn-accept { background-color: #28a745; color: white; border: none; border-radius: 8px; padding: 5px 15px; transition: 0.3s; }
    .btn-accept:hover { background-color: #218838; }

    .btn-decline { background-color: #dc3545; color: white; border: none; border-radius: 8px; padding: 5px 15px; transition: 0.3s; }
    .btn-decline:hover { background-color: #c82333; }

    .btn-live {
        border: 1px solid #79d7ed;
        color: #79d7ed;
        border-radius: 8px;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-live:hover {
        background: #79d7ed;
        color: #000;
    }

    .btn-finish {
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 5px 15px;
        transition: 0.3s;
    }

    .edit-profile-btn {
        border: 1px solid rgba(255, 255, 255, 0.5);
        color: white;
        border-radius: 50px;
        padding: 8px 25px;
        text-decoration: none;
        transition: 0.3s;
    }

    .edit-profile-btn:hover {
        background: white;
        color: black;
    }
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card glass-card text-white p-4">
                <h2 class="fw-bold m-0">{{ $provider->name ?? 'Provider' }}</h2>
                <p class="text-success mt-1 mb-3">Verified Provider</p>
                <hr class="border-secondary mb-4">
                
                <p class="text-white-50">
                    {{ $provider->bio ?? 'I am a smart mistru' }}
                </p>
                
                <div class="mt-5">
                    <a href="{{ route('profile.edit') }}" class="edit-profile-btn">
                        <i class="fas fa-edit me-2"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card glass-card text-white p-4 mb-4">
                <h3 class="text-warning fw-bold mb-3">Incoming Service Requests</h3>
                <div class="table-responsive">
                    <table class="table table-dark m-0">
                        <tbody>
                            @forelse($incomingRequests as $request)
                                <tr class="border-secondary align-middle">
                                    <td>
                                        <span class="text-white fw-bold">{{ $request->customer_name }}</span> 
                                        <span class="text-white-50">is requesting your service.</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('tracking.accept', $request->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-accept btn-sm">Accept</button>
                                            </form>
                                            <form action="{{ route('tracking.decline', $request->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-decline btn-sm">Decline</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-white-50 py-2">No new requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card glass-card text-white p-4">
                <h3 class="text-success fw-bold mb-4">Working Progress</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover m-0">
                        <thead>
                            <tr class="text-white-50 border-secondary">
                                <th class="fw-normal">Customer</th>
                                <th class="fw-normal">Status</th>
                                <th class="fw-normal">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeJobs as $job)
                                <tr class="border-secondary align-middle">
                                    <td>{{ $job->customer_name }}</td>
                                    <td>
                                        <span class="badge badge-status">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('tracking.live', $job->id) }}" class="btn btn-live btn-sm">
                                                Live Map
                                            </a>
                                            
                                            <form action="{{ route('tracking.complete', $job->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-finish btn-sm">
                                                    Finish Job
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-white-50 py-4">No active jobs in progress.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection