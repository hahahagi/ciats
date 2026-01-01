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

    <!-- Pending Requests -->
    @forelse($transactions as $tx)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border-l-4 border-yellow-500">
        <div class="p-6">
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
</script>
@endsection
