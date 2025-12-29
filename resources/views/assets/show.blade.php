@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Back Button -->
    <a href="{{ route('assets.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Aset
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Asset Info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Asset Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header with Status -->
                <div class="gradient-bg p-6 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-2">{{ $asset['name'] ?? 'Unknown Asset' }}</h1>
                            <p class="text-purple-100 capitalize">{{ $asset['category'] ?? 'Uncategorized' }}</p>
                        </div>

                        @php
                        $statusConfig = [
                        'available' => ['bg' => 'bg-green-500', 'icon' => 'fa-check-circle', 'label' => 'Available'],
                        'in_use' => ['bg' => 'bg-blue-500', 'icon' => 'fa-hand-holding', 'label' => 'In Use'],
                        'booked' => ['bg' => 'bg-yellow-500', 'icon' => 'fa-clock', 'label' => 'Booked'],
                        'maintenance' => ['bg' => 'bg-orange-500', 'icon' => 'fa-tools', 'label' => 'Maintenance'],
                        'damaged' => ['bg' => 'bg-red-500', 'icon' => 'fa-exclamation-triangle', 'label' => 'Damaged'],
                        ];
                        $status = $statusConfig[$asset['status'] ?? 'available'] ?? $statusConfig['available'];
                        @endphp

                        <span class="px-4 py-2 {{ $status['bg'] }} text-white rounded-full text-sm font-semibold">
                            <i class="fas {{ $status['icon'] }} mr-1"></i>
                            {{ $status['label'] }}
                        </span>
                    </div>
                </div>

                <!-- Asset Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Serial Number -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-barcode text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Serial Number</p>
                                <p class="font-mono font-semibold text-gray-800">{{ $asset['serial_number'] ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Location</p>
                                <p class="font-semibold text-gray-800">{{ $asset['location'] ?? 'Unknown' }}</p>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-teal-100 p-3 rounded-lg">
                                <i class="fas fa-tag text-teal-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Category</p>
                                <p class="font-semibold text-gray-800 capitalize">{{ $asset['category'] ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-info-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Status</p>
                                <p class="font-semibold text-gray-800 capitalize">
                                    {{ str_replace('_', ' ', $asset['status'] ?? 'available') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Holder -->
                    @if(($asset['current_holder'] ?? null))
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                        <p class="text-sm text-blue-700 font-medium mb-1">
                            <i class="fas fa-user mr-2"></i>Saat ini dipinjam oleh:
                        </p>
                        <p class="text-lg font-bold text-blue-900">{{ $asset['current_holder'] }}</p>
                    </div>
                    @endif

                    <!-- Description -->
                    @if(!empty($asset['description']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Deskripsi</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $asset['description'] }}</p>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                        <div>
                            <p class="text-xs text-gray-500">Created</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ isset($asset['created_at']) ? date('d M Y H:i', $asset['created_at']) : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Last Updated</p>
                            <p class="text-sm font-medium text-gray-700">
                                {{ isset($asset['updated_at']) ? date('d M Y H:i', $asset['updated_at']) : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Riwayat Transaksi
                </h2>

                @if(count($transactions) > 0)
                <div class="space-y-3">
                    @foreach($transactions as $tx)
                    <div class="border-l-4 
                        @if($tx['status'] == 'completed') border-green-500 @elseif($tx['status'] == 'active') border-blue-500 @elseif($tx['status'] == 'rejected') border-red-500 @else border-yellow-500 @endif
                        bg-gray-50 p-4 rounded-r-lg">

                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($tx['status'] == 'completed') bg-green-100 text-green-700
                                        @elseif($tx['status'] == 'active') bg-blue-100 text-blue-700
                                        @elseif($tx['status'] == 'rejected') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $tx['status'])) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ date('d M Y', $tx['requested_at'] ?? time()) }}
                                    </span>
                                </div>

                                <p class="font-medium text-gray-800 mb-1">{{ $tx['user_name'] ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600">{{ $tx['purpose'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Belum ada riwayat transaksi</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- QR Code -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-4">QR Code</h3>

                @if(!empty($asset['qr_code_url']))
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <img src="{{ $asset['qr_code_url'] }}" alt="QR Code" class="w-full max-w-xs mx-auto">
                </div>
                @else
                <div class="bg-gray-100 p-8 rounded-lg mb-4">
                    <i class="fas fa-qrcode text-gray-400 text-6xl"></i>
                    <p class="text-gray-500 text-sm mt-2">QR Code belum tersedia</p>
                </div>
                @endif

                <a href="{{ route('assets.printQR', $assetId) }}"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-print mr-2"></i>
                    Print QR Code
                </a>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>

                <div class="space-y-3">
                    @if($user['role'] == 'operator')
                    <a href="{{ route('assets.edit', $assetId) }}"
                        class="flex items-center justify-center space-x-2 w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-edit"></i>
                        <span>Edit Aset</span>
                    </a>

                    <form action="{{ route('assets.destroy', $assetId) }}" method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus aset ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center justify-center space-x-2 w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash"></i>
                            <span>Hapus Aset</span>
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('assets.index') }}"
                        class="flex items-center justify-center space-x-2 w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <!-- Asset Info Summary -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Info</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Booked</span>
                        @if(($asset['booked'] ?? false))
                        <span
                            class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">Yes</span>
                        @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">No</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Transactions</span>
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-semibold">
                            {{ count($transactions) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Asset ID</span>
                        <span class="font-mono text-xs text-gray-700">{{ $assetId }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection