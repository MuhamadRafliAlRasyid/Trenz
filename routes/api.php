<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// Public API
Route::get('/products', [ProductController::class, 'index']);       // List produk
Route::get('/products/{id}', [ProductController::class, 'show']);   // Detail produk
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/courier', [AuthController::class, 'registerCourier']);
Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/verify-email', [AuthController::class, 'verify']);
Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->middleware('auth:sanctum');

// Authenticated user (customer/courier)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
});

// Admin-only API (via middleware role:admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
