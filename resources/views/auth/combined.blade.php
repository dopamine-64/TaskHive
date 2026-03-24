@extends('layouts.auth')

@section('title', 'Welcome to TaskHive')

@section('content')
    <div class="auth-container {{ old('name') || $errors->has('name') ? 'right-panel-active' : '' }}" id="authContainer">
        
        <div class="form-container sign-up-container">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h1 style="color: #1670d0;">Create Account</h1>
                
                <input type="text" class="form-control" name="name" placeholder="Full Name" value="{{ old('name') }}" required />
                <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required />
                
                <div class="d-flex gap-2 w-100">
                    <input type="password" class="form-control" name="password" placeholder="Password" required />
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm" required />
                </div>

                <div class="role-selector">
                    <label class="form-check">
                        <input class="form-check-input ms-0" type="radio" name="role" value="user" checked>
                        <span class="d-block mt-1" style="font-size: 0.85rem;">Customer</span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input ms-0" type="radio" name="role" value="provider">
                        <span class="d-block mt-1" style="font-size: 0.85rem;">Provider</span>
                    </label>
                </div>

                <button class="btn-custom mt-3" type="submit">Sign Up</button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1 style="color: #1670d0;">Sign In</h1>
                
                <input type="email" class="form-control" name="email" placeholder="Email" required />
                <input type="password" class="form-control" name="password" placeholder="Password" required />
                
                <div class="d-flex justify-content-between w-100 px-2 mt-2 mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label text-muted" style="font-size: 13px;" for="remember">Remember me</label>
                    </div>
                    <a href="#" class="text-muted text-decoration-none" style="font-size: 13px;">Forgot your password?</a>
                </div>

                <button class="btn-custom" type="submit">Sign In</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Already have an account? Log in to manage your services and bookings.</p>
                    <button class="btn-custom ghost" id="signInBtn">Sign In</button>
                </div>
                
                <div class="overlay-panel overlay-right">
                    <h1 class="display-4 fw-bold">TaskHive</h1>
                    <p>Don't have an account yet? Register as a customer or service provider to get started.</p>
                    <button class="btn-custom ghost" id="signUpBtn">Create Account</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const signUpBtn = document.getElementById('signUpBtn');
        const signInBtn = document.getElementById('signInBtn');
        const authContainer = document.getElementById('authContainer');

        signUpBtn.addEventListener('click', () => {
            authContainer.classList.add("right-panel-active");
        });

        signInBtn.addEventListener('click', () => {
            authContainer.classList.remove("right-panel-active");
        });
    </script>
@endpush