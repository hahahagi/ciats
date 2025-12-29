{{-- CREATE: assets/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Back Button -->
    <a href="{{ route('assets.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Aset
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-plus-circle text-purple-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Aset Baru</h1>
                <p class="text-gray-600">Lengkapi informasi aset yang akan ditambahkan</p>
            </div>
        </div>

        <form action="{{ route('assets.store') }}" method="POST" class="space-y-6">
            @csrf
{{-- EDIT: assets/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route('assets.show', $assetId) }}"
        class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Detail Aset
    </a>

    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-edit text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Aset</h1>
                <p class="text-gray-600">Update informasi aset</p>
            </div>
        </div>

        <form action="{{ route('assets.update', $assetId) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-box text-purple-600 mr-2"></i>
                    Nama Aset <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $asset['name'] ?? '') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                    required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Serial -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-tag text-purple-600 mr-2"></i>
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition capitalize"
                        required>
                        <option value="">Pilih Kategori</option>
                        @foreach(['laptop', 'monitor', 'keyboard', 'mouse', 'printer', 'scanner', 'projector', 'other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $asset['category'] ?? '') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                        @endforeach
                    </select>
                    @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-barcode text-purple-600 mr-2"></i>
                        Serial Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset['serial_number'] ?? '') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition font-mono"
                        required>
                    @error('serial_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <select name="location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition" required>
                        <option value="">Pilih Lokasi</option>
                        @foreach($locations as $locId => $location)
                        <option value="{{ $location['name'] ?? $locId }}" {{ old('location', $asset['location'] ?? '') == ($location['name'] ?? $locId) ? 'selected' : '' }}>
                            {{ $location['name'] ?? $locId }}
                        </option>
                        @endforeach
                    </select>
                    @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition" required>
                        <option value="available" {{ old('status', $asset['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="maintenance" {{ old('status', $asset['status'] ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="damaged" {{ old('status', $asset['status'] ?? '') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-align-left text-purple-600 mr-2"></i>
                    Deskripsi
                </label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition">{{ old('description', $asset['description'] ?? '') }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location Change Notes (if location changed) -->
            <div id="locationChangeNote" class="hidden">
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-comment text-purple-600 mr-2"></i>
                    Catatan Perubahan Lokasi
                </label>
                <input type="text" name="location_change_notes" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition" placeholder="Alasan perubahan lokasi...">
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Update Aset
                </button>
                <a href="{{ route('assets.show', $assetId) }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const originalLocation = "{{ $asset['location'] ?? '' }}";
const locationSelect = document.querySelector('select[name="location"]');
const locationChangeNote = document.getElementById('locationChangeNote');

locationSelect.addEventListener('change', function() {
    if (this.value !== originalLocation && this.value !== '') {
        locationChangeNote.classList.remove('hidden');
    } else {
        locationChangeNote.classList.add('hidden');
    }
});
</script>
@endsection