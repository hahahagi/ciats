<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CIATS' }} - Corporate IT Asset Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    .gradient-bg {
        background: #3b82f6;
    }

    .sidebar-gradient {
        background: #3b82f6;
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .sidebar-link {
        transition: all 0.2s ease;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(4px);
    }

    .sidebar-link.active {
        background: rgba(255, 255, 255, 0.22);
        font-weight: 700;
        border-left: 4px solid rgba(255, 255, 255, 0.95);
        box-shadow: inset 0 0 12px rgba(0,0,0,0.04);
    }

    .alert-slide {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile Sidebar Animation */
    .sidebar-mobile {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-mobile.open {
        transform: translateX(0);
    }

    /* Overlay */
    .overlay {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out;
    }

    .overlay.show {
        opacity: 1;
        pointer-events: all;
    }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="gradient-bg shadow-lg sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Left: Mobile Menu + Logo -->
                <div class="flex items-center space-x-3">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileSidebar()"
                        class="lg:hidden w-10 h-10 flex items-center justify-center text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                        <div class="text-white hidden sm:block">
                            <h1 class="text-lg font-bold">CIATS</h1>
                            <p class="text-xs text-purple-200">Asset Tracking System</p>
                        </div>
                    </div>
                </div>

                <!-- Right: User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-right text-white">
                        <p class="text-sm font-medium">{{ $user['name'] ?? 'User' }}</p>
                        <p class="text-xs text-purple-200 capitalize">{{ $user['role'] ?? 'employee' }}</p>
                    </div>

                    <div class="relative group">
                        <button onclick="toggleProfileDropdown()"
                            class="w-10 h-10 rounded-full bg-white text-blue-600 font-semibold flex items-center justify-center hover:scale-110 transition">
                            {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                        </button>

                        <!-- Dropdown -->
                        <div id="profileDropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                            <div class="px-4 py-2 border-b md:hidden">
                                <p class="font-medium text-gray-800">{{ $user['name'] ?? 'User' }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $user['role'] ?? 'employee' }}</p>
                            </div>
                            <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-home mr-2"></i> Dashboard
                            </a>
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100 hover:text-red-700 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="overlay fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        onclick="toggleMobileSidebar()">
    </div>

    <div class="flex">
        <!-- Sidebar Desktop -->
        <aside class="sidebar-gradient w-64 min-h-screen fixed left-0 top-16 bottom-0 hidden lg:block shadow-2xl overflow-y-auto z-40">
            <div class="p-4">
                <!-- Dashboard -->
                <a href="/dashboard"
                    class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home text-xl"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Assets -->
                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Aset</p>
                    <a href="{{ route('assets.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes text-xl"></i>
                        <span>Daftar Aset</span>
                    </a>
                </div>

                <!-- Transactions -->
                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Transaksi</p>

                    @if(($user['role'] ?? '') == 'employee')
                    <a href="{{ route('transactions.catalog') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.catalog') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span>Katalog</span>
                    </a>
                    <a href="{{ route('transactions.myRequests') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.myRequests') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list text-xl"></i>
                        <span>Request Saya</span>
                    </a>
                    @endif

                    @if(in_array($user['role'] ?? '', ['operator', 'admin']))
                    <a href="{{ route('transactions.pendingApprovals') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.pendingApprovals') ? 'active' : '' }}">
                        <i class="fas fa-clock text-xl"></i>
                        <span>Persetujuan</span>
                    </a>
                    <a href="{{ route('transactions.activeLoans') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.activeLoans') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt text-xl"></i>
                        <span>Peminjaman Aktif</span>
                    </a>
                    @endif

                    @if(($user['role'] ?? '') == 'admin')
                    <a href="{{ route('transactions.allTransactions') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.allTransactions') ? 'active' : '' }}">
                        <i class="fas fa-list text-xl"></i>
                        <span>Semua Transaksi</span>
                    </a>
                    @endif
                </div>

                <!-- Scanner -->
                @if(in_array($user['role'] ?? '', ['operator', 'admin']))
                <div class="mt-4">
                    <a href="{{ route('scanner.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('scanner.index') ? 'active' : '' }}">
                        <i class="fas fa-qrcode text-xl"></i>
                        <span>QR Scanner</span>
                    </a>
                </div>
                @endif

                <!-- User Management -->
                @if(($user['role'] ?? '') == 'admin')
                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Admin</p>
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users text-xl"></i>
                        <span>Kelola User</span>
                    </a>
                </div>
                @endif
            </div>
        </aside>

        <!-- Sidebar Mobile -->
        <aside id="mobileSidebar"
            class="sidebar-mobile sidebar-gradient w-72 fixed inset-y-0 left-0 z-50 lg:hidden shadow-2xl overflow-y-auto">
            <div class="p-4">
                <!-- Close Button -->
                <div class="flex items-center justify-between mb-6">
                    <div class="text-white">
                        <h2 class="text-lg font-bold">Menu</h2>
                        <p class="text-xs text-purple-200">CIATS Navigation</p>
                    </div>
                    <button onclick="toggleMobileSidebar()"
                        class="w-10 h-10 flex items-center justify-center text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Same menu as desktop -->
                <a href="/dashboard"
                    class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home text-xl w-6"></i>
                    <span>Dashboard</span>
                </a>

                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Aset</p>
                    <a href="{{ route('assets.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes text-xl w-6"></i>
                        <span>Daftar Aset</span>
                    </a>
                    @if(($user['role'] ?? '') == 'operator')
                    <a href="{{ route('assets.create') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('assets.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle text-xl w-6"></i>
                        <span>Tambah Aset</span>
                    </a>
                    @endif
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Transaksi</p>
                    @if(($user['role'] ?? '') == 'employee')
                    <a href="{{ route('transactions.catalog') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.catalog') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart text-xl w-6"></i>
                        <span>Katalog</span>
                    </a>
                    <a href="{{ route('transactions.myRequests') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.myRequests') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list text-xl w-6"></i>
                        <span>Request Saya</span>
                    </a>
                    @endif

                    @if(in_array($user['role'] ?? '', ['operator', 'admin']))
                    <a href="{{ route('transactions.pendingApprovals') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.pendingApprovals') ? 'active' : '' }}">
                        <i class="fas fa-clock text-xl w-6"></i>
                        <span>Persetujuan</span>
                    </a>
                    <a href="{{ route('transactions.activeLoans') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.activeLoans') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt text-xl w-6"></i>
                        <span>Peminjaman Aktif</span>
                    </a>
                    @endif

                    @if(($user['role'] ?? '') == 'admin')
                    <a href="{{ route('transactions.allTransactions') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('transactions.allTransactions') ? 'active' : '' }}">
                        <i class="fas fa-list text-xl w-6"></i>
                        <span>Semua Transaksi</span>
                    </a>
                    @endif
                </div>

                @if(in_array($user['role'] ?? '', ['operator', 'admin']))
                <div class="mt-4">
                    <a href="{{ route('scanner.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('scanner.index') ? 'active' : '' }}">
                        <i class="fas fa-qrcode text-xl w-6"></i>
                        <span>QR Scanner</span>
                    </a>
                </div>
                @endif

                @if(($user['role'] ?? '') == 'admin')
                <div class="mt-4">
                    <p class="text-xs font-semibold text-purple-200 uppercase px-4 mb-2">Admin</p>
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg mb-2 text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users text-xl w-6"></i>
                        <span>Kelola User</span>
                    </a>
                </div>
                @endif
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:ml-64">
            <!-- Alerts -->
            @if(session('success'))
            <div class="alert-slide bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert-slide bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert-slide bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                    <p class="font-semibold">Terdapat kesalahan:</p>
                </div>
                <ul class="list-disc list-inside ml-8">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('mobileOverlay');

        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }

    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close profile dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profileBtn = event.target.closest('button[onclick="toggleProfileDropdown()"]');
        
        if (!profileBtn && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-slide');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Close mobile sidebar when clicking on a link
    document.querySelectorAll('#mobileSidebar a').forEach(link => {
        link.addEventListener('click', () => {
            toggleMobileSidebar();
        });
    });
    </script>
</body>

</html>