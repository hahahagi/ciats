@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Item Aset</h1>
            <a href="{{ route('assets.show', $assetId) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </a>
        </div>

        <div class="mb-6 p-4 bg-purple-50 rounded-lg">
            <p class="text-sm text-purple-700 font-bold">Aset Induk</p>
            <p class="text-lg text-gray-800">{{ $asset['name'] }}</p>
            <p class="text-xs text-gray-600 font-mono">{{ $assetId }}</p>
        </div>

        <form action="{{ route('assets.updateItem', ['id' => $assetId, 'itemId' => $itemId]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Serial Number -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $item['serial_number']) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 font-mono"
                    required>
                @error('serial_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Condition -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">Kondisi</label>
                <select name="condition" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    <option value="good" {{ $item['condition'] == 'good' ? 'selected' : '' }}>Good (Baik)</option>
                    <option value="minor_damage" {{ $item['condition'] == 'minor_damage' ? 'selected' : '' }}>Minor Damage (Rusak Ringan)</option>
                    <option value="major_damage" {{ $item['condition'] == 'major_damage' ? 'selected' : '' }}>Major Damage (Rusak Berat)</option>
                </select>
                @error('condition')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">Status</label>
                <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    <option value="available" {{ $item['status'] == 'available' ? 'selected' : '' }}>Available (Tersedia)</option>
                    <option value="in_use" {{ $item['status'] == 'in_use' ? 'selected' : '' }}>In Use (Sedang Dipakai)</option>
                    <option value="maintenance" {{ $item['status'] == 'maintenance' ? 'selected' : '' }}>Maintenance (Perbaikan)</option>
                    <option value="damaged" {{ $item['status'] == 'damaged' ? 'selected' : '' }}>Damaged (Rusak)</option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="w-full py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition shadow-lg">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
