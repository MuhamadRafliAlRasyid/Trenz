<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Snap;

class TransactionController extends Controller
{
    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        Log::info('Transaction creation started');

        // Validasi input
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'courier_id' => 'nullable|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => 'required|in:pending,paid,processed,shipped,delivered',
            'products' => 'required|array',
        ]);

        Log::info('Validated Data: ', $validatedData);

        // Set courier_id jika tidak ada
        $courier_id = $validatedData['courier_id'] ?? null;

        // Buat order_id unik
        $orderId = 'TRENZ-' . $validatedData['user_id'] . '-' . strtoupper(Str::random(8));

        // Simpan transaksi
        $transaction = Transaction::create([
            'user_id' => $validatedData['user_id'],
            'address_id' => $validatedData['address_id'],
            'courier_id' => $courier_id,
            'total_price' => $validatedData['total_price'],
            'status' => $validatedData['status'],
            'order_id' => $orderId,
        ]);

        Log::info('Transaction Created: ', ['transaction_id' => $transaction->id]);

        // Simpan detail produk
        foreach ($validatedData['products'] as $product) {
            TransactionDetails::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price_per_item' => $product['price_per_item'],
            ]);
        }

        // Simpan data awal ke tabel payment
        Payment::create([
            'transaction_id' => $transaction->id,
            'method' => 'midtrans',
            'status' => 'unpaid',
        ]);

        Log::info('Transaction Details and Payment Saved');

        return response()->json([
            'message' => 'Transaction successfully created.',
            'transaction' => $transaction,
        ]);
    }

    // Menampilkan semua transaksi
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $transactions = Transaction::where('status', $status)->get();
        return response()->json($transactions);
    }

    // Menampilkan detail transaksi berdasarkan ID
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'address', 'transactionDetails.product', 'payment'])->findOrFail($id);
        return response()->json($transaction);
    }

    // Mengubah status transaksi
    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,paid,processed,shipped,delivered',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => $validatedData['paid'],
        ]);

        return response()->json([
            'message' => 'Status transaksi berhasil diperbarui.',
            'transaction' => $transaction,
        ]);
    }

    // Mengambil transaksi pengguna yang sedang login dan mengkategorikan berdasarkan status
    public function getCategorizedTransactions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('User ID: ' . $user->id);

        $transactions = Transaction::where('user_id', $user->id)->with('payment')->get();

        $categorized = [
            'pending' => [],
            'paid' => [],
            'processed' => [],
            'shipped' => [],
            'delivered' => [],
        ];

        foreach ($transactions as $tx) {
            if (array_key_exists($tx->status, $categorized)) {
                $categorized[$tx->status][] = $tx;
            }
        }

        return response()->json($categorized);
    }

    // Menghasilkan Snap Token untuk transaksi Midtrans
    public function getSnapToken($id)
    {
        $transaction = Transaction::with(['user', 'payment'])->findOrFail($id);

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan Snap Token ke tabel payments
        if ($transaction->payment) {
            $transaction->payment->update([
                'snap_token' => $snapToken,
            ]);
        }

        return response()->json(['snap_token' => $snapToken]);
    }
}
