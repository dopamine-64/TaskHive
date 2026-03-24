<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Root route redirects to the combined auth page
Route::get('/', [AuthController::class, 'showAuth']);

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Both GET routes now point to the same interactive combined view
    Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
    Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
    
    // POST routes still handle the actual form submissions separately
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    
    // The Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Placeholder for Viewing Services (Module 1)
    Route::get('/services', function () {
        return "<h1>Browse Services Page</h1><p>We will build this next!</p><a href='".route('dashboard')."'>Back to Dashboard</a>";
    })->name('services.index');

    // Placeholder for Posting a Service (Module 1)
    Route::get('/services/create', function () {
        return "<h1>Post a New Service</h1><p>We will build the form here!</p><a href='".route('dashboard')."'>Back to Dashboard</a>";
    })->name('services.create');

});