@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-file-alt text-purple-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan</h1>
                <p class="text-gray-600">Generate laporan transaksi dan aset</p>
            </div>
        </div>

        <form action="{{ route('reports.generate') }}" method="POST" target="_blank" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date Range -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-01') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Jenis Laporan</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" required>
                        <option value="transactions">Transaksi Peminjaman</option>
                        <option value="assets">Data Aset (Created)</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Status (Opsional)</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                        <option value="">Semua Status</option>
                        <option value="waiting_approval">Waiting Approval</option>
                        <option value="approved">Approved</option>
                        <option value="active">Active (Dipinjam)</option>
                        <option value="completed">Completed (Dikembalikan)</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-print mr-2"></i> Generate Laporan
            </button>
        </form>
    </div>
</div>
@endsection
