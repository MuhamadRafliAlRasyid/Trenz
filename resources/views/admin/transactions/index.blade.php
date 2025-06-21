@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4 text-[#5979f5]">Daftar Transaksi</h2>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="table-auto w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border text-left">Order ID</th>
                        <th class="px-4 py-2 border text-left">User</th>
                        <th class="px-4 py-2 border text-left">Status</th>
                        <th class="px-4 py-2 border text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr class="border-b">
                            <td class="px-4 py-2 border">{{ $transaction->order_id }}</td>
                            <td class="px-4 py-2 border">{{ $transaction->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 border capitalize">{{ $transaction->status }}</td>
                            <td class="px-4 py-2 border">
                                <a href="{{ route('admin.transactions.show', $transaction->id) }}"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Lihat Detail Pesanan
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
