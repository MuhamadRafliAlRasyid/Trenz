<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Produk - Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white border-b shadow p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-[#FF5C39] flex items-center gap-2">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-6 h-6">
            TRENDZ Admin
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="font-medium">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r min-h-screen p-4">
            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-[#FF5C39]"><i
                        class="fas fa-chart-line"></i> Dashboard</a>
                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-2 text-[#FF5C39] font-semibold"><i class="fas fa-box"></i> Produk &
                    Kategori</a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]"><i
                        class="fas fa-shopping-cart"></i> Transaksi</a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]"><i class="fas fa-truck"></i>
                    Pengiriman</a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]"><i class="fas fa-users"></i>
                    Pengguna</a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]"><i class="fas fa-chart-pie"></i>
                    Laporan</a>
            </nav>
        </aside>

        <!-- Produk List -->
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-semibold mb-6">Manajemen Produk</h1>

            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.products.create') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Tambah Produk</a>
            </div>

            <!-- Produk Table -->
            <!-- Produk Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition duration-300">
                        <div class="h-48 overflow-hidden rounded-t-lg">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">

                        </div>
                        <div class="p-4">
                            <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-600 mb-1">Kategori: {{ $product->category->name }}</p>
                            <p class="text-sm text-gray-600 mb-1">Stok: {{ $product->stock }}</p>
                            <p class="text-blue-500 font-semibold mb-3">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</p>

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

        </main>
    </div>
</body>

</html>
