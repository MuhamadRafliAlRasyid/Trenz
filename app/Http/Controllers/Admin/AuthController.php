<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Landing page
     */
    public function landing()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.landing');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt(array_merge($credentials, ['role' => 'admin']), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ('auth.failed'),
        ]);
    }

    /**
     * Show registration form for admin
     */
    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    /**
     * Handle registration of admin user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Show the profile settings page
     */
    public function editSettings()
    {
        $user = Auth::user(); // Get the currently logged-in user
        return view('admin.auth.settings', compact('user'));
    }

    /**
     * Update user profile settings (email, password, etc.)
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user(); // Lebih aman

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|in:admin,customer,courier',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // hanya admin yang bisa ubah role
        if ($user->role === 'admin' && $request->filled('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }




    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // Log out the admin
        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate CSRF token to prevent session fixation

        return redirect()->route('admin.login.form'); // Redirect to login form
    }
}
