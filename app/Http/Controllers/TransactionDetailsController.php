<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;
use App\Models\TransactionDetails;
use Illuminate\Http\Request;

class TransactionDetailsController extends Controller
{
    // Menyimpan detail transaksi baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'price_per_item' => 'required|numeric',
        ]);

        $transactionDetail = TransactionDetails::create($validatedData);

        return response()->json([
            'message' => 'Detail transaksi berhasil disimpan.',
            'transaction_detail' => $transactionDetail,
        ]);
    }
}
