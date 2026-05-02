@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-body p-5 text-center">
                    
                    <i class="fas fa-envelope fa-4x text-primary mb-3"></i>
                    <h3 class="fw-bold mb-2">Email Verification</h3>
                    
                    {{-- Error Display --}}
                    @if(session('error'))
                        <div class="alert alert-danger py-2 mb-3 shadow-sm d-flex align-items-center" 
                            style="border-radius: 12px; font-size: 14px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info py-2 mb-3 shadow-sm d-flex align-items-center justify-content-center" 
                            style="border-radius: 12px; font-size: 14px;">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Verifying email:</label>
                        <p class="fw-bold fs-5">{{ $email }}</p>
                    </div>

                    {{-- Main Verification Form --}}
                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="type" value="{{ $type ?? 'register' }}">
                        
                        <div class="mb-3">
                            <input type="text" name="code" class="form-control form-control-lg text-center" 
                                   placeholder="Enter 6-digit code" maxlength="6" 
                                   style="font-size: 24px; letter-spacing: 8px; border-radius: 12px;" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" style="border-radius: 12px;">
                            Verify & Continue
                        </button>
                    </form>
                    
                    {{-- Resend Form --}}
                    <form method="POST" action="{{ route('otp.resend') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="type" value="{{ $type ?? 'register' }}">
                        
                        <button type="submit" class="btn btn-link text-decoration-none small text-muted">
                            Didn't receive code? <strong>Resend OTP</strong>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection