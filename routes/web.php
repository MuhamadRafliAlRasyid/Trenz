<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailsController;

// Lindungi dengan Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', AddressController::class);
    Route::apiResource('cart-items', CartItemController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('deliveries', DeliveryController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('transaction-details', TransactionDetailsController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Public Route
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
