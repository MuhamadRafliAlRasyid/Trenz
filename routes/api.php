<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('products', ProductController::class);
Route::post('/verify-email', [AuthController::class, 'verify']);
Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->middleware('auth:sanctum');

// Tautan dari email
Route::post('/verify-email', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'code' => 'required|string'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ], 404);
    }

    if ($user->email_verified_at) {
        return response()->json([
            'status' => true,
            'message' => 'Email already verified'
        ]);
    }

    if (trim($user->verification_code) === trim($request->code)) {
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    return response()->json([
        'status' => false,
        'message' => 'Invalid verification code'
    ], 400);
});



// Kirim ulang email verifikasi
Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification email resent.']);
})->middleware(['auth:sanctum']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
});
