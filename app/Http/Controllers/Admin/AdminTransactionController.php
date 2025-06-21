<?php

namespace App\Http\Controllers\Admin;

use App\Models\Delivery;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Midtrans\Config as MidtransConfig;
use Midtrans\Transaction as MidtransTransaction;

class AdminTransactionController extends Controller
{
    // 1. Tampilkan semua transaksi
    public function index()
    {
        $transactions = Transaction::with('user', 'transactionDetails.product')
            ->orderByDesc('created_at')
            ->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    // Fungsi untuk mengubah status transaksi menjadi 'processed'
    public function updateToProcessed($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan hanya transaksi dengan status 'paid' yang bisa diproses
        if ($transaction->status !== 'paid') {
            return redirect()->back()->with('error', 'Transaksi belum dibayar, tidak dapat diproses.');
        }

        $transaction->status = 'processed';
        $transaction->save();

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui menjadi Diproses.');
    }

    // Fungsi untuk mengubah status transaksi menjadi 'shipped'
    public function updateToShipped($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan hanya transaksi dengan status 'processed' yang bisa dikirim
        if ($transaction->status !== 'processed') {
            return redirect()->back()->with('error', 'Transaksi belum diproses, tidak dapat dikirim.');
        }

        $transaction->status = 'shipped';
        $transaction->save();

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui menjadi Dikirim.');
    }

    // Fungsi untuk mengubah status transaksi menjadi 'delivered'
    public function updateToDelivered($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Pastikan hanya transaksi dengan status 'shipped' yang bisa dikirimkan
        if ($transaction->status !== 'shipped') {
            return redirect()->back()->with('error', 'Transaksi belum dikirim, tidak dapat dikirimkan.');
        }

        $transaction->status = 'delivered';
        $transaction->save();

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui menjadi Diterima.');
    }

    // 2. Tampilkan detail transaksi
    public function show($id)
    {
        // Mengambil transaksi berdasarkan ID dan relasi terkait
        $transaction = Transaction::with(['user', 'transactionDetails.product', 'payment'])->findOrFail($id);

        // Mengirimkan data transaksi dan total harga ke view
        return view('admin.transactions.show', compact('transaction'));
    }

    public function showTransactionDetails($id)
    {
        // Ambil transaksi berdasarkan ID
        $transaction = Transaction::with(['transactionDetails.product']) // Ambil detail transaksi beserta produk
            ->findOrFail($id); // Temukan transaksi atau gagal

        // Hitung total pembayaran
        $totalPayment = $transaction->transactionDetails->sum(function ($detail) {
            return $detail->price_per_item * $detail->quantity; // Total pembayaran berdasarkan jumlah dan harga per item
        });

        return view('admin.transactions.details', compact('transaction', 'totalPayment'));
    }

    // 3. Validasi pembayaran dengan Midtrans
    public function validatePayment($id)
    {
        $transaction = Transaction::with('payment')->findOrFail($id);
        $orderId = $transaction->order_id;

        try {
            // Konfigurasi Midtrans
            MidtransConfig::$serverKey = config('services.midtrans.server_key');
            MidtransConfig::$isProduction = config('services.midtrans.is_production', false);
            MidtransConfig::$isSanitized = true;
            MidtransConfig::$is3ds = true;

            // Ambil status dari Midtrans
            $status = (object) MidtransTransaction::status($orderId);


            // Validasi berdasarkan status
            switch ($status->transaction_status) {
                case 'settlement':
                    $transaction->status = 'paid';
                    $transaction->payment->status = 'paid';
                    break;

                case 'pending':
                    $transaction->status = 'pending';
                    $transaction->payment->status = 'pending';
                    break;

                default:
                    $transaction->status = 'failed';
                    $transaction->payment->status = 'failed';
                    break;
            }

            $transaction->push(); // Simpan semua perubahan model terkait

            return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal validasi pembayaran: ' . $e->getMessage());
        }
    }

    // 4. Update status pengiriman
    public function markDelivered(Transaction $transaction)
    {
        // Memastikan status transaksi sudah "shipped" sebelum mengubah menjadi "delivered"
        if ($transaction->status !== 'shipped') {
            return redirect()->back()->with('error', 'Transaksi belum diproses atau dikirim.');
        }

        // Mengubah status transaksi menjadi "delivered"
        $transaction->status = 'delivered';
        $transaction->save();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaksi telah dikirim.');
    }
}
