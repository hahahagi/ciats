@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                <i class="fas fa-exchange-alt text-4xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Peminjaman Aktif</h1>
                <p class="text-purple-100">Kelola aset yang sedang dipinjam</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Total Active</p>
            <p class="text-2xl font-bold text-blue-600">{{ count($transactions) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Due Today</p>
            <p class="text-2xl font-bold text-orange-600">3</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Overdue</p>
            <p class="text-2xl font-bold text-red-600">1</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">On Time</p>
            <p class="text-2xl font-bold text-green-600">{{ count($transactions) - 4 }}</p>
        </div>
    </div>

    <!-- Active Loans List -->
    @forelse($transactions as $tx)
    @php
    $expectedReturn = $tx['expected_return_date'] ?? 0;
    $now = time();
    $daysLeft = ceil(($expectedReturn - $now) / 86400);

    if ($daysLeft < 0) { $statusClass='border-red-500 bg-red-50' ; $badgeClass='bg-red-100 text-red-700' ;
        $daysLabel='Overdue ' . abs($daysLeft) . ' days' ; } elseif ($daysLeft==0) {
        $statusClass='border-orange-500 bg-orange-50' ; $badgeClass='bg-orange-100 text-orange-700' ;
        $daysLabel='Due today' ; } elseif ($daysLeft <=3) { $statusClass='border-yellow-500 bg-yellow-50' ;
        $badgeClass='bg-yellow-100 text-yellow-700' ; $daysLabel=$daysLeft . ' days left' ; } else {
        $statusClass='border-blue-500 bg-blue-50' ; $badgeClass='bg-blue-100 text-blue-700' ; $daysLabel=$daysLeft
        . ' days left' ; } @endphp <div
        class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 {{ $statusClass }}">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4 flex-1">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 p-3 rounded-lg">
                        <i class="fas fa-laptop text-blue-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $tx['asset_name'] ?? 'Unknown' }}</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            <i class="fas fa-barcode mr-1"></i>
                            <span class="font-mono">{{ $tx['asset_serial'] ?? '-' }}</span>
                        </p>

                        <!-- Borrower -->
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                {{ strtoupper(substr($tx['user_name'] ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $tx['user_name'] ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $tx['user_email'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <span class="px-3 py-1 {{ $badgeClass }} rounded-full text-sm font-semibold whitespace-nowrap">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $daysLabel }}
                </span>
            </div>

            <!-- Timeline -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Checkout</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ date('d M Y', $tx['checkout_at'] ?? time()) }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Expected Return</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ date('d M Y', $expectedReturn) }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Location</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $tx['requested_location'] ?? '-' }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Condition</p>
                    <p class="text-sm font-medium text-gray-800 capitalize">
                        {{ str_replace('_', ' ', $tx['condition_before'] ?? 'Good') }}
                    </p>
                </div>
            </div>

            <!-- Purpose -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3 mb-4">
                <p class="text-xs text-blue-700 font-semibold mb-1">PURPOSE:</p>
                <p class="text-sm text-gray-700">{{ $tx['purpose'] ?? '-' }}</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <a href="{{ route('transactions.checkinForm', $tx['id']) }}"
                    class="flex-1 text-center px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-redo mr-2"></i>
                    Checkin (Return)
                </a>

                <button data-email="{{ e($tx['user_email'] ?? '') }}" onclick="alertContact(this)"
                    class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-phone"></i>
                </button>
            </div>
        </div>
</div>
@empty
<div class="text-center py-16 bg-white rounded-xl shadow-lg">
    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Peminjaman Aktif</h3>
    <p class="text-gray-500">Semua aset sudah dikembalikan</p>
</div>
@endforelse
</div>
@endsection

@push('scripts')
<script>
function alertContact(el) {
    const email = el?.dataset?.email || '';
    alert('Contact: ' + email);
}
</script>
@endpush