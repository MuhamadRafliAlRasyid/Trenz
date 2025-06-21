@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Detail Pengguna: {{ $user->name }}</h2>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-xl font-semibold">Informasi Pengguna</h3>
            <p><strong>Nama:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Status:</strong> {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</p>

            <h3 class="text-xl font-semibold mt-4">Riwayat Transaksi</h3>

            @foreach ($transactions as $transaction)
                <div class="mb-4">
                    <p class="font-semibold">Transaksi #{{ $transaction->id }} -
                        {{ $transaction->created_at->format('d/m/Y') }}</p>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 text-left">Produk</th>
                                <th class="py-2 px-4 text-left">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->transactiondetails as $detail)
                                <tr>
                                    <td class="py-2 px-4">{{ optional($detail->product)->name ?? 'Produk tidak ditemukan' }}
                                    </td>
                                    <td class="py-2 px-4">{{ $detail->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach


        </div>
    </div>
@endsection
