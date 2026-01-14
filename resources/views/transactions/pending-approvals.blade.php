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
                    <p class="text-3xl font-bold text-yellow-600">{{ $totalPending ?? 0 }}</p>
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
                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAllGlobal(this)" class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500 cursor-pointer mr-2">
                <label for="selectAllCheckbox" class="text-gray-700 font-semibold text-sm cursor-pointer">Select All</label>
            </div>

            <span class="font-semibold text-gray-700">
                <span id="selectedCount" class="text-purple-600 font-bold">0</span> Selected
            </span>
            <div class="flex space-x-2">
                <button type="submit" name="action" value="approve" formaction="{{ route('transactions.bulkApprove') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold text-sm">
                    <i class="fas fa-check-double mr-2"></i> Approve
                </button>
                <button type="button" onclick="confirmBulkReject()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm">
                    <i class="fas fa-times-circle mr-2"></i> Reject
                </button>
            </div>
            <!-- Hidden input for bulk reject reason -->
            <input type="hidden" name="bulk_rejection_reason" id="bulkRejectionReason">
        </div>
    </form>

    <!-- Pending Requests -->
    @forelse($groupedTransactions as $groupKey => $group)
    @php
        $tx = $group['data'];
        $items = $group['items'];
        $count = count($items);
        $isGrouped = $count > 1;
    @endphp

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 border-yellow-500 relative transition hover:shadow-xl">

        <div class="p-6 md:pl-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-start justify-between mb-4 gap-4">
                <div class="flex items-start space-x-4 flex-1">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 p-3 rounded-lg flex-shrink-0">
                        <i class="fas fa-laptop text-purple-600 text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-xl font-bold text-gray-800 truncate">{{ $tx['asset_name'] ?? 'Unknown Asset' }}</h3>
                            @if($isGrouped)
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                    {{ $count }} Items
                                </span>
                            @endif
                        </div>

                        <!-- Requestor Info -->
                        <div class="flex items-center space-x-4 text-sm mt-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-2 flex-shrink-0">
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

                <div class="flex flex-col items-end gap-2">
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold whitespace-nowrap">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                    @if($isGrouped)
                        <button type="button" onclick="toggleDetails('{{ $groupKey }}')" class="text-blue-600 hover:text-blue-800 text-sm font-semibold focus:outline-none">
                            <i class="fas fa-chevron-down mr-1"></i> View Details
                        </button>
                    @endif
                </div>
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

             <!-- Single Item Action Bar (If NOT Grouped) -->
             @if(!$isGrouped)
                <div class="flex gap-3 mt-4 border-t pt-4">
                    <div class="flex items-center mr-2">
                            <input type="checkbox" name="tx_ids[]" form="bulkApproveForm" value="{{ $tx['id'] }}"
                            class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500 cursor-pointer tx-checkbox"
                            onchange="updateBatchState()">
                    </div>

                    <button onclick="confirmApprove('{{ $tx['id'] }}', '{{ $tx['asset_name'] ?? '' }}', '{{ $tx['user_name'] ?? '' }}')"
                        class="flex-1 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>

                    <button onclick="confirmReject('{{ $tx['id'] }}', '{{ $tx['asset_name'] ?? '' }}', '{{ $tx['user_name'] ?? '' }}')"
                        class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-times mr-2"></i> Reject
                    </button>
                </div>
             @endif
        </div>

        <!-- Group Details Section (Hidden by default) -->
        @if($isGrouped)
        <div id="details-{{ $groupKey }}" class="bg-gray-50 border-t border-gray-200 hidden">
            <div class="p-4 bg-gray-100 flex justify-between items-center">
                 <h4 class="font-semibold text-gray-700">Items in this Request ({{ $count }})</h4>
                 <div class="flex items-center">
                     <input type="checkbox" onchange="toggleGroupCheckboxes(this, '{{ $groupKey }}')" class="w-4 h-4 text-purple-600 rounded border-gray-300 mr-2">
                     <span class="text-sm text-gray-600">Select All in Group</span>
                 </div>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($items as $item)
                <div class="p-4 flex flex-col md:flex-row items-center justify-between hover:bg-white transition gap-4 group-{{ $groupKey }}">
                    <div class="flex items-center space-x-4 w-full md:w-auto">
                        <input type="checkbox" name="tx_ids[]" form="bulkApproveForm" value="{{ $item['id'] }}"
                            class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500 cursor-pointer tx-checkbox group-cb-{{ $groupKey }}"
                            onchange="updateBatchState()">

                        <div>
                             <p class="font-medium text-gray-800 text-sm">Item #{{ $loop->iteration }}</p>
                             <p class="text-xs text-gray-500 font-mono">{{ $item['asset_serial'] ?? 'No Serial' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button onclick="confirmApprove('{{ $item['id'] }}', '{{ $item['asset_name'] ?? '' }}', '{{ $item['user_name'] ?? '' }}')"
                            class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded text-sm font-semibold transition">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                        <button onclick="confirmReject('{{ $item['id'] }}', '{{ $item['asset_name'] ?? '' }}', '{{ $item['user_name'] ?? '' }}')"
                            class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded text-sm font-semibold transition">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
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
    function toggleDetails(key) {
        const el = document.getElementById('details-' + key);
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }

    // Batch Actions Logic
    function toggleGroupCheckboxes(source, groupKey) {
        const checkboxes = document.querySelectorAll('.group-cb-' + groupKey);
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
        });
        updateBatchState();
    }

    function toggleSelectAllGlobal(source) {
        const checkboxes = document.querySelectorAll('.tx-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
        });
        updateBatchState();
    }

    function updateBatchState() {
        const checkboxes = document.querySelectorAll('.tx-checkbox:checked');
        const count = checkboxes.length;
        const panel = document.getElementById('batchActionPanel');
        const countSpan = document.getElementById('selectedCount');

        if (count > 0) {
            panel.style.display = 'flex';
            countSpan.textContent = count;
        } else {
            panel.style.display = 'none';
        }

        // Update Select All Checkbox state
        const allCheckboxes = document.querySelectorAll('.tx-checkbox');
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) {
             if (count === allCheckboxes.length && count > 0) {
                 selectAll.checked = true;
                 selectAll.indeterminate = false;
             } else if (count > 0) {
                 selectAll.checked = false;
                 selectAll.indeterminate = true;
             } else {
                 selectAll.checked = false;
                 selectAll.indeterminate = false;
             }
        }
    }

    function confirmBulkReject() {
        const checkboxes = document.querySelectorAll('.tx-checkbox:checked');
        if (checkboxes.length === 0) return;

        Swal.fire({
            title: 'Bulk Reject?',
            text: `Tolak ${checkboxes.length} request terpilih?`,
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan (untuk semua)',
            inputPlaceholder: 'Tuliskan alasan penolakan...',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Reject All',
            inputValidator: (value) => {
                if (!value) return 'Anda harus menuliskan alasan penolakan!'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkRejectionReason').value = result.value;
                const form = document.getElementById('bulkApproveForm');

                let actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'reject';
                form.appendChild(actionInput);

                form.submit();
            }
        });
    }

    function confirmApprove(txId, assetName, userName) {
        Swal.fire({
            title: 'Approve Request?',
            text: `Setujui peminjaman?`,
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
            text: `Tolak peminjaman?`,
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan',
            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Reject',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) return 'Anda harus menuliskan alasan penolakan!'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('actionForm');
                form.action = `/transactions/${txId}/reject`;

                const oldInput = form.querySelector('input[name="rejection_reason"]');
                if(oldInput) oldInput.remove();

                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'rejection_reason';
                reasonInput.value = result.value;
                form.appendChild(reasonInput);

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
