<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $totalProducts = Product::count();
            $totalOrders = Transaction::count();
            $totalUsers = User::count();

            $monthlyRevenue = Transaction::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_price');

            // Jika request dari API (Accept: application/json), return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'totalProducts' => $totalProducts,
                    'totalOrders' => $totalOrders,
                    'totalUsers' => $totalUsers,
                    'monthlyRevenue' => $monthlyRevenue
                ]);
            }

            // Kalau bukan, return Blade view
            return view('admin.dashboard', compact(
                'totalProducts',
                'totalOrders',
                'totalUsers',
                'monthlyRevenue'
            ));
        } catch (\Exception $e) {
            // Kembalikan error JSON jika API, atau tampilkan error jika Blade
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan',
                    'error' => $e->getMessage()
                ], 500);
            }

            return abort(500, $e->getMessage());
        }
    }
}
