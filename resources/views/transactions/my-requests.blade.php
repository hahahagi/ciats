@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Request Peminjaman Saya</h1>
            <p class="text-gray-600">Track status peminjaman aset Anda</p>
        </div>

        <a href="{{ route('transactions.catalog') }}"
            class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Ajukan Baru
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
        $stats = [
        'waiting_approval' => collect($transactions)->where('status', 'waiting_approval')->count(),
        'approved' => collect($transactions)->where('status', 'approved')->count(),
        'active' => collect($transactions)->where('status', 'active')->count(),
        'completed' => collect($transactions)->where('status', 'completed')->count(),
        ];
        @endphp

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['waiting_approval'] }}</p>
                </div>
                <i class="fas fa-clock text-yellow-400 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Active</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['active'] }}</p>
                </div>
                <i class="fas fa-hand-holding text-blue-400 text-2xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Completed</p>
                    <p class="text-2xl font-bold text-gray-600">{{ $stats['completed'] }}</p>
                </div>
                <i class="fas fa-check-double text-gray-400 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    @forelse($transactions as $tx)
    @php
    $statusConfig = [
    'waiting_approval' => [
    'bg' => 'bg-yellow-50',
    'border' => 'border-yellow-500',
    'badge_bg' => 'bg-yellow-100',
    'badge_text' => 'text-yellow-700',
    'icon' => 'fa-clock',
    'icon_color' => 'text-yellow-500',
    'label' => 'Menunggu Persetujuan'
    ],
    'approved' => [
    'bg' => 'bg-green-50',
    'border' => 'border-green-500',
    'badge_bg' => 'bg-green-100',
    'badge_text' => 'text-green-700',
    'icon' => 'fa-check-circle',
    'icon_color' => 'text-green-500',
    'label' => 'Disetujui'
    ],
    'active' => [
    'bg' => 'bg-blue-50',
    'border' => 'border-blue-500',
    'badge_bg' => 'bg-blue-100',
    'badge_text' => 'text-blue-700',
    'icon' => 'fa-hand-holding',
    'icon_color' => 'text-blue-500',
    'label' => 'Sedang Dipinjam'
    ],
    'completed' => [
    'bg' => 'bg-gray-50',
    'border' => 'border-gray-500',
    'badge_bg' => 'bg-gray-100',
    'badge_text' => 'text-gray-700',
    'icon' => 'fa-check-double',
    'icon_color' => 'text-gray-500',
    'label' => 'Selesai'
    ],
    'rejected' => [
    'bg' => 'bg-red-50',
    'border' => 'border-red-500',
    'badge_bg' => 'bg-red-100',
    'badge_text' => 'text-red-700',
    'icon' => 'fa-times-circle',
    'icon_color' => 'text-red-500',
    'label' => 'Ditolak'
    ],
    ];
    $status = $statusConfig[$tx['status'] ?? 'waiting_approval'] ?? $statusConfig['waiting_approval'];
    @endphp

    <div
        class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 {{ $status['bg'] }} border-l-4 {{ $status['border'] }}">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4">
                    <div class="bg-white p-3 rounded-lg shadow">
                        <i class="fas fa-laptop text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $tx['asset_name'] ?? 'Unknown Asset' }}</h3>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-barcode mr-1"></i>
                            <span class="font-mono">{{ $tx['asset_serial'] ?? '-' }}</span>
                        </p>
                    </div>
                </div>

                <span
                    class="px-4 py-2 {{ $status['badge_bg'] }} {{ $status['badge_text'] }} rounded-full text-sm font-semibold">
                    <i class="fas {{ $status['icon'] }} mr-1"></i>
                    {{ $status['label'] }}
                </span>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Request Date</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ date('d M Y, H:i', $tx['requested_at'] ?? time()) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Expected Return</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-calendar-check mr-1"></i>
                        {{ date('d M Y', $tx['expected_return_date'] ?? time()) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Location</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $tx['requested_location'] ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Purpose -->
            <div class="bg-white bg-opacity-60 rounded-lg p-4 mb-4">
                <p class="text-xs text-gray-500 mb-1">Purpose</p>
                <p class="text-sm text-gray-700">{{ $tx['purpose'] ?? '-' }}</p>
            </div>

            <!-- Timeline -->
            <div class="flex items-center space-x-2 text-sm">
                <!-- Requested -->
                <div class="flex items-center {{ ($tx['requested_at'] ?? false) ? 'text-green-600' : 'text-gray-400' }}">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Requested</span>
                </div>
                <div class="flex-1 h-1 {{ ($tx['approved_at'] ?? false) ? 'bg-green-500' : 'bg-gray-300' }} rounded"></div>

                <!-- Approved -->
                <div class="flex items-center {{ ($tx['approved_at'] ?? false) ? 'text-green-600' : 'text-gray-400' }}">
                    <i class="fas fa-{{ ($tx['status'] ?? '') == 'rejected' ? 'times' : 'check' }}-circle mr-1"></i>
                    <span>{{ ($tx['status'] ?? '') == 'rejected' ? 'Rejected' : 'Approved' }}</span>
                </div>
                <div class="flex-1 h-1 {{ ($tx['checkout_at'] ?? false) ? 'bg-blue-500' : 'bg-gray-300' }} rounded"></div>

                <!-- Checkout -->
                <div class="flex items-center {{ ($tx['checkout_at'] ?? false) ? 'text-blue-600' : 'text-gray-400' }}">
                    <i class="fas fa-hand-holding mr-1"></i>
                    <span>Checkout</span>
                </div>
                <div class="flex-1 h-1 {{ ($tx['checkin_at'] ?? false) ? 'bg-gray-500' : 'bg-gray-300' }} rounded"></div>

                <!-- Checkin -->
                <div class="flex items-center {{ ($tx['checkin_at'] ?? false) ? 'text-gray-600' : 'text-gray-400' }}">
                    <i class="fas fa-check-double mr-1"></i>
                    <span>Returned</span>
                </div>
            </div>

            <!-- Additional Info -->
            @if($tx['status'] == 'approved')
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Request Anda telah disetujui! Silakan ambil barang di operator untuk checkout.
                </p>
                @if(!empty($tx['approved_by_name']))
                <p class="text-xs text-green-700 mt-1">
                    Disetujui oleh: <span class="font-medium">{{ $tx['approved_by_name'] }}</span>
                </p>
                @endif
            </div>
            @endif

            @if($tx['status'] == 'rejected' && !empty($tx['rejection_reason']))
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm font-medium text-red-900 mb-1">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Alasan Penolakan:
                </p>
                <p class="text-sm text-red-700">{{ $tx['rejection_reason'] }}</p>
            </div>
            @endif

            @if($tx['status'] == 'active')
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Aset sedang Anda gunakan. Jangan lupa kembalikan sesuai tanggal yang dijanjikan!
                </p>
                @if($tx['checkout_at'])
                <p class="text-xs text-blue-700 mt-1">
                    Checkout: {{ date('d M Y, H:i', $tx['checkout_at']) }}
                </p>
                @endif
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Request</h3>
        <p class="text-gray-500 mb-6">Anda belum pernah mengajukan peminjaman aset</p>
        <a href="{{ route('transactions.catalog') }}"
            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-shopping-cart mr-2"></i>
            Browse Katalog
        </a>
    </div>
    @endforelse
</div>
@endsection
