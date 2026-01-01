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
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_assets'] }}</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-boxes text-purple-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-green-500 text-sm mt-3">
                <i class="fas fa-arrow-up mr-1"></i> Data Realtime
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Dipinjam</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['borrowed_assets'] }}</p>
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
                    <p class="text-3xl font-bold text-teal-600 mt-2">{{ $stats['total_users'] }}</p>
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
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['pending_requests'] }}</p>
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
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_assets'] }}</p>
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
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['pending_requests'] }}</p>
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
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['borrowed_assets'] }}</p>
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
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['available_assets'] }}</p>
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
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['available_assets'] }}</p>
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
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['my_requests'] }}</p>
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
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['my_active_loans'] }}</p>
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
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['my_pending_requests'] }}</p>
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
                    <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $stats['pending_requests'] }}</span>
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
                @forelse($activities as $activity)
                    @php
                        $status = $activity['status'] ?? '';
                        $assetName = $activity['asset_name'] ?? 'Unknown Asset';
                        $userName = $activity['user_name'] ?? 'User';

                        // Determine timestamp
                        $timestamp = $activity['updated_at'] ?? $activity['created_at'] ?? $activity['requested_at'] ?? time();
                        try {
                            $time = \Carbon\Carbon::createFromTimestamp($timestamp)->diffForHumans();
                        } catch (\Exception $e) {
                            $time = '-';
                        }

                        $icon = 'fa-circle';
                        $bgClass = 'bg-gray-100';
                        $textClass = 'text-gray-600';
                        $title = 'Aktivitas';
                        $desc = "$assetName";

                        switch($status) {
                            case 'waiting_approval':
                            case 'pending':
                                $icon = 'fa-clock';
                                $bgClass = 'bg-yellow-100';
                                $textClass = 'text-yellow-600';
                                $title = "Request Peminjaman";
                                $desc = "$userName mengajukan peminjaman $assetName";
                                break;
                            case 'approved':
                                $icon = 'fa-check-circle';
                                $bgClass = 'bg-green-100';
                                $textClass = 'text-green-600';
                                $title = "Peminjaman Disetujui";
                                $desc = "Request $userName untuk $assetName disetujui";
                                break;
                            case 'active':
                                $icon = 'fa-hand-holding';
                                $bgClass = 'bg-blue-100';
                                $textClass = 'text-blue-600';
                                $title = "Barang Diambil";
                                $desc = "$userName telah mengambil $assetName";
                                break;
                            case 'completed':
                            case 'returned':
                                $icon = 'fa-check-double';
                                $bgClass = 'bg-indigo-100';
                                $textClass = 'text-indigo-600';
                                $title = "Barang Dikembalikan";
                                $desc = "$userName telah mengembalikan $assetName";
                                break;
                            case 'rejected':
                                $icon = 'fa-times-circle';
                                $bgClass = 'bg-red-100';
                                $textClass = 'text-red-600';
                                $title = "Request Ditolak";
                                $desc = "Pengajuan $userName untuk $assetName ditolak";
                                break;
                            default:
                                $icon = 'fa-info-circle';
                                $bgClass = 'bg-gray-100';
                                $textClass = 'text-gray-600';
                                $title = "Status: " . ucfirst($status);
                                $desc = "$userName - $assetName";
                        }
                    @endphp

                    <div class="flex items-start space-x-3 pb-4 border-b last:border-0">
                        <div class="{{ $bgClass }} p-2 rounded-lg">
                            <i class="fas {{ $icon }} {{ $textClass }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $title }}</p>
                            <p class="text-sm text-gray-600">{{ $desc }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-clock mr-1"></i> {{ $time }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-history text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Belum ada aktivitas terbaru.</p>
                    </div>
                @endforelse
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
