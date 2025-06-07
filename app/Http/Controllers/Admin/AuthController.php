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

        // Untuk Web (Session-Based Login)
        if (Auth::guard('web')->attempt(array_merge($credentials, ['role' => 'admin']), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Berikan token API kepada admin setelah login via web
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return redirect()->route('admin.dashboard')->with('token', $token);  // Redirect dengan token API
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
        // Pastikan user terautentikasi
        $user = $request->user();

        if ($user) {
            // Menghapus semua token yang terkait dengan pengguna yang sedang aktif
            $user->tokens()->delete();

            // Logout untuk web
            Auth::guard('web')->logout(); // Log out admin via web
            $request->session()->invalidate(); // Menghapus session
            $request->session()->regenerateToken(); // Menghasilkan CSRF token yang baru untuk mencegah session fixation

            // Redirect ke halaman login admin
            return redirect()->route('admin.login.form')->with('status', 'You have been logged out successfully');
        }

        // Jika tidak ada pengguna terautentikasi, redirect ke halaman login
        return redirect()->route('admin.login.form')->with('status', 'User not authenticated or token invalid');
    }
}
