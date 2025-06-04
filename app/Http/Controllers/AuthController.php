<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    // Register Customer
    public function registerCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $verificationCode = rand(1000, 9999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'verification_code' => $verificationCode,
        ]);

        Mail::raw("Your email verification code is: $verificationCode", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Email Verification Code");
        });

        return response()->json([
            'status' => true,
            'message' => 'Customer registered. Check your email for verification code.',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ],
        ]);
    }

    // Register Courier
    public function registerCourier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            // Kamu bisa tambahkan validasi tambahan untuk courier disini
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $verificationCode = rand(1000, 9999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'courier',
            'verification_code' => $verificationCode,
        ]);

        Mail::raw("Your email verification code is: $verificationCode", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Email Verification Code");
        });

        return response()->json([
            'status' => true,
            'message' => 'Courier registered. Check your email for verification code.',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ],
        ]);
    }
    /**
     * Logout (Invalidate current token)
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }

    /**
     * Refresh Token (generate new token)
     */
    public function refreshToken(Request $request)
    {
        $request->user()->tokens()->delete();

        $newToken = $request->user()->createToken("auth_token")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Token refreshed successfully",
            "access_token" => $newToken
        ]);
    }
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['status' => true, 'message' => 'Email already verified']);
        }

        if ($user->verification_code === $request->code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return response()->json(['status' => true, 'message' => 'Email verified successfully']);
        }

        return response()->json(['status' => false, 'message' => 'Invalid verification code'], 400);
    }
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return response()->json(['status' => true, 'message' => 'Email already verified.']);
        }

        $verificationCode = rand(1000, 9999);
        $user->verification_code = $verificationCode;
        $user->save();

        Mail::raw("Your email verification code is: $verificationCode", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Resend Email Verification Code");
        });

        return response()->json(['status' => true, 'message' => 'Verification code resent.']);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            "status" => true,
            "message" => "User profile fetched successfully",
            "data" => $request->user()
        ]);
    }
}
