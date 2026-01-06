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
            <p class="text-2xl font-bold text-orange-600">{{ $stats['due_today'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">On Time</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['on_time'] }}</p>
        </div>
    </div>

    <!-- Search Bar & Controls -->
    <div class="mb-6 space-y-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="loanSearch"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm"
                placeholder="Search by asset name, serial, user, or location...">
        </div>

        <div class="flex items-center justify-between px-1">
            <label class="inline-flex items-center cursor-pointer group">
                <div class="relative flex items-center justify-center p-2 rounded-lg bg-white border border-gray-200 shadow-sm group-hover:bg-gray-50 transition">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                </div>
                <span class="ml-3 text-gray-700 font-medium select-none group-hover:text-blue-600 transition">Pilih Semua</span>
            </label>
            <div class="text-sm text-gray-500">
                <span id="visibleCount">{{ count($transactions) }}</span> items found
            </div>
        </div>
    </div>

    <form action="{{ route('transactions.bulkCheckinForm') }}" method="POST" id="activeLoansForm">
        @csrf
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
            . ' days left' ; } @endphp
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 {{ $statusClass }} flex loan-card">
                <!-- Checkbox Section -->
                <div class="p-4 flex items-center justify-center bg-gray-50 border-r border-gray-100">
                    <input type="checkbox" name="item_serials[]" value="{{ $tx['asset_serial'] }}"
                        class="w-6 h-6 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer transition transform hover:scale-110">
                </div>

            <div class="p-6 flex-1">
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
            </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Peminjaman Aktif</h3>
        <p class="text-gray-500">Semua aset sudah dikembalikan</p>
    </div>
    @endforelse

    <!-- Floating Action Button for Bulk Checkin -->
    <div class="fixed bottom-8 right-8 z-50">
        <button type="submit" class="group flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-2xl transition-all transform hover:scale-105 active:scale-95">
            <span class="mr-3 text-lg"><i class="fas fa-check-double"></i></span>
            <span class="whitespace-nowrap">Checkin Selected</span>
            <span class="ml-2 bg-white bg-opacity-20 px-2 py-0.5 rounded-full text-sm" id="selectedCount">0</span>
        </button>
    </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="item_serials[]"]');
            const selectAllCheckbox = document.getElementById('selectAll');
            const counter = document.getElementById('selectedCount');
            const button = document.querySelector('button[type="submit"]');
            const searchInput = document.getElementById('loanSearch');
            const visibleCountEl = document.getElementById('visibleCount');
            const loanCards = document.querySelectorAll('.loan-card');

            function updateCounter() {
                const checked = Array.from(checkboxes).filter(c => c.checked).length;
                counter.textContent = checked;

                if (checked > 0) {
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    button.disabled = false;
                } else {
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    button.disabled = true;
                }

                // Update Select All state based on individual checkboxes
                // Only consider checking if ALL visible items are checked
                const visibleCheckboxes = Array.from(checkboxes).filter(cb =>
                    cb.closest('.loan-card').style.display !== 'none'
                );

                const allVisibleChecked = visibleCheckboxes.length > 0 &&
                                        visibleCheckboxes.every(cb => cb.checked);

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allVisibleChecked;
                    selectAllCheckbox.indeterminate = !allVisibleChecked && checked > 0;
                }
            }

            // Select All Logic
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function(e) {
                    const isChecked = e.target.checked;

                    // Only affect visible cards
                    loanCards.forEach(card => {
                        if (card.style.display !== 'none') {
                            const cb = card.querySelector('input[name="item_serials[]"]');
                            cb.checked = isChecked;
                        }
                    });

                    updateCounter();
                });
            }

            // Individual Checkbox Logic
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateCounter);
            });

            // Prevent submit if 0 selected
            document.getElementById('activeLoansForm').addEventListener('submit', function(e) {
                const checked = Array.from(checkboxes).filter(c => c.checked).length;
                if (checked === 0) {
                    e.preventDefault();
                    alert('Pilih setidaknya satu item untuk dikembalikan.');
                }
            });

            // Search Functionality
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    const term = e.target.value.toLowerCase();
                    let visible = 0;

                    loanCards.forEach(card => {
                        // Lazy search
                        const text = card.textContent.toLowerCase();

                        if (text.includes(term)) {
                            card.style.display = 'flex';
                            visible++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if(visibleCountEl) visibleCountEl.textContent = visible;
                    updateCounter();
                });
            }

            // Init
            updateCounter();
        });
    </script>
</div>
@endsection
