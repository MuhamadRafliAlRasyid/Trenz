<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalProducts = Product::count();
            $totalOrders = Transaction::count();
            $totalUsers = User::count();

            $monthlyRevenue = Transaction::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_price');

            return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalUsers', 'monthlyRevenue'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
