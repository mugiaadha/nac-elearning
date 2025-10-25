<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SiteSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Site Settings API Routes
Route::prefix('site-settings')->group(function () {
    Route::get('/', [SiteSettingController::class, 'index']);
    Route::get('/clear', [SiteSettingController::class, 'clearAllCache']);
    // Route::get('/clear-cache', [SiteSettingController::class, 'clearSiteSettingsCache']);
});

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/session-login', [AuthController::class, 'sessionLogin']);

Route::middleware('recaptcha')->group(function () {
    // Send Feedback Form Endpoint
    Route::post('/send-feedback', [SiteSettingController::class, 'sendContactEmail']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Upload bukti pembayaran
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/upload-payment-proof', [PaymentController::class, 'uploadProof']);
});
