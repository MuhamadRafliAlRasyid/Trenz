<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function index()
    {
        // Ambil transaksi dengan status 'shipped'
        $transactions = Transaction::where('status', 'shipped')
            ->with('delivery') // ambil relasi dengan tabel deliveries
            ->get();

        return view('admin.shipments.index', compact('transactions'));
    }
}
