<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProviderProfileController;
use App\Http\Controllers\RatingController;

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
    
    // View all services
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    // Show the form to create a new service
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    
    // Save the new service to the database (This handles the form submission)
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

    // Provider Profile Routes
    Route::get('/provider/{userId}', [ProviderProfileController::class, 'show'])->name('provider.show');
    Route::get('/profile/edit', [ProviderProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/dashboard', [ProviderProfileController::class, 'dashboard'])->name('profile.dashboard');

    // Rating Routes
    Route::post('/provider/{providerId}/rate', [RatingController::class, 'store'])->name('rating.store');
    Route::delete('/rating/{rating}', [RatingController::class, 'destroy'])->name('rating.destroy');
});
