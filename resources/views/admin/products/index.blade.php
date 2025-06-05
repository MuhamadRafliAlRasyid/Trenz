@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Daftar Produk</h2>

        <!-- Filter berdasarkan kategori -->
        <form action="{{ route('admin.products.index') }}" method="GET" class="mb-4">
            <div class="flex items-center space-x-4">
                <select name="category_id" id="category_id" class="p-2 border border-gray-300 rounded-md">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>
        </form>

        <a href="{{ route('admin.products.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            Tambah Produk
        </a>

        <!-- Produk Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                    <div class="h-48 overflow-hidden rounded-t-lg">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                        <p class="text-sm text-gray-600 mb-1">Kategori: {{ $product->category->name }}</p>
                        <p class="text-sm text-gray-600 mb-1">Stok: {{ $product->stock }}</p>
                        <p class="text-blue-500 font-semibold mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>

                        <div class="flex justify-between">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                                class="text-sm text-blue-600 hover:underline">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>

                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
