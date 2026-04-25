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

    .meta-label {
        color: rgba(255,255,255,0.65);
        font-size: 0.9rem;
    }

    .meta-value {
        color: #fff;
        font-weight: 600;
    }

    .skill-pill {
        background: rgba(121, 215, 237, 0.2);
        color: #79d7ed;
        border: 1px solid rgba(121, 215, 237, 0.5);
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.82rem;
        display: inline-block;
        margin: 3px;
    }

    .badge-status {
        background-color: #79d7ed;
        color: #000;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
    }

    .btn-accept { background-color: #28a745; color: white; border: none; border-radius: 8px; padding: 5px 15px; }
    .btn-decline { background-color: #dc3545; color: white; border: none; border-radius: 8px; padding: 5px 15px; }
    .btn-finish { background-color: #3b82f6; color: white; border: none; border-radius: 8px; padding: 5px 15px; }
    .btn-live { border: 1px solid #79d7ed; color: #79d7ed; border-radius: 8px; text-decoration: none; }
    .btn-invoice { background-color: #20c997; color: white; border: none; border-radius: 8px; text-decoration: none; padding: 5px 15px; }
    .btn-invoice:hover { color: white; background-color: #1aa179; }
</style>

@php
    $providerUser = $user ?? $provider ?? null;
    $providerProfile = $profile ?? null;
    $profileOwnerId = $providerUser?->id;
    $isProfileOwner = auth()->id() === $profileOwnerId;
    $skills = collect($providerProfile?->skills ?? [])->filter()->values();
    $incomingRequests = collect($incomingRequests ?? []);
    $activeJobs = collect($activeJobs ?? []);
@endphp

<div class="container mt-5">
    <div class="row g-4">
        <div class="col-12">
            <div class="card glass-card text-white p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold m-0">{{ $providerUser?->name ?? 'Provider' }}</h2>
                        <p class="text-success mt-1 mb-2">Verified Provider</p>
                        <div class="small text-warning">
                            ★ {{ number_format((float) ($providerProfile?->average_rating ?? 0), 1) }}
                            ({{ (int) ($providerProfile?->total_ratings ?? (isset($ratings) ? $ratings->total() : 0)) }} ratings)
                        </div>
                    </div>
                    @if($isProfileOwner)
                        <a href="{{ route('profile.edit') }}" class="edit-profile-btn">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card glass-card text-white p-4 h-100">
                <h4 class="text-warning mb-3">About</h4>
                <p class="text-white-50 mb-4">{{ $providerProfile?->bio ?: 'No bio added yet.' }}</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="meta-label">Experience</div>
                        <div class="meta-value">{{ (int) ($providerProfile?->experience_years ?? 0) }} years</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Service Area</div>
                        <div class="meta-value">{{ $providerProfile?->service_area ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Hourly Rate</div>
                        <div class="meta-value">{{ $providerProfile?->hourly_rate ? '৳' . $providerProfile->hourly_rate : 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Fixed Rate</div>
                        <div class="meta-value">{{ $providerProfile?->fixed_rate ? '৳' . $providerProfile->fixed_rate : 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Service Radius</div>
                        <div class="meta-value">{{ $providerProfile?->service_radius_km ? $providerProfile->service_radius_km . ' km' : 'Not specified' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Certifications</div>
                        <div class="meta-value">{{ $providerProfile?->certifications ?: 'Not specified' }}</div>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <h5 class="mb-2">Skills</h5>
                @if($skills->isEmpty())
                    <p class="text-white-50 mb-0">No skills listed.</p>
                @else
                    @foreach($skills as $skill)
                        <span class="skill-pill">{{ $skill }}</span>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card glass-card text-white p-4 h-100">
                <h4 class="text-warning mb-3">Ratings & Reviews</h4>
                @forelse($ratings ?? collect() as $rating)
                    <div class="mb-3 pb-3 border-bottom border-secondary">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $rating->reviewer->name ?? 'Customer' }}</strong>
                            <span class="text-warning">★ {{ number_format((float) $rating->rating, 1) }}</span>
                        </div>
                        <p class="text-white-50 mb-0 mt-1">{{ $rating->review ?: 'No written review.' }}</p>
                    </div>
                @empty
                    <p class="text-white-50 mb-0">No reviews yet.</p>
                @endforelse

                @if(isset($ratings) && method_exists($ratings, 'links'))
                    <div class="mt-2">{{ $ratings->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    @if($isProfileOwner)
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card glass-card text-white p-4 text-center">
                    <h6 class="text-white-50 mb-2">Incoming Requests</h6>
                    <h2 class="mb-0 text-warning">{{ $incomingRequests->count() }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card glass-card text-white p-4 text-center">
                    <h6 class="text-white-50 mb-2">Active Jobs</h6>
                    <h2 class="mb-0 text-success">{{ $activeJobs->count() }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card glass-card text-white p-4 text-center">
                    <h6 class="text-white-50 mb-2">Accepted/In Progress</h6>
                    <h2 class="mb-0 text-info">{{ $activeJobs->count() }}</h2>
                </div>
            </div>

            <div class="col-12">
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
            </div>

            <div class="col-12">
                <div class="card glass-card text-white p-4">
                    <h3 class="text-success fw-bold mb-4">Working Progress</h3>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover m-0">
                            <thead>
                                <tr class="text-white-50 border-secondary">
                                    <th class="fw-normal">Customer</th>
                                    <th class="fw-normal">Job Status</th>
                                    <th class="fw-normal">Payment</th> {{-- Added Payment Header --}}
                                    <th class="fw-normal">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeJobs as $job)
                                    <tr class="border-secondary align-middle">
                                        <td>{{ $job->customer_name }}</td>
                                        <td><span class="badge badge-status">{{ ucfirst($job->status) }}</span></td>
                                        
                                        {{-- Payment Status Badge --}}
                                        <td>
                                            @if($job->payment_status == 'paid')
                                                <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i> PAID</span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">UNPAID</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('tracking.live', $job->id) }}" class="btn btn-live btn-sm d-flex align-items-center">Live Map</a>
                                                
                                                {{-- Invoice Button (Only if Paid) --}}
                                                @if($job->payment_status == 'paid')
                                                    <a href="{{ route('invoice.show', $job->id) }}" class="btn btn-invoice btn-sm d-flex align-items-center">
                                                        <i class="fas fa-file-invoice me-1"></i> Invoice
                                                    </a>
                                                @endif

                                                @if($job->payment_status == 'paid')
                                                    <form action="{{ route('tracking.complete', $job->id) }}" method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-finish btn-sm">Finish Job</button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-50 border border-light-subtle text-light d-flex align-items-center px-3">
                                                        Waiting for payment
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-white-50 py-4">No active jobs in progress.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
