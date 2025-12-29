@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Back Button -->
    <a href="{{ route('transactions.catalog') }}"
        class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Katalog
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-xl p-8">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-hand-holding text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Ajukan Peminjaman</h1>
                        <p class="text-gray-600">Lengkapi form berikut untuk meminjam aset</p>
                    </div>
                </div>

                <form action="{{ route('transactions.submitRequest') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="asset_id" value="{{ $assetId }}">

                    <!-- Purpose -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                            Tujuan Peminjaman <span class="text-red-500">*</span>
                        </label>
                        <textarea name="purpose" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                            placeholder="Jelaskan untuk apa aset ini akan digunakan..."
                            required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Minimal 10 karakter, maksimal 500 karakter</p>
                    </div>

                    <!-- Requested Location -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                            Lokasi Penggunaan <span class="text-red-500">*</span>
                        </label>
                        <select name="requested_location"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                            required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $locId => $location)
                            <option value="{{ $location['name'] ?? $locId }}"
                                {{ old('requested_location') == ($location['name'] ?? $locId) ? 'selected' : '' }}>
                                {{ $location['name'] ?? $locId }}
                            </option>
                            @endforeach
                        </select>
                        @error('requested_location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Di mana aset akan digunakan</p>
                    </div>

                    <!-- Expected Return Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                            Perkiraan Tanggal Pengembalian <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="expected_return_date" value="{{ old('expected_return_date') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 transition"
                            required>
                        @error('expected_return_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Tanggal harus setelah hari ini</p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="font-medium text-blue-900">Perhatian</p>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1 list-disc list-inside">
                                    <li>Request Anda akan ditinjau oleh operator</li>
                                    <li>Anda akan diberitahu setelah request disetujui</li>
                                    <li>Aset harus dikembalikan sesuai tanggal yang dijanjikan</li>
                                    <li>Pastikan kondisi aset tetap baik saat dikembalikan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" id="termsCheck"
                                class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                required>
                            <span class="text-sm text-gray-700">
                                Saya bertanggung jawab penuh terhadap aset yang dipinjam dan akan mengembalikan dalam
                                kondisi baik sesuai waktu yang dijanjikan.
                            </span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" id="submitBtn"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Request
                        </button>
                        <a href="{{ route('transactions.catalog') }}"
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Asset Summary Sidebar -->
        <div class="space-y-6">
            <!-- Asset Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-24">
                <div class="gradient-bg p-4 text-white">
                    <h3 class="font-bold text-lg">Aset yang Dipilih</h3>
                </div>

                <div class="p-6">
                    <!-- Icon -->
                    <div class="flex items-center justify-center mb-4">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-laptop text-purple-600 text-3xl"></i>
                        </div>
                    </div>

                    <!-- Asset Info -->
                    <h4 class="text-xl font-bold text-gray-800 text-center mb-4">
                        {{ $asset['name'] ?? 'Unknown' }}
                    </h4>

                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-tag text-purple-500 mr-3 w-5"></i>
                            <span class="text-gray-700 capitalize">{{ $asset['category'] ?? '-' }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-barcode text-purple-500 mr-3 w-5"></i>
                            <span class="text-gray-700 font-mono">{{ $asset['serial_number'] ?? '-' }}</span>
                        </div>

                        <div class="flex items-center text-sm">
                            <i class="fas fa-map-marker-alt text-purple-500 mr-3 w-5"></i>
                            <span class="text-gray-700">{{ $asset['location'] ?? '-' }}</span>
                        </div>

                        <div class="pt-3 border-t">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>
                                Available
                            </span>
                        </div>
                    </div>

                    @if(!empty($asset['description']))
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-xs text-gray-500 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-700">{{ $asset['description'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6">
                <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Tips
                </h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                        <span>Jelaskan tujuan peminjaman dengan jelas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                        <span>Tentukan tanggal pengembalian yang realistis</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                        <span>Pastikan lokasi penggunaan sudah benar</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Enable submit button only when terms checked
const termsCheck = document.getElementById('termsCheck');
const submitBtn = document.getElementById('submitBtn');

termsCheck.addEventListener('change', function() {
    submitBtn.disabled = !this.checked;
});

// Set minimum date
document.querySelector('input[type="date"]').min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
</script>
@endsection