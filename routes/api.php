<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/services', [ServiceController::class, 'store']);
Route::get('/services', [ServiceController::class, 'index']);

Route::get('/admin/users', [AdminController::class, 'getUsers']);

Route::middleware('throttle:matching')->post('/providers/match', [ProviderController::class, 'match']);
Route::middleware(['auth:sanctum', 'throttle:matching'])->group(function () {
    Route::post('/providers/recommend', [ProviderController::class, 'recommend']);
    Route::post('/user/preferences', [ProviderController::class, 'preferences']);
    Route::put('/user/preferences', [ProviderController::class, 'preferences']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
