<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|min:6|confirmed", // ✅ confirmed butuh password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $verificationCode = rand(1000, 9999); // ✅ 4 digit code

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "verification_code" => $verificationCode
        ]);

        // Kirim email verifikasi
        Mail::raw("Your email verification code is: $verificationCode", function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Email Verification Code");
        });

        return response()->json([
            'status' => true,
            'message' => 'Registration successful. Please check your email for the verification code.',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken
            ]
        ]);
    }

    /**
     * Login a user and generate API token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email"    => "required|email",
            "password" => "required|string"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Check user
        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "status"  => false,
                "message" => "Invalid email or password"
            ], 401);
        }

        // Check verification
        if (!$user->email_verified_at) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in.'
            ], 403);
        }

        // Create token
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Login successful",
            "data" => [
                "user" => $user,
                "token" => $token
            ]
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
