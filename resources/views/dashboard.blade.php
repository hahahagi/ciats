@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Welcome Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user['name'] }}!</h1>
                <p class="text-purple-100">Dashboard {{ ucfirst($user['role']) }} - CIATS</p>
            </div>
            <div class="hidden md:block">
                <div class="text-right">
                    <p class="text-sm text-purple-200">{{ date('l, d F Y') }}</p>
                    <p class="text-2xl font-bold" id="clock"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        @if($user['role'] == 'admin')
        <!-- Admin Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Aset</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">156</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-green-500 text-sm mt-3">
                <i class="fas fa-arrow-up mr-1"></i> +12 bulan ini
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Dipinjam</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">43</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-3">Peminjaman aktif</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total User</p>
                    <p class="text-3xl font-bold text-teal-600 mt-2">87</p>
                </div>
                <div class="bg-teal-100 p-4 rounded-full">
                    <i class="fas fa-users text-teal-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-3">Semua role</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">8</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-orange-500 text-sm mt-3">Perlu persetujuan</p>
        </div>

        @elseif($user['role'] == 'operator')
        <!-- Operator Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Aset</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">156</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('assets.index') }}" class="text-purple-600 text-sm mt-3 inline-block hover:underline">
                Lihat semua →
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending Approval</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">8</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('transactions.pendingApprovals') }}"
                class="text-orange-600 text-sm mt-3 inline-block hover:underline">
                Proses sekarang →
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Aktif Dipinjam</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">43</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('transactions.activeLoans') }}"
                class="text-blue-600 text-sm mt-3 inline-block hover:underline">
                Kelola →
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Available</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">113</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-3">Siap dipinjam</p>
        </div>

        @else
        <!-- Employee Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Aset Tersedia</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">113</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-boxes text-green-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('transactions.catalog') }}"
                class="text-green-600 text-sm mt-3 inline-block hover:underline">
                Lihat katalog →
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Request Saya</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">3</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('transactions.myRequests') }}"
                class="text-blue-600 text-sm mt-3 inline-block hover:underline">
                Lihat detail →
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Sedang Dipinjam</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">2</p>
                </div>
                <div class="bg-orange-100 p-4 rounded-full">
                    <i class="fas fa-hand-holding text-orange-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-3">Aktif</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">1</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-clock text-purple-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-3">Menunggu approval</p>
        </div>
        @endif
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Quick Actions
            </h3>

            <div class="space-y-3">
                @if($user['role'] == 'operator')
                <a href="{{ route('assets.create') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Tambah Aset Baru</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('transactions.pendingApprovals') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-orange-500 p-2 rounded-lg">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Proses Persetujuan</span>
                    </div>
                    <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">8</span>
                </a>

                <a href="{{ route('scanner.index') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <i class="fas fa-qrcode text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Scan QR Code</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                @elseif($user['role'] == 'admin')
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Kelola User</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('transactions.allTransactions') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Lihat Semua Transaksi</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('assets.index') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-teal-500 p-2 rounded-lg">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Lihat Semua Aset</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                @else
                <a href="{{ route('transactions.catalog') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-500 p-2 rounded-lg">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Browse Katalog</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('transactions.myRequests') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Lihat Request Saya</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('assets.index') }}"
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <span class="font-medium text-gray-700">Lihat Semua Aset</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-blue-500 mr-2"></i>
                Aktivitas Terbaru
            </h3>

            <div class="space-y-4">
                <div class="flex items-start space-x-3 pb-4 border-b">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Peminjaman Disetujui</p>
                        <p class="text-sm text-gray-500">Laptop Dell XPS 15 • 2 jam lalu</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 pb-4 border-b">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-plus text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Aset Baru Ditambahkan</p>
                        <p class="text-sm text-gray-500">Monitor LG 27" • 5 jam lalu</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3 pb-4 border-b">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-redo text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Aset Dikembalikan</p>
                        <p class="text-sm text-gray-500">Keyboard Mechanical • Kemarin</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <div class="bg-orange-100 p-2 rounded-lg">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Request Pending</p>
                        <p class="text-sm text-gray-500">Mouse Logitech MX • 2 hari lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time clock
function updateClock() {
    const clock = document.getElementById('clock');
    if (clock) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clock.textContent = `${hours}:${minutes}:${seconds}`;
    }
}

updateClock();
setInterval(updateClock, 1000);
</script>
@endsection