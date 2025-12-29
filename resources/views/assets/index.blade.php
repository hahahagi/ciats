@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Aset</h1>
            <p class="text-gray-600">Kelola semua aset perusahaan</p>
        </div>

        @if($user['role'] == 'operator')
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
                    <p class="text-gray-500 text-sm">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ count($assets) }}</p>
                </div>
                <i class="fas fa-boxes text-gray-400 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Available</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ collect($assets)->where('status', 'available')->count() }}
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
                        {{ collect($assets)->where('status', 'in_use')->count() }}
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
                        {{ collect($assets)->whereIn('status', ['maintenance', 'damaged'])->count() }}
                    </p>
                </div>
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Assets Grid -->
    <div id="assetsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assets as $asset)
        <div class="asset-card bg-white rounded-xl shadow-lg overflow-hidden card-hover"
            data-name="{{ strtolower($asset['name'] ?? '') }}"
            data-serial="{{ strtolower($asset['serial_number'] ?? '') }}"
            data-category="{{ strtolower($asset['category'] ?? '') }}"
            data-status="{{ $asset['status'] ?? 'available' }}">

            <!-- Status Badge -->
            <div class="p-4 pb-0">
                @php
                $statusConfig = [
                'available' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle', 'label'
                => 'Available'],
                'in_use' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-hand-holding', 'label' =>
                'In Use'],
                'booked' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' =>
                'Booked'],
                'maintenance' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'fa-tools', 'label' =>
                'Maintenance'],
                'damaged' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-exclamation-triangle',
                'label' => 'Damaged'],
                ];
                $status = $statusConfig[$asset['status'] ?? 'available'] ?? $statusConfig['available'];
                @endphp

                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $status['bg'] }} {{ $status['text'] }}">
                    <i class="fas {{ $status['icon'] }} mr-1"></i>
                    {{ $status['label'] }}
                </span>
            </div>

            <!-- Asset Image/Icon -->
            <div class="flex items-center justify-center p-6">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-laptop text-purple-600 text-5xl"></i>
                </div>
            </div>

            <!-- Asset Info -->
            <div class="p-6 pt-0">
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $asset['name'] ?? 'Unknown' }}</h3>

                <div class="space-y-2 mb-4">
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-tag text-purple-500 mr-2 w-4"></i>
                        <span class="capitalize">{{ $asset['category'] ?? 'Uncategorized' }}</span>
                    </p>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-barcode text-purple-500 mr-2 w-4"></i>
                        <span class="font-mono">{{ $asset['serial_number'] ?? '-' }}</span>
                    </p>
                    <p class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-map-marker-alt text-purple-500 mr-2 w-4"></i>
                        <span>{{ $asset['location'] ?? 'Unknown' }}</span>
                    </p>
                </div>

                @if(($asset['current_holder'] ?? null))
                <div class="bg-blue-50 rounded-lg p-3 mb-4">
                    <p class="text-xs text-blue-700 font-medium">
                        <i class="fas fa-user mr-1"></i>
                        Dipinjam: {{ $asset['current_holder'] }}
                    </p>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex gap-2">
                    <a href="{{ route('assets.show', $asset['id']) }}"
                        class="flex-1 text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>

                    @if($user['role'] == 'operator')
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