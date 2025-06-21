@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Detail Transaksi</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow p-4">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Transaction ID</th>
                    <th class="px-4 py-2">Product Name</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Price per Item</th>
                    <th class="px-4 py-2">Special Request</th>
                    <th class="px-4 py-2">Created At</th>
                    <th class="px-4 py-2">Updated At</th>
                    <th class="px-4 py-2">Total Payment</th>
                    <th class="px-4 py-2">Product Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->transactionDetails as $detail)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $detail->id }}</td>
                        <td class="px-4 py-2">{{ $transaction->order_id }}</td>
                        <td class="px-4 py-2">{{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}</td>
                        <td class="px-4 py-2">{{ $detail->quantity }}</td>
                        <td class="px-4 py-2">{{ number_format($detail->price_per_item, 2) }}</td>
                        <td class="px-4 py-2">{{ $detail->special_request ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $transaction->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $transaction->updated_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">{{ number_format($transaction->total_price, 2) }}</td>
                        <!-- Total Price from Transaction -->
                        <td class="px-4 py-2">
                            @if ($detail->product->image)
                                <img src="{{ asset('storage/' . $detail->product->image) }}"
                                    alt="{{ $detail->product->name }}" class="w-16 h-16 object-cover">
                            @else
                                <span>No Image</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Form to change transaction status -->
        @if ($transaction->status == 'paid')
            <form action="{{ route('admin.transactions.updateToProcessed', $transaction->id) }}" method="POST">
                @csrf
                <br><button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Proses Pesanan
                </button>
            </form>
        @elseif ($transaction->status == 'processed')
            <form action="{{ route('admin.transactions.updateToShipped', $transaction->id) }}" method="POST">
                @csrf
                <br><button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Mark as Shipped
                </button>
            </form>
        @endif
    </div>
@endsection
