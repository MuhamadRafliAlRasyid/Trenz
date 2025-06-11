<?php

use App\Http\Controllers\Admin\{
    DashboardController,
    ReportController,
    UserController,
    AuthController as AdminAuthController
};
use App\Http\Controllers\{
    ProductController,
    CategoryController,
    TransactionController,
    CartItemController,
    AddressController,
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Public API
// Register routes
Route::post('/register/customer', [AuthController::class, 'registerCustomer']);
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);

// Login routes
Route::post('/login/customer', [AuthController::class, 'loginCustomer']);
Route::post('/login/admin', [AuthController::class, 'loginAdmin']);

// Email verification and resend
Route::post('/verify-email', [AuthController::class, 'verify']);
Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail']);

// Logout and refresh token
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/refresh-token', [AuthController::class, 'refreshToken']);
Route::middleware('auth:sanctum')->get('/transactions/status', [TransactionController::class, 'getCategorizedTransactions']);



Route::middleware('auth:sanctum')->group(function () {
    // Product-related routes
    Route::get('/products', [ProductController::class, 'apiIndex']);

    // User profile and authentication

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [UserController::class, 'updateProfile']); // jika pakai _method: PUT
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken']);

    // Cart-related routes
    Route::get('/cart/{userId}', [CartItemController::class, 'index']);
    Route::post('/cart', [CartItemController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartItemController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartItemController::class, 'destroy']);
    // Address-related routes
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::get('/addresses/{id}', [AddressController::class, 'show']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    //transactions-related routes
    // Route untuk menyimpan transaksi baru
    Route::post('/transactions', [TransactionController::class, 'store']);
    // Route untuk mengambil semua transaksi
    // Route untuk mengambil transaksi berdasarkan ID
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);

    // Route untuk memperbarui status transaksi
    Route::put('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);

    // Corrected route for categorized transactions
    Route::get('/transactions/category', [TransactionController::class, 'getCategorizedTransactions']);
});

// Admin-only API (via middleware role:admin)
Route::prefix('admin')->name('admin.')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Settings
    Route::get('settings', [AdminAuthController::class, 'editSettings'])->name('settings');
    Route::put('settings', [AdminAuthController::class, 'updateSettings'])->name('settings.update');

    // Product CRUD API
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // User management API
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    // Category CRUD API
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Transaction API
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::post('transactions/{id}/validate-payment', [TransactionController::class, 'validatePayment'])->name('transactions.validatePayment');
});
