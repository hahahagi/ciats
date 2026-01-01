@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                <i class="fas fa-list text-4xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Semua Transaksi</h1>
                <p class="text-blue-100">Lihat riwayat semua transaksi peminjaman aset</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Transaksi</p>
                    <p class="text-3xl font-bold text-blue-600">{{ count($transactions) }}</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-list text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Active</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ count(array_filter($transactions, fn($t) => ($t['status'] ?? '') === 'active')) }}</p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-full">
                    <i class="fas fa-hourglass-half text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ count(array_filter($transactions, fn($t) => ($t['status'] ?? '') === 'completed')) }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-check-double text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ count(array_filter($transactions, fn($t) => in_array($t['status'] ?? '', ['pending', 'waiting_approval']))) }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="gradient-bg text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Aset</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Peminjam</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal Request</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <i class="fas fa-laptop text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $tx['asset_name'] ?? 'Unknown Asset' }}</p>
                                    <p class="text-xs text-gray-500">{{ $tx['asset_serial'] ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                    {{ strtoupper(substr($tx['user_name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $tx['user_name'] ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $tx['user_email'] ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $tx['status'] ?? 'unknown';
                                $statusConfig = [
                                    'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock'],
                                    'waiting_approval' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock'],
                                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle'],
                                    'returned' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-undo'],
                                    'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-check-double'],
                                    'active' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'icon' => 'fa-hourglass-half'],
                                ];
                                $config = $statusConfig[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-question'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 {{ $config['bg'] }} {{ $config['text'] }} rounded-full text-xs font-semibold">
                                <i class="fas {{ $config['icon'] }} mr-1"></i>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($tx['requested_at'])
                                {{ \Carbon\Carbon::parse($tx['requested_at'])->format('d M Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <p class="max-w-xs truncate">{{ $tx['reason'] ?? $tx['notes'] ?? '-' }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-4"></i>
                            <p class="text-lg font-medium">Belum ada transaksi</p>
                            <p class="text-sm">Tidak ada riwayat transaksi untuk ditampilkan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
