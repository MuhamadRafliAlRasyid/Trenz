<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - TRENDZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <nav class="bg-white border-b shadow p-4 flex justify-between items-center">
        <div class="text-xl font-bold text-[#FF5C39] flex items-center gap-2">
            <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-12 h-13">
            TRENDZ Admin
        </div>

        <div class="relative flex items-center gap-4 text-sm">
            <span class="font-medium">{{ Auth::user()->name }}</span>
            <button
                class="flex items-center space-x-2 text-sm bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg px-4 py-2">
                <i class="fas fa-user-circle"></i>
                <span>{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>

            <div class="absolute right-0 w-48 mt-2 bg-white border rounded-lg shadow-lg hidden dropdown-menu">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar + Main -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r min-h-screen p-4">
            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-[#FF5C39]">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="flex items-center gap-2 text-[#FF5C39] font-semibold">
                    <i class="fas fa-box"></i> Produk & Kategori
                </a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]">
                    <i class="fas fa-shopping-cart"></i> Transaksi
                </a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]">
                    <i class="fas fa-truck"></i> Pengiriman
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 hover:text-[#FF5C39]">
                    <i class="fas fa-users"></i> Pengguna
                </a>
                <a href="#" class="flex items-center gap-2 hover:text-[#FF5C39]">
                    <i class="fas fa-chart-pie"></i> Laporan
                </a>
            </nav>
        </aside>


        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <script>
        const dropdownBtn = document.querySelector('button');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        dropdownBtn.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        window.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
