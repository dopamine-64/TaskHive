<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | TaskHive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            margin: 0; padding: 0; min-height: 100vh; 
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('/images/bg-1.png') no-repeat center center fixed;
            background-size: cover; font-family: 'Inter', sans-serif; color: white;
            display: flex; flex-direction: column;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* Keep card styles consistent */
        .card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-5" style="margin-top: 100px;">
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>