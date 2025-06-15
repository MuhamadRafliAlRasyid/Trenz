<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MidtransCallbackController extends Controller
{
    public function callback(Request $request)
    {
        foreach (config('services.midtrans') as $key => $value) {
            Config::${$key} = $value;
        }

        // Ambil notifikasi dari Midtrans
        $notification = new Notification();

        Log::info('Midtrans Notification received:', (array) $notification);

        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;
        $midtransTransactionId = $notification->transaction_id;

        // Extract ID transaksi kita
        $parts = explode('-', $orderId); // Contoh: "ORDER-123" => ['ORDER', '123']
        $transactionId = $parts[1] ?? null;

        if (!$transactionId) {
            Log::error("Invalid Order ID: $orderId");
            return response()->json(['message' => 'Invalid order ID'], 400);
        }

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            Log::error("Transaction with ID $transactionId not found");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update status berdasarkan notifikasi
        if ($transactionStatus === 'settlement') {
            $transaction->status = 'paid';
        } elseif ($transactionStatus === 'expire') {
            $transaction->status = 'expired';
        } elseif ($transactionStatus === 'cancel') {
            $transaction->status = 'cancelled';
        }

        // Update data lain
        $transaction->payment_type = $paymentType;
        $transaction->midtrans_transaction_id = $midtransTransactionId;
        $transaction->save();

        Log::info("Transaction $transactionId updated to {$transaction->status}");

        return response()->json(['message' => 'Notification handled'], 200);
    }
}
