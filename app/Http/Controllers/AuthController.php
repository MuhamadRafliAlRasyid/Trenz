<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            "password" => "required|string|min:6|confirmed", // uses password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            "name"     => $request->name,
            "email"    => $request->email,
            "password" => bcrypt($request->password)
        ]);

        return response()->json([
            "status"  => true,
            "message" => "User registered successfully",
            "data"    => $user
        ], 201);
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
