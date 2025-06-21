<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - TRENDZ</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            transition: transform 0.3s ease;
        }

        .sidebar-hidden {
            transform: translateX(-100%);
        }

        .sidebar .sidebar-links a {
            display: flex;
            align-items: center;
        }

        .sidebar .sidebar-links a span {
            margin-left: 8px;
        }

        .sidebar-hidden .sidebar-links a span {
            display: none;
        }

        #userDropdownMenu {
            z-index: 50;
        }

        #toggleSidebarBtn {
            color: white;
            background-color: #5979f5;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 h-screen overflow-hidden">
    <!-- Wrapper -->
    <div class="flex flex-col h-screen w-screen overflow-hidden">

        <!-- Navbar -->
        <nav class="bg-white border-b shadow p-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <button id="toggleSidebarBtn" class="text-2xl p-2">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="text-xl font-bold text-[#5979f5] flex items-center gap-2">
                    <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-10 h-10"
                        onerror="this.style.display='none'">
                    TRENDZ Admin
                </div>
            </div>

            <div class="relative flex items-center gap-4 text-sm">
                @if (Auth::check())
                    <button id="userDropdownBtn"
                        class="flex items-center bg-gray-200 text-gray-800 hover:bg-gray-300 rounded-lg px-4 py-2">
                        <i class="fas fa-user-circle mr-2"></i> {{ Auth::user()->name }}
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    <div id="userDropdownMenu"
                        class="absolute right-0 mt-12 bg-white border rounded-lg shadow-lg hidden">
                        <a href="{{ route('admin.settings') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left">Settings</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 w-full text-left">Logout</button>
                        </form>
                    </div>
                @endif
            </div>
        </nav>

        <!-- Content Section -->
        <div class="flex flex-1 w-full h-full overflow-hidden">
            <!-- Sidebar -->
            <aside id="sidebar"
                class="sidebar w-64 bg-white border-r p-4 h-full transition-all md:relative absolute z-20">
                <nav class="mt-8 space-y-3 sidebar-links text-[15px]">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-chart-line"></i><span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-box"></i><span>Produk</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-tags"></i><span>Kategori</span>
                    </a>
                    <a href="{{ route('admin.transactions.index') }}"
                        class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-shopping-cart"></i><span>Transaksi</span>
                    </a>
                    <a href="{{ route('admin.shipments.index') }}" class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-truck"></i><span>Pengiriman</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-users"></i><span>Pengguna</span>
                    </a>
                    <a href="#" class="flex items-center gap-2 hover:text-[#5979f5]">
                        <i class="fas fa-chart-pie"></i><span>Laporan</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main id="main-content" class="flex-1 overflow-y-auto p-6 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script -->
    <script>
        const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-hidden');
        });

        userDropdownBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('hidden');
        });

        window.addEventListener('click', function(e) {
            if (!userDropdownBtn?.contains(e.target)) {
                userDropdownMenu?.classList.add('hidden');
            }
        });
    </script>
</body>

</html>
