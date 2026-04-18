<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProviderProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TrackingController; 
use App\Http\Controllers\BookingController;
// --- PUBLIC ROUTES ---
Route::get('/', [AuthController::class, 'showAuth']);

// --- GUEST ONLY ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
    Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Services Management
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/categories', [ServiceController::class, 'categories'])->name('services.categories');

    // Provider Search & Discovery
    Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
    Route::get('/providers/search', [ProviderController::class, 'index'])->name('providers.search');
    
    Route::get('/provider/{userId}', [ProviderProfileController::class, 'show'])->name('provider.show');
    // ========== AYESHA'S BOOKING ROUTES (Module 2) ==========
    Route::get('/service/{id}/book', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}/reschedule', [BookingController::class, 'rescheduleForm'])->name('booking.reschedule.form');
    Route::put('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::delete('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    // ========================================================
    // Profile Management
    Route::get('/profile/edit', [ProviderProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/dashboard', [ProviderProfileController::class, 'dashboard'])->name('profile.dashboard');

    // Ratings & Reviews
    Route::post('/provider/{providerId}/rate', [RatingController::class, 'store'])->name('rating.store');
    Route::delete('/rating/{rating}', [RatingController::class, 'destroy'])->name('rating.destroy');

    // --- TRACKING & BOOKING FLOW ---
    Route::post('/tracking/initiate/{providerId}', [TrackingController::class, 'initiateTracking'])->name('tracking.initiate');
    Route::get('/tracking/live/{id}', [TrackingController::class, 'liveTracking'])->name('tracking.live');
    Route::post('/tracking/accept/{id}', [TrackingController::class, 'accept'])->name('tracking.accept');
    Route::post('/tracking/decline/{id}', [TrackingController::class, 'decline'])->name('tracking.decline');
    Route::post('/tracking/complete/{id}', [TrackingController::class, 'complete'])->name('tracking.complete');
    // Inside the auth middleware group
    Route::get('/my-profile', [TrackingController::class, 'customerProfile'])->name('customer.profile');
Route::get('/my-account', [TrackingController::class, 'customerProfile'])->name('customer.profile');
});
