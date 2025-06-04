<?php

use App\Http\Controllers\Admin\{
    DashboardController,
    ReportController,
    UserController,
    AuthController as AdminAuthController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProductController,
    CategoryController,
    TransactionController
};

// Landing page
Route::get('/', [AdminAuthController::class, 'landing'])->name('landing');

// Admin login & register (guest only)
Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/loginadmin', [AdminAuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/loginadmin', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::get('/registeradmin', [AdminAuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/registeradmin', [AdminAuthController::class, 'register'])->name('register.submit');
});

// Admin authenticated routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    // Produk (CRUD via form di admin/products/form.blade.php)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // User management routes
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    // Kategori
    Route::resource('categories', CategoryController::class);

    // Transaksi
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::post('transactions/{id}/validate-payment', [TransactionController::class, 'validatePayment'])->name('transactions.validatePayment');
});
