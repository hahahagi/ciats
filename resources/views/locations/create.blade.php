@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <!-- Back Button -->
    <a href="{{ route('locations.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Lokasi
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-plus-circle text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Lokasi Baru</h1>
                <p class="text-gray-600">Tambahkan lokasi penyimpanan aset baru</p>
            </div>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-tag text-blue-600 mr-2"></i>
                    Nama Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    placeholder="Contoh: Ruang Server Lt. 2" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-align-left text-blue-600 mr-2"></i>
                    Deskripsi
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    placeholder="Deskripsi singkat tentang lokasi ini...">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 hover:shadow-lg transition transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
