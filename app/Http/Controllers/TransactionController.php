<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Models\TransactionDetails;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        Log::info('Transaction creation started');  // Log the start of the transaction creation

        // Validate input
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'address_id' => 'required|exists:addresses,id',
            'courier_id' => 'nullable|exists:users,id', // Make sure courier_id is nullable and validated
            'total_price' => 'required|numeric',
            'status' => 'required|in:pending,paid,processed,shipped,delivered',
            'products' => 'required|array', // Products that are bought
        ]);

        Log::info('Validated Data: ', $validatedData);  // Log the validated data

        // If courier_id is not provided, set it to null
        $courier_id = $validatedData['courier_id'] ?? null;

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => $validatedData['user_id'],
            'address_id' => $validatedData['address_id'],
            'courier_id' => $courier_id, // Set to null if not provided
            'total_price' => $validatedData['total_price'],
            'status' => $validatedData['status'],
        ]);

        Log::info('Transaction Created: ', ['transaction_id' => $transaction->id]);

        // Create transaction details (products)
        foreach ($validatedData['products'] as $product) {
            TransactionDetails::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price_per_item' => $product['price_per_item'],
            ]);
        }

        Log::info('Transaction Details Saved');

        // Return response
        return response()->json([
            'message' => 'Transaction successfully created.',
            'transaction' => $transaction,
        ]);
    }

    // Menampilkan semua transaksi
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // default status 'pending'

        $transactions = Transaction::where('status', $status)->get();

        return response()->json($transactions);
    }

    // Menampilkan transaksi berdasarkan ID
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'address', 'transactionDetails.product'])->findOrFail($id);
        return response()->json($transaction);
    }

    // Mengupdate status transaksi
    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,paid,processed,shipped,delivered',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => $validatedData['status'],
        ]);

        return response()->json([
            'message' => 'Status transaksi berhasil diperbarui.',
            'transaction' => $transaction,
        ]);
    }
    public function getCategorizedTransactions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('User ID: ' . $user->id);

        $transactions = Transaction::where('user_id', $user->id)->get();

        Log::info('Total transaksi ditemukan: ' . $transactions->count());
        Log::info('Transactions: ', $transactions->toArray());

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
}
