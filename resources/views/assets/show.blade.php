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
                <!-- Image & Header -->
                <div class="relative h-64 bg-gray-800">
                    @if(!empty($asset['image']))
                        <img src="{{ asset($asset['image']) }}" class="w-full h-full object-contain opacity-80">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6 pt-20">
                         <h1 class="text-3xl font-bold text-white mb-1">{{ $asset['name'] ?? 'Unknown Asset' }}</h1>
                         <p class="text-white/80 capitalize">{{ $asset['category'] ?? 'Uncategorized' }}</p>
                    </div>
                </div>

                <!-- Asset Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
                        <!-- Stock Info -->
                         <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-cubes text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Total Stock</p>
                                <p class="font-semibold text-gray-800">{{ count($items) }} Units</p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($asset['description']))
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Deskripsi</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $asset['description'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items List (Stocks) -->
            <form action="{{ route('transactions.bulkCheckout') }}" method="POST" id="bulkCheckoutForm">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $assetId }}">

             <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b flex justify-between items-center">
                     <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-list-ul text-purple-600 mr-2"></i>
                        Daftar Unit (Items)
                    </h2>

                    @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                    <div id="checkoutPanel" style="display: none;" class="items-center space-x-3 bg-purple-50 px-4 py-2 rounded-lg border border-purple-100">
                        <span class="text-xs font-semibold text-purple-700">
                            <span id="selectedItemCount">0</span> selected
                        </span>

                        <!-- Checkout Button -->
                        <button type="button" onclick="submitBulkAction('checkout')" id="btnCheckout" class="hidden px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition font-bold shadow-sm">
                            <i class="fas fa-check mr-1"></i> Checkout Selected
                        </button>

                        <!-- Checkin Button -->
                        <button type="button" onclick="submitBulkAction('checkin')" id="btnCheckin" class="hidden px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition font-bold shadow-sm">
                            <i class="fas fa-undo mr-1"></i> Checkin Selected
                        </button>
                    </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                                <th class="px-6 py-3 w-10">
                                    <input type="checkbox" onclick="toggleAllItems(this)" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                </th>
                                @endif
                                <th class="px-6 py-3">Serial Number</th>
                                <th class="px-6 py-3">Kondisi</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">QR Code</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $item)
                            <tr class="hover:bg-gray-50">
                                @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                                <td class="px-6 py-4">
                                    @php
                                        // Valid for Checkout: Booked
                                        // Valid for Checkin: In Use
                                        $status = $item['status_display'] ?? ($item['status'] ?? 'available');
                                        $canSelect = ($status === 'booked' || $status === 'in_use');
                                        $actionType = ($status === 'booked') ? 'checkout' : 'checkin';
                                    @endphp
                                    @if($canSelect)
                                    <input type="checkbox" name="item_serials[]" value="{{ $item['serial_number'] }}"
                                        data-action="{{ $actionType }}"
                                        class="item-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer"
                                        onchange="updateCheckoutState()">
                                    @else
                                    <input type="checkbox" disabled class="rounded border-gray-200 text-gray-300 cursor-not-allowed bg-gray-50">
                                    @endif
                                </td>
                                @endif
                                <td class="px-6 py-4 font-mono font-medium text-gray-800">{{ $item['serial_number'] }}</td>

                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                        {{ ($item['condition']??'good') == 'good' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($item['condition'] ?? 'good') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                     @php
                                        $statusKey = $item['status_display'] ?? ($item['status'] ?? 'available');
                                        $statusLabel = $item['status_label'] ?? ucfirst($statusKey);
                                        $statusClass = 'bg-gray-100 text-gray-700';

                                        if ($statusKey == 'available') $statusClass = 'bg-green-100 text-green-700';
                                        elseif ($statusKey == 'booked') $statusClass = 'bg-yellow-100 text-yellow-800';
                                        elseif ($statusKey == 'booked_pending') $statusClass = 'bg-orange-100 text-orange-800 border border-orange-200';
                                        elseif ($statusKey == 'in_use') $statusClass = 'bg-blue-100 text-blue-700';
                                        elseif ($statusKey == 'damaged') $statusClass = 'bg-red-100 text-red-700';
                                        elseif ($statusKey == 'maintenance') $statusClass = 'bg-orange-100 text-orange-700';
                                     @endphp
                                     <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" onclick="showItemDetail('{{ $item['serial_number'] }}', '{{ $item['condition'] }}', '{{ $item['status'] }}')"
                                            class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                                        <a href="{{ route('assets.editItem', ['id' => $assetId, 'itemId' => $item['id']]) }}"
                                            class="text-yellow-600 hover:text-yellow-800" title="Edit Item">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @endif

                                        <a href="{{ route('assets.printQR', ['id' => $assetId, 'serial' => $item['serial_number']]) }}"
                                            target="_blank"
                                            class="text-gray-600 hover:text-gray-800" title="Print QR">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
             </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- QR Code Parent (General) -->
            <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Stock QR Code</h3>
                <p class="text-xs text-gray-500 mb-4">Scan untuk melihat info stok aset ini</p>

                @if(!empty($asset['qr_code_url']))
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <img src="{{ $asset['qr_code_url'] }}" alt="QR Code" class="w-full max-w-xs mx-auto">
                </div>
                @else
                <div class="bg-gray-100 p-8 rounded-lg mb-4">
                    <!-- Generate QR dynamically if not saved -->
                     <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ url('/assets/' . $assetId) }}"
                        alt="QR Code" class="w-full max-w-xs mx-auto">
                </div>
                @endif

                <a href="{{ route('assets.printQR', $assetId) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-print mr-2"></i>
                    Print All QR
                </a>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>

                <div class="space-y-3">
                    @php
                        $pendingCheckout = null;
                        $activeLoan = null;
                        if(isset($transactions) && is_array($transactions)) {
                            foreach($transactions as $tx) {
                                if (($tx['status'] ?? '') == 'approved') {
                                    $pendingCheckout = $tx;
                                }
                                if (($tx['status'] ?? '') == 'active') {
                                    $activeLoan = $tx;
                                }
                            }
                        }
                    @endphp

                    @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                        <!-- Removed Single Checkout/Checkin Buttons - Use Bulk Action Table -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-2">
                             <p class="text-xs text-blue-700">Untuk proses <strong>Checkout</strong> atau <strong>Checkin</strong>, silahkan checklist item pada daftar unit di sebelah kiri.</p>
                        </div>
                    @endif

                    @if($user['role'] == 'operator')
                    <a href="{{ route('assets.edit', $assetId) }}"
                        class="flex items-center justify-center space-x-2 w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-edit"></i>
                        <span>Edit Aset</span>
                    </a>

                    <form id="deleteForm" action="{{ route('assets.destroy', $assetId) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
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
                        <span class="text-sm text-gray-600">Reserved (Pending)</span>
                        @if(($reservedCount ?? 0) > 0)
                        <span
                            class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">{{ $reservedCount }} Items</span>
                        @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">None</span>
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

<script>
function showItemDetail(serial, condition, status) {
    Swal.fire({
        title: 'Detail Unit',
        html: `
            <div class="text-left">
                <p><strong>Serial Number:</strong> <br> <span class="font-mono text-lg">${serial}</span></p>
                <div class="mt-4 flex justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Kondisi</p>
                        <p class="font-bold uppercase">${condition}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-bold uppercase text-purple-600">${status}</p>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${serial}"
                        class="mx-auto border p-2 rounded-lg">
                    <p class="text-xs text-gray-400 mt-2">Scan QR untuk proses transaksi</p>
                </div>
            </div>
        `,
        showCloseButton: true,
        showConfirmButton: false
    });
}

function confirmDelete() {
    Swal.fire({
        title: 'Hapus Aset?',
        text: "Aset yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    })
}

function toggleAllItems(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        if (!cb.disabled) {
            cb.checked = source.checked;
        }
    });
    updateCheckoutState();
}

function updateCheckoutState() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const count = checkboxes.length;
    const panel = document.getElementById('checkoutPanel');
    const countSpan = document.getElementById('selectedItemCount');
    const btnCheckout = document.getElementById('btnCheckout');
    const btnCheckin = document.getElementById('btnCheckin');

    if (countSpan) countSpan.textContent = count;

    if (panel) {
        if (count > 0) {
            panel.style.display = 'flex';

            // Determine dominant type
            let hasCheckout = false;
            let hasCheckin = false;

            checkboxes.forEach(cb => {
                const action = cb.getAttribute('data-action');
                if(action === 'checkout') hasCheckout = true;
                if(action === 'checkin') hasCheckin = true;
            });

            if (hasCheckout && !hasCheckin) {
                if(btnCheckout) btnCheckout.classList.remove('hidden');
                if(btnCheckin) btnCheckin.classList.add('hidden');
            } else if (hasCheckin && !hasCheckout) {
                if(btnCheckout) btnCheckout.classList.add('hidden');
                if(btnCheckin) btnCheckin.classList.remove('hidden');
            } else {
                // Mixed selection
                if(btnCheckout) btnCheckout.classList.add('hidden');
                if(btnCheckin) btnCheckin.classList.add('hidden');
            }

        } else {
            panel.style.display = 'none';
        }
    }
}

function submitBulkAction(action) {
    const form = document.getElementById('bulkCheckoutForm');
    if (action === 'checkout') {
        form.action = "{{ route('transactions.bulkCheckoutForm') }}"; // Redirect to Form
    } else if (action === 'checkin') {
        form.action = "{{ route('transactions.bulkCheckinForm') }}";
    }
    form.submit();
}
</script>
@endsection
