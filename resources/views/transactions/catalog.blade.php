@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                <i class="fas fa-shopping-cart text-4xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">Katalog Aset</h1>
                <p class="text-purple-100">Pilih aset yang ingin Anda pinjam</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari aset yang Anda butuhkan..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <select id="categoryFilter"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $categoryName => $items)
                <option value="{{ strtolower($categoryName) }}">{{ $categoryName }} ({{ count($items) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Total Available</p>
            <p class="text-2xl font-bold text-green-600">
                {{ collect($categories)->flatten(1)->count() }}
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">Categories</p>
            <p class="text-2xl font-bold text-purple-600">{{ count($categories) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">My Active</p>
            <p class="text-2xl font-bold text-blue-600">2</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm mb-1">My Pending</p>
            <p class="text-2xl font-bold text-orange-600">1</p>
        </div>
    </div>

    <!-- Categories -->
    @forelse($categories as $categoryName => $assets)
    <div class="category-section mb-8" data-category="{{ strtolower($categoryName) }}">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-folder text-purple-600 mr-3"></i>
                {{ $categoryName }}
                <span class="ml-3 px-3 py-1 bg-purple-100 text-purple-700 text-sm rounded-full">
                    {{ count($assets) }} items
                </span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($assets as $asset)
            <div class="asset-item bg-white rounded-xl shadow-lg overflow-hidden card-hover"
                data-name="{{ strtolower($asset['name'] ?? '') }}" data-category="{{ strtolower($categoryName) }}">

                <!-- Image/Icon -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 flex items-center justify-center">
                    <div class="w-24 h-24 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                        @php
                        $icons = [
                        'laptop' => 'fa-laptop',
                        'monitor' => 'fa-desktop',
                        'keyboard' => 'fa-keyboard',
                        'mouse' => 'fa-mouse',
                        'printer' => 'fa-print',
                        'scanner' => 'fa-scanner',
                        'projector' => 'fa-video'
                        ];
                        $icon = $icons[strtolower($asset['category'] ?? 'other')] ?? 'fa-box';
                        @endphp
                        <i class="fas {{ $icon }} text-purple-600 text-4xl"></i>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $asset['name'] ?? 'Unknown' }}</h3>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-barcode text-purple-500 mr-2 w-4"></i>
                            <span class="font-mono">{{ $asset['serial_number'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-purple-500 mr-2 w-4"></i>
                            <span>{{ $asset['location'] ?? 'Unknown' }}</span>
                        </div>

                        @if(!empty($asset['description']))
                        <div class="flex items-start text-sm text-gray-600 mt-2">
                            <i class="fas fa-info-circle text-purple-500 mr-2 w-4 mt-0.5"></i>
                            <span class="line-clamp-2">{{ $asset['description'] }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>
                            Tersedia
                        </span>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('transactions.requestForm', $asset['id']) }}"
                        class="block text-center px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-hand-holding mr-2"></i>
                        Ajukan Peminjaman
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl shadow-lg">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Aset Tersedia</h3>
        <p class="text-gray-500">Saat ini tidak ada aset yang dapat dipinjam</p>
    </div>
    @endforelse

    <!-- No Results Message -->
    <div id="noResults" class="hidden text-center py-16 bg-white rounded-xl shadow-lg">
        <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Hasil</h3>
        <p class="text-gray-500">Coba ubah kata kunci atau filter pencarian Anda</p>
    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const noResults = document.getElementById('noResults');

function filterAssets() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categoryFilter.value.toLowerCase();

    const sections = document.querySelectorAll('.category-section');
    let totalVisible = 0;

    sections.forEach(section => {
        const sectionCategory = section.dataset.category;
        const items = section.querySelectorAll('.asset-item');
        let sectionVisible = 0;

        // Check if section matches category filter
        if (selectedCategory && sectionCategory !== selectedCategory) {
            section.style.display = 'none';
            return;
        }

        items.forEach(item => {
            const name = item.dataset.name;
            const matchesSearch = !searchTerm || name.includes(searchTerm);

            if (matchesSearch) {
                item.style.display = 'block';
                sectionVisible++;
                totalVisible++;
            } else {
                item.style.display = 'none';
            }
        });

        section.style.display = sectionVisible > 0 ? 'block' : 'none';
    });

    // Show/hide no results
    noResults.style.display = totalVisible === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', filterAssets);
categoryFilter.addEventListener('change', filterAssets);
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection