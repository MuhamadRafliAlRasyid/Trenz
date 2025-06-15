<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * Register a new customer
     */
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
            'role' => 'customer',  // Role for customer
            'verification_code' => $verificationCode,
        ]);

        // Send verification code to user's email
        Mail::raw("Your email verification code is: $verificationCode", function ($message) use ($user) {
            $message->to($user->email)->subject("Email Verification Code");
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

    /**
     * Login for customer
     */
    public function loginCustomer(Request $request)
    {
        Log::info('Login attempt', $request->all());
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'customer']))) {
            Log::info('Login success');
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Customer login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);
        }
        Log::warning('Login failed');
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // Fetch authenticated user

        // Validation for the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed', // optional password
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Directly update fields
        $user->name = $request->name;

        // Update password if provided
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Delete the old image if exists
            if ($user->image) {
                Storage::delete($user->image);
            }

            // Store the new image
            $imagePath = $request->file('image')->store('profile_images', 'public');
            $user->image = $imagePath;
        }

        // Save changes to the user model explicitly
        try {
            $user->save(); // Ensure the model gets updated and saved
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile. ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Account settings updated successfully',
            'data' => [
                'name' => $user->name,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
            ],
        ]);
    }

    /**
     * Register a new admin
     */
    public function registerAdmin(Request $request)
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',  // Role for admin
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin registered successfully.',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ],
        ]);
    }

    /**
     * Login for admin
     */
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'admin']))) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Admin login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Logout (Invalidate current token)
     */
    public function logout(Request $request)
    {
        // Revoke the token for the logged-in user
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        // Remove user and token from local storage (frontend)
        return response()->json(['message' => 'Logged out successfully']);
    }
    /**
     * Refresh Token (generate new token)
     */
    public function refreshToken(Request $request)
    {
        // Delete existing tokens
        $request->user()->tokens()->delete();

        // Generate new token
        $newToken = $request->user()->createToken("auth_token")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Token refreshed successfully",
            "access_token" => $newToken
        ]);
    }

    /**
     * Email Verification
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string'
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        // Check if the email is already verified
        if ($user->email_verified_at) {
            return response()->json(['status' => true, 'message' => 'Email already verified']);
        }

        // Verify the code and update email_verified_at
        if ($user->verification_code === $request->code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return response()->json(['status' => true, 'message' => 'Email verified successfully']);
        }

        return response()->json(['status' => false, 'message' => 'Invalid verification code'], 400);
    }

    /**
     * Resend Verification Email
     */
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return response()->json(['status' => true, 'message' => 'Email already verified.']);
        }

        // Generate a new verification code
        $verificationCode = rand(1000, 9999);
        $user->verification_code = $verificationCode;
        $user->save();

        // Send the verification code via email
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
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'User profile fetched successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->image
                    ? asset('storage/' . $user->image)
                    : null,
            ],
        ]);
    }
}
