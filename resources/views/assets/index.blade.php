@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Aset</h1>
            <p class="text-gray-600">Kelola semua aset perusahaan</p>
        </div>

        @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
        <a href="{{ route('assets.create') }}"
            class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Aset
        </a>
        @endif
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari nama, serial number, kategori..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Category Filter -->
            <select id="categoryFilter"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                <option value="">Semua Kategori</option>
                <option value="laptop">Laptop</option>
                <option value="monitor">Monitor</option>
                <option value="keyboard">Keyboard</option>
                <option value="mouse">Mouse</option>
                <option value="printer">Printer</option>
            </select>

            <!-- Status Filter -->
            <select id="statusFilter"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                <option value="">Semua Status</option>
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="maintenance">Maintenance</option>
                <option value="damaged">Damaged</option>
            </select>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Items</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_items'] ?? 0 }}</p>
                </div>
                <i class="fas fa-boxes text-gray-400 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Available</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $stats['available_items'] ?? 0 }}
                    </p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">In Use</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $stats['in_use_items'] ?? 0 }}
                    </p>
                </div>
                <i class="fas fa-hand-holding text-blue-400 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Issues</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ $stats['issue_items'] ?? 0 }}
                    </p>
                </div>
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Assets Grid -->
    <div id="assetsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assets as $asset)
    @php
        $serials = collect($asset['items'] ?? [])->pluck('serial_number')->join(' ');
        $status = ($asset['available_stock'] ?? 0) > 0 ? 'available' : 'in_use';
        // Simple logic: if any available, show as available. Else in_use (or maintenance if logic expanded)
    @endphp
    <div class="asset-card bg-white rounded-xl shadow-lg overflow-hidden card-hover"
        data-name="{{ strtolower($asset['name'] ?? '') }}"
        data-category="{{ strtolower($asset['category'] ?? '') }}"
        data-serial="{{ strtolower($serials) }}"
        data-status="{{ $status }}">
            <!-- Image/Icon -->
            <div class="h-48 w-full bg-gray-100 flex items-center justify-center overflow-hidden relative">
                 @if(!empty($asset['image']))
                    <img src="{{ asset($asset['image']) }}" alt="{{ $asset['name'] }}" class="w-full h-full object-contain p-2">
                @else
                    <div class="text-center text-gray-400">
                         <i class="fas fa-image text-4xl mb-2"></i>
                         <p class="text-sm">No Image</p>
                    </div>
                @endif

                <div class="absolute top-2 right-2 flex space-x-1">
                     <span class="px-2 py-1 bg-white/90 text-gray-800 text-xs rounded-lg font-bold shadow">
                        <i class="fas fa-layer-group text-purple-500 mr-1"></i> {{ $asset['total_stock'] ?? 0 }}
                    </span>
                </div>
            </div>

            <!-- Asset Info -->
            <div class="p-6">
                 <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-gray-800 line-clamp-1">{{ $asset['name'] ?? 'Unknown' }}</h3>
                    <span class="flex-shrink-0 ml-2 px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-lg font-semibold capitalize">
                        {{ $asset['category'] ?? 'General' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4 mt-4">
                     <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Total Stok</p>
                        <p class="font-bold text-gray-800">{{ $asset['total_stock'] ?? 0 }}</p>
                     </div>
                     <div class="text-center p-2 bg-green-50 rounded-lg">
                        <p class="text-xs text-green-600">Tersedia</p>
                        <p class="font-bold text-green-700">{{ $asset['available_stock'] ?? 0 }}</p>
                     </div>
                </div>

                 <div class="text-sm text-gray-600 flex items-center mb-4">
                        <i class="fas fa-map-marker-alt text-purple-500 mr-2 w-4"></i>
                        <span>{{ $asset['location'] ?? 'Unknown' }}</span>
                 </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('assets.show', $asset['id']) }}"
                        class="flex-1 text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>

                    @if(in_array($user['role'], ['operator', 'admin', 'super_admin']))
                    <a href="{{ route('assets.edit', $asset['id']) }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada aset tersedia</p>
        </div>
        @endforelse
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden text-center py-12">
        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg">Tidak ada aset yang ditemukan</p>
    </div>
</div>

<script>
// Search & Filter functionality
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const statusFilter = document.getElementById('statusFilter');
const assetsContainer = document.getElementById('assetsContainer');
const noResults = document.getElementById('noResults');

function filterAssets() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categoryFilter.value.toLowerCase();
    const selectedStatus = statusFilter.value.toLowerCase();

    const assetCards = document.querySelectorAll('.asset-card');
    let visibleCount = 0;

    assetCards.forEach(card => {
        const name = card.dataset.name;
        const serial = card.dataset.serial;
        const category = card.dataset.category;
        const status = card.dataset.status;

        const matchesSearch = !searchTerm ||
            name.includes(searchTerm) ||
            serial.includes(searchTerm) ||
            category.includes(searchTerm);

        const matchesCategory = !selectedCategory || category === selectedCategory;
        const matchesStatus = !selectedStatus || status === selectedStatus;

        if (matchesSearch && matchesCategory && matchesStatus) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide no results message
    if (visibleCount === 0) {
        assetsContainer.classList.add('hidden');
        noResults.classList.remove('hidden');
    } else {
        assetsContainer.classList.remove('hidden');
        noResults.classList.add('hidden');
    }
}

searchInput.addEventListener('input', filterAssets);
categoryFilter.addEventListener('change', filterAssets);
statusFilter.addEventListener('change', filterAssets);
</script>
@endsection
