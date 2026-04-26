@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white m-0">
            <i class="fas fa-wallet text-warning me-2"></i>My Wallet & Points
        </h2>
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
            Back to Dashboard
        </a>
    </div>

    <!-- Combined Balance & Points Card -->
    <div class="card mb-4 border-0 overflow-hidden" style="background: linear-gradient(135deg, #1a1a2e, #16213e); border-radius: 24px;">
        <div class="card-body p-0">
            <div class="row g-0">
                <!-- Wallet Balance Section -->
                <div class="col-md-6 p-4 text-center border-end border-white-10">
                    <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                    <p class="text-white-50 mb-1">Wallet Balance</p>
                    <h2 class="text-info mb-0 fw-bold">
                        ৳ {{ number_format($currentBalance ?? 0, 2) }}
                    </h2>
                    <p class="text-white-50 mt-2 small">Available for payments</p>
                </div>
                <!-- Reward Points Section -->
                <div class="col-md-6 p-4 text-center">
                    <i class="fas fa-star fa-2x text-warning mb-2"></i>
                    <p class="text-white-50 mb-1">Reward Points</p>
                    <h2 class="text-warning mb-0 fw-bold">
                        {{ number_format(auth()->user()->reward_points ?? 0) }}
                    </h2>
                    <p class="text-white-50 mt-2 small">10 points = 1 Tk discount on next booking</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Quick links to use points -->
    <div class="text-center mb-4">
        <a href="{{ route('services.categories') }}" class="btn btn-sm btn-outline-warning rounded-pill px-4">
            <i class="fas fa-shopping-cart me-1"></i> Book a Service
        </a>
    </div>

    <!-- Transaction History -->
    <div class="card border-0" style="background: rgba(255,255,255,0.05); border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <h5 class="text-warning mb-0"><i class="fas fa-history me-2"></i>Transaction History</h5>
        </div>
        <div class="card-body">
            @if(isset($transactions) && $transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table text-white">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-end">Amount (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-end {{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->amount > 0 ? '+' : '' }}৳ {{ number_format(abs($transaction->amount), 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            @else
                <p class="text-center text-white-50 py-4">No transactions yet</p>
            @endif
        </div>
    </div>
</div>
@endsection