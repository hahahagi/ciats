{{-- CREATE: assets/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Back Button -->
    <a href="{{ route('assets.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Aset
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-plus-circle text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Aset Baru</h1>
                <p class="text-gray-600">Lengkapi informasi aset yang akan ditambahkan</p>
            </div>
        </div>

        <form action="{{ route('assets.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-box text-blue-600 mr-2"></i>
                    Nama Aset <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    placeholder="Contoh: Laptop Dell XPS 15" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Serial Number -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-tag text-blue-600 mr-2"></i>
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition capitalize"
                        required>
                        <option value="">Pilih Kategori</option>
                        <option value="laptop" {{ old('category') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                        <option value="monitor" {{ old('category') == 'monitor' ? 'selected' : '' }}>Monitor</option>
                        <option value="keyboard" {{ old('category') == 'keyboard' ? 'selected' : '' }}>Keyboard</option>
                        <option value="mouse" {{ old('category') == 'mouse' ? 'selected' : '' }}>Mouse</option>
                        <option value="printer" {{ old('category') == 'printer' ? 'selected' : '' }}>Printer</option>
                        <option value="scanner" {{ old('category') == 'scanner' ? 'selected' : '' }}>Scanner</option>
                        <option value="projector" {{ old('category') == 'projector' ? 'selected' : '' }}>Projector
                        </option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-barcode text-blue-600 mr-2"></i>
                        Serial Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition font-mono"
                        placeholder="SN123456789" required>
                    @error('serial_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Lokasi <span class="text-red-500">*</span>
                </label>
                <select name="location"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    required>
                    <option value="">Pilih Lokasi</option>
                    @foreach($locations as $locId => $location)
                    <option value="{{ $location['name'] ?? $locId }}"
                        {{ old('location') == ($location['name'] ?? $locId) ? 'selected' : '' }}>
                        {{ $location['name'] ?? $locId }}
                    </option>
                    @endforeach
                </select>
                @error('location')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-align-left text-blue-600 mr-2"></i>
                    Deskripsi
                </label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    placeholder="Deskripsi tambahan tentang aset...">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Opsional - Maksimal 500 karakter</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
                    <div>
                        <p class="font-medium text-blue-900">QR Code akan digenerate otomatis</p>
                        <p class="text-sm text-blue-700 mt-1">Setelah aset ditambahkan, QR Code akan dibuat secara
                            otomatis dan dapat diprint.</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Aset
                </button>
                <a href="{{ route('assets.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection