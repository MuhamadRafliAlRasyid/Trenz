@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Kelola Pengiriman</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow p-4">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Order ID</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $tx)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $tx->order_id }}</td>
                        <td class="px-4 py-2">{{ $tx->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 capitalize">{{ $tx->status }}</td>
                        <td class="px-4 py-2">{{ $tx->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">
                            <!-- Button untuk menuju detail transaksi -->
                            <a href="{{ route('admin.transactions.showDetails', $tx->id) }}" class="btn btn-info">Detail
                                Pesanan</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
