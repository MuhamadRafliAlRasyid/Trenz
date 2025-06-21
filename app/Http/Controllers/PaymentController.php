<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    /**
     * Generate Snap Token dari Midtrans
     */
    public function getSnapToken(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer|exists:transactions,id',
        ]);

        $transaction = Transaction::with('user')->findOrFail($request->transaction_id);

        if (!$transaction->total_price || $transaction->total_price <= 0) {
            return response()->json(['message' => 'Invalid total amount.'], 422);
        }

        $orderId = 'TRENZ-' . $transaction->id . '-' . strtoupper(Str::random(5));
        $transaction->order_id = $orderId;
        $transaction->save();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name ?? 'Customer',
                'email' => $transaction->user->email ?? 'noemail@example.com',
            ],
            'callbacks' => [
                'callbacks' => [
                    'finish' => 'https://2c38-36-70-25-23.ngrok-free.app/api/home',
                ],

            ],
            'notification_url' => 'https://2c38-36-70-25-23.ngrok-free.app/api/midtrans/notification'
        ];

        try {
            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            Log::debug('Sending Snap request:', $params);
            $snapToken = Snap::getSnapToken($params);

            if (!$snapToken) {
                return response()->json(['message' => 'Failed to receive Snap token.'], 500);
            }

            // Simpan data token ke payment
            $payment = $transaction->payment ?? $transaction->payments()->create([
                'transaction_id' => $transaction->id,
            ]);

            $payment->method = 'midtrans';
            $payment->snap_token = $snapToken;
            $payment->status = 'pending'; // jangan langsung paid
            $payment->save();

            Log::debug('Generated Snap Token:', ['token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error saat generate Snap token.'], 500);
        }
    }

    /**
     * Handle Midtrans Notification
     */
    public function handleNotification(Request $request)
    {
        Log::info('Midtrans Notification Received', [
            'json' => $request->all()
        ]);

        // Ambil data notifikasi
        $data = $request->all();

        // Parsing data penting
        $orderId = $data['order_id'] ?? null;
        $transactionStatus = $data['transaction_status'] ?? null;

        // Ambil ID transaksi dari order_id
        $parts = explode('-', $orderId);
        $id = $parts[1] ?? null;

        if (!$id || !is_numeric($id)) {
            return response()->json(['message' => 'Invalid order_id'], 400);
        }

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update status transaksi
        if ($transactionStatus === 'settlement') {
            $transaction->status = 'paid';
        } elseif ($transactionStatus === 'pending') {
            $transaction->status = 'pending';
        } elseif (in_array($transactionStatus, ['cancel', 'expire'])) {
            $transaction->status = 'failed';
        } else {
            $transaction->status = $transactionStatus;
        }

        $transaction->save();

        // Update juga ke table `payment` jika ada
        $payment = $transaction->payment ?? $transaction->payments()->latest()->first();
        if ($payment) {
            $payment->status = $transaction->status;
            $payment->save();
        }

        Log::info("Midtrans notification processed successfully for OrderID: {$orderId}");

        return response()->json(['message' => 'Notification handled'], 200);
    }



    public function notificationHandler(Request $request)
    {
        Log::info('Raw Notification Handler Called');
        Log::info($request->all());
        return response()->json(['message' => 'OK']);
    }

    public function receive(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('order_id', $request->order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if (in_array($request->transaction_status, ['settlement', 'capture'])) {
            $transaction->status = 'paid';
        } elseif ($request->transaction_status === 'expire') {
            $transaction->status = 'expired';
        } elseif ($request->transaction_status === 'cancel') {
            $transaction->status = 'cancelled';
        } else {
            $transaction->status = $request->transaction_status;
        }

        $transaction->save();

        return response()->json(['message' => 'Callback received and processed']);
    }
}
