@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                <i class="fas fa-clock text-4xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Persetujuan Peminjaman</h1>
                <p class="text-purple-100">Review dan proses request peminjaman aset</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ count($transactions) }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Approved This Week</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['approved_week'] }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Rejected This Week</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['rejected_week'] }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Actions -->
    <form action="{{ route('transactions.bulkApprove') }}" method="POST" id="bulkApproveForm" class="hidden md:block mb-4">
        @csrf
        <div class="flex items-center space-x-4 bg-white p-4 rounded-xl shadow-md border border-gray-100" id="batchActionPanel" style="display: none;">
            <div class="flex items-center border-r border-gray-200 pr-4">
                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)" class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500 cursor-pointer mr-2">
                <label for="selectAllCheckbox" class="text-gray-700 font-semibold text-sm cursor-pointer">Select All</label>
            </div>

            <span class="font-semibold text-gray-700">
                <span id="selectedCount" class="text-purple-600 font-bold">0</span> Selected
            </span>
            <div class="flex space-x-2">
                <button type="submit" name="action" value="approve" formaction="{{ route('transactions.bulkApprove') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold text-sm">
                    <i class="fas fa-check-double mr-2"></i> Approve Selected
                </button>
                <button type="button" onclick="confirmBulkReject()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm">
                    <i class="fas fa-times-circle mr-2"></i> Reject Selected
                </button>
            </div>
            <!-- Hidden input for bulk reject reason -->
            <input type="hidden" name="bulk_rejection_reason" id="bulkRejectionReason">
        </div>
    </form>

    <!-- Pending Requests -->
    @forelse($transactions as $tx)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 border-yellow-500 relative group">
        <!-- Checkbox for Batch -->
        <div class="absolute top-4 right-4 z-10 md:right-auto md:left-4 md:top-1/2 md:-translate-y-1/2 md:-ml-12 transition-all duration-300 group-hover:block md:group-hover:ml-2">
             <input type="checkbox" name="tx_ids[]" form="bulkApproveForm" value="{{ $tx['id'] }}"
                class="w-6 h-6 text-purple-600 rounded border-gray-300 focus:ring-purple-500 cursor-pointer tx-checkbox"
                onchange="updateBatchState()">
        </div>

        <div class="p-6 md:pl-16"> <!-- Added padding-left for checkbox space -->
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4 flex-1">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-3 rounded-lg">
                        <i class="fas fa-laptop text-purple-600 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $tx['asset_name'] ?? 'Unknown Asset' }}</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-barcode mr-1"></i>
                            <span class="font-mono">{{ $tx['asset_serial'] ?? '-' }}</span>
                        </p>

                        <!-- Requestor Info -->
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-2">
                                    {{ strtoupper(substr($tx['user_name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $tx['user_name'] ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $tx['user_email'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <span
                    class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold whitespace-nowrap">
                    <i class="fas fa-clock mr-1"></i>
                    Pending
                </span>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Request Date</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-calendar mr-1 text-purple-500"></i>
                        {{ date('d M Y, H:i', $tx['requested_at'] ?? time()) }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Expected Return</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-calendar-check mr-1 text-purple-500"></i>
                        {{ date('d M Y', $tx['expected_return_date'] ?? time()) }}
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Requested Location</p>
                    <p class="text-sm font-medium text-gray-800">
                        <i class="fas fa-map-marker-alt mr-1 text-purple-500"></i>
                        {{ $tx['requested_location'] ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Purpose -->
            <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-4 mb-4">
                <p class="text-xs text-purple-700 font-semibold mb-2">PURPOSE:</p>
                <p class="text-sm text-gray-700">{{ $tx['purpose'] ?? '-' }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button onclick="confirmApprove('{{ $tx['id'] }}', '{{ $tx['asset_name'] ?? '' }}', '{{ $tx['user_name'] ?? '' }}')"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-check mr-2"></i>
                    Approve
                </button>

                <button onclick="confirmReject('{{ $tx['id'] }}', '{{ $tx['asset_name'] ?? '' }}', '{{ $tx['user_name'] ?? '' }}')"
                    class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-times mr-2"></i>
                    Reject
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <i class="fas fa-check-double text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Semua Bersih!</h3>
        <p class="text-gray-500">Tidak ada request yang perlu diproses saat ini</p>
    </div>
    @endforelse
</div>

<!-- Hidden Forms for Actions -->
<form id="actionForm" method="POST" class="hidden">
    @csrf
</form>

<script>
function confirmApprove(txId, assetName, userName) {
    Swal.fire({
        title: 'Approve Request?',
        text: `Setujui peminjaman ${assetName} untuk ${userName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Approve',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('actionForm');
            form.action = `/transactions/${txId}/approve`;
            form.submit();
        }
    });
}

function confirmReject(txId, assetName, userName) {
    Swal.fire({
        title: 'Reject Request',
        text: `Tolak peminjaman ${assetName} untuk ${userName}?`,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
        inputAttributes: {
            'aria-label': 'Tuliskan alasan penolakan di sini'
        },
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Reject',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Anda harus menuliskan alasan penolakan!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('actionForm');
            form.action = `/transactions/${txId}/reject`;

            // Add rejection reason input dynamically
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = result.value;
            form.appendChild(reasonInput);

            form.submit();
        }
    });
}

function updateBatchState() {
    const checkboxes = document.querySelectorAll('.tx-checkbox:checked');
    const panel = document.getElementById('batchActionPanel');
    const countSpan = document.getElementById('selectedCount');

    countSpan.textContent = checkboxes.length;
    if (checkboxes.length > 0) {
        panel.style.display = 'flex';
    } else {
        // Don't hide the panel completely if we want to show "Select All" always?
        // Logic: if hidden, user can't select all? No, select all is Inside the panel.
        // So we need a way to show panel if any item exists? Or keep it hidden until checkbox?
        // Better: Always show panel if there are items, but disable buttons?
        // Current logic: hide panel if nothing selected -> implies manual click on item to show panel.
        // Let's stick to current logic, but ensure 'Select All' is visible if user wants to use it?
        // Wait, if panel is hidden, select all is hidden. How to select all?
        // Fix: Move select all outside or keep panel visible.
        // Correct fix: check if any checkboxes exist on page. If yes, show panel (maybe disabled buttons).

        // Actually, let's keep it simple: Show panel if count > 0.
        // But how to click "Select All"?
        // Let's modify: user manually checks ONE, then panel appears, then can click Check All?
        // No, check all should be available.
        panel.style.display = 'flex'; // Always show panel but disable buttons if 0
    }

    // Toggle buttons state
    const buttons = panel.querySelectorAll('button');
    buttons.forEach(btn => {
        btn.disabled = checkboxes.length === 0;
        if(checkboxes.length === 0) {
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
             btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });
}

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.tx-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
    updateBatchState();
}

function confirmBulkReject() {
    Swal.fire({
        title: 'Reject Selected?',
        text: `Tolak semua permintaan yang dipilih?`,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan (Untuk Semua)',
        inputPlaceholder: 'Tuliskan alasan penolakan...',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Reject All',
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan wajib diisi!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('bulkApproveForm');
            document.getElementById('bulkRejectionReason').value = result.value;
            form.action = "{{ route('transactions.bulkReject') }}";
            form.submit();
        }
    });
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    updateBatchState();
});
</script>
@endsection
