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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::get('/user/ban/{id}', [AdminController::class, 'banUser'])->name('admin.user.ban');
    Route::delete('/user/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
    
    // Provider Management
    Route::get('/providers', [AdminController::class, 'manageProviders'])->name('admin.providers');
    Route::get('/provider/verify/{id}', [AdminController::class, 'verifyProvider'])->name('admin.provider.verify');
    Route::delete('/provider/delete/{id}', [AdminController::class, 'deleteProvider'])->name('admin.provider.delete');
    
    // Booking Management
    Route::get('/bookings', [AdminController::class, 'manageBookings'])->name('admin.bookings');
    Route::put('/booking/update/{id}', [AdminController::class, 'updateBookingStatus'])->name('admin.booking.update');
    Route::delete('/booking/delete/{id}', [AdminController::class, 'deleteBooking'])->name('admin.booking.delete');
    
    // Service Management
    Route::get('/services', [AdminController::class, 'manageServices'])->name('admin.services');
    Route::get('/service/toggle/{id}', [AdminController::class, 'toggleServiceStatus'])->name('admin.service.toggle');
    Route::get('/service/edit/{id}', [AdminController::class, 'editService'])->name('admin.service.edit');
    Route::put('/service/update/{id}', [AdminController::class, 'updateService'])->name('admin.service.update');
    Route::delete('/service/delete/{id}', [AdminController::class, 'deleteService'])->name('admin.service.delete');
    
    // System Activities
    Route::get('/activities', [AdminController::class, 'activities'])->name('admin.activities');
});

// ==================== PUBLIC ROUTES ====================
Route::get('/', [AuthController::class, 'showAuth']);

// ==================== GUEST ONLY ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
    Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// ==================== PAYMENT CALLBACK ROUTES (PUBLIC) ====================
Route::post('/pay/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/pay/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::post('/pay/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

// ==================== AUTHENTICATED ROUTES ====================
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
    
    // Booking Routes (Customer)
    Route::get('/service/{id}/book', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}/reschedule', [BookingController::class, 'rescheduleForm'])->name('booking.reschedule.form');
    Route::put('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::delete('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    
    // Payment Initiation & Invoice
    Route::get('/pay/{booking_id}', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/invoice/{id}', [PaymentController::class, 'invoice'])->name('invoice.show');
    
    // Profile Management
    Route::get('/profile/edit', [ProviderProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/dashboard', [ProviderProfileController::class, 'dashboard'])->name('profile.dashboard');
    
    // Ratings & Reviews
    Route::post('/provider/{providerId}/rate', [RatingController::class, 'store'])->name('rating.store');
    Route::delete('/rating/{rating}', [RatingController::class, 'destroy'])->name('rating.destroy');
    
    // Tracking & Booking Flow (Provider side)
    Route::post('/tracking/initiate/{providerId}', [TrackingController::class, 'initiateTracking'])->name('tracking.initiate');
    Route::get('/tracking/live/{id}', [TrackingController::class, 'liveTracking'])->name('tracking.live');
    Route::post('/tracking/accept/{id}', [TrackingController::class, 'accept'])->name('tracking.accept');
    Route::post('/tracking/decline/{id}', [TrackingController::class, 'decline'])->name('tracking.decline');
    Route::post('/tracking/complete/{id}', [TrackingController::class, 'complete'])->name('tracking.complete');
    
    // Customer Profile
    Route::get('/my-profile', [TrackingController::class, 'customerProfile'])->name('customer.profile');
    Route::get('/my-account', [TrackingController::class, 'customerProfile']); // Alias
});