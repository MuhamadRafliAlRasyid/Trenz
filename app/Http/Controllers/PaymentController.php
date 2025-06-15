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
                'finish' => 'https://e616-36-70-31-236.ngrok-free.app/transaction-success'
            ],
            'notification_url' => 'https://e616-36-70-31-236.ngrok-free.app/api/midtrans/notification',

        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            if (!$snapToken) {
                return response()->json(['message' => 'Failed to receive Snap token.'], 500);
            }

            // Simpan data token ke payment (optional)
            $payment = $transaction->payment ?? $transaction->payments()->create();
            $payment->method = 'midtrans';
            $payment->snap_token = $snapToken;
            $payment->status = 'paid';
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
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;
        $status_code = $notif->status_code;

        // Misal: TRENZ-27-XXXX => Ambil id dari order_id
        $parts = explode('-', $order_id);
        $id = $parts[1]; // 27

        $transaksi = Transaction::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction == 'settlement') {
            $transaksi->status = 'paid';
            $transaksi->save();
        } elseif ($transaction == 'pending') {
            $transaksi->status = 'pending';
            $transaksi->save();
        } elseif ($transaction == 'cancel' || $transaction == 'expire') {
            $transaksi->status = 'failed';
            $transaksi->save();
        }

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

        // Validasi Signature Key dari Midtrans
        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('order_id', $request->order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($request->transaction_status === 'settlement' || $request->transaction_status === 'capture') {
            $transaction->update([
                'status' => 'paid',
            ]);
        } elseif ($request->transaction_status === 'expire') {
            $transaction->update([
                'status' => 'expired',
            ]);
        } elseif ($request->transaction_status === 'cancel') {
            $transaction->update([
                'status' => 'cancelled',
            ]);
        } else {
            $transaction->update([
                'status' => $request->transaction_status,
            ]);
        }

        return response()->json(['message' => 'Callback received and processed']);
    }
}
