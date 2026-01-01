{{-- CHECKIN FORM: transactions/checkin-form.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route('transactions.activeLoans') }}"
        class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>

    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-redo text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Checkin Barang</h1>
                <p class="text-gray-600">Terima barang yang dikembalikan</p>
            </div>
        </div>

        <!-- Asset & User Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-700 font-semibold mb-2">ASET</p>
                <p class="font-bold text-gray-800 text-lg">{{ $asset['name'] ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-600 font-mono">{{ $asset['serial_number'] ?? '-' }}</p>
            </div>

            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-700 font-semibold mb-2">DIKEMBALIKAN OLEH</p>
                <p class="font-bold text-gray-800 text-lg">{{ $transaction['user_name'] ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-600">{{ $transaction['user_email'] ?? '-' }}</p>
            </div>
        </div>

        <!-- Loan Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Checkout Date</p>
                    <p class="font-medium">{{ date('d M Y', $transaction['checkout_at'] ?? time()) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Expected Return</p>
                    <p class="font-medium">{{ date('d M Y', $transaction['expected_return_date'] ?? time()) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Condition Before</p>
                    <p class="font-medium capitalize">{{ $transaction['condition_before'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('transactions.processCheckin', $transactionId) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Condition After Return -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>
                    Kondisi Setelah Dikembalikan <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label
                        class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="condition" value="good" class="w-5 h-5 text-green-600" required>
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">Good Condition</p>
                            <p class="text-sm text-gray-600">Barang dikembalikan dalam kondisi baik</p>
                        </div>
                    </label>

                    <label
                        class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="condition" value="minor_damage" class="w-5 h-5 text-orange-600">
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">Minor Damage</p>
                            <p class="text-sm text-gray-600">Ada kerusakan kecil</p>
                        </div>
                    </label>

                    <label
                        class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="condition" value="major_damage" class="w-5 h-5 text-red-600">
                        <div class="ml-3">
                            <p class="font-medium text-gray-800">Major Damage</p>
                            <p class="text-sm text-gray-600">Ada kerusakan signifikan</p>
                        </div>
                    </label>
                </div>
                @error('condition')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Damage Notes -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-comment text-purple-600 mr-2"></i>
                    Catatan Kerusakan / Kondisi
                </label>
                <textarea name="notes" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                    placeholder="Jelaskan kondisi atau kerusakan jika ada..."></textarea>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Jika ada kerusakan, pastikan sudah didokumentasikan dan dilaporkan.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-check mr-2"></i>
                    Proses Checkin
                </button>
                <a href="{{ route('transactions.activeLoans') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
