@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Checkin Items (Pengembalian)</h1>
        <p class="text-gray-600">Konfirmasi pengembalian aset dari karyawan</p>
    </div>

    <form action="{{ route('transactions.processBulkCheckin') }}" method="POST">
        @csrf
        <input type="hidden" name="asset_id" value="{{ $assetId }}">

        <!-- List Items -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b bg-gray-50">
                <h3 class="font-bold text-gray-700">Daftar Item yang Akan Dikembalikan</h3>
            </div>
            <div class="p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 border-b">
                            <th class="py-2">Item Serial</th>
                            <th class="py-2">Holder</th>
                            <th class="py-2 w-1/5">Condition</th>
                            <th class="py-2 w-1/3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($checkinItems as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 font-mono font-medium text-gray-800 align-top pr-4">
                                {{ $item['serial'] }}
                            </td>
                            <td class="py-4 align-top pr-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-2">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $item['holder'] }}</span>
                                </div>
                                <input type="hidden" name="items[{{ $loop->index }}][serial]" value="{{ $item['serial'] }}">
                                <input type="hidden" name="items[{{ $loop->index }}][tx_id]" value="{{ $item['tx_id'] }}">
                                <input type="hidden" name="items[{{ $loop->index }}][asset_id]" value="{{ $item['asset_id'] }}">
                            </td>
                            <td class="py-4 align-top pr-4">
                                <select name="items[{{ $loop->index }}][condition]" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 w-full mb-1 p-2.5 bg-gray-50 hover:bg-white transition-colors">
                                    <option value="good" class="text-green-600">Good</option>
                                    <option value="minor_damage" class="text-yellow-600">Minor Damage</option>
                                    <option value="damaged" class="text-red-600">Damaged</option>
                                </select>
                            </td>
                            <td class="py-4 align-top">
                                <textarea name="items[{{ $loop->index }}][notes]" rows="2" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 w-full p-2.5 bg-gray-50 hover:bg-white transition-colors" placeholder="Tambahkan catatan (opsional)..."></textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transform transition hover:-translate-y-0.5 w-full md:w-auto">
                    <i class="fas fa-check-circle mr-2"></i> Konfirmasi Checkin
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
