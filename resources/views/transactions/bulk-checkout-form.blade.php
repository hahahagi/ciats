@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Checkout Items</h1>
        <p class="text-gray-600">Konfirmasi detail checkout untuk item yang dipilih</p>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Error!</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('transactions.processBulkCheckout') }}" method="POST">
        @csrf
        <input type="hidden" name="asset_id" value="{{ $assetId }}">

        <!-- List Items -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b bg-gray-50">
                <h3 class="font-bold text-gray-700">Daftar Item yang Akan Di-Checkout</h3>
            </div>
            <div class="p-6">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 border-b">
                            <th class="py-2">Item Serial</th>
                            <th class="py-2">Assigned To</th>
                            <th class="py-2">Condition</th>
                            <th class="py-2">Note (Optional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($checkoutItems as $item)
                        <tr>
                            <td class="py-3 font-mono font-medium text-gray-800 align-top">{{ $item['serial'] }}</td>
                            <td class="py-3 align-top">
                                <span class="font-semibold text-gray-700">{{ $item['user_name'] }}</span>
                                <p class="text-xs text-gray-500">{{ $item['purpose'] }}</p>
                                <input type="hidden" name="items[{{ $loop->index }}][serial]" value="{{ $item['serial'] }}">
                                <input type="hidden" name="items[{{ $loop->index }}][tx_id]" value="{{ $item['tx_id'] }}">
                            </td>
                            <td class="py-3 align-top">
                                <select name="items[{{ $loop->index }}][condition]" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 w-full">
                                    <option value="good">Good</option>
                                    <option value="minor_damage">Minor Damage</option>
                                </select>
                            </td>
                            <td class="py-3 align-top">
                                <input type="text" name="items[{{ $loop->index }}][notes]" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 w-full" placeholder="Catatan...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 font-semibold">Batal</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:scale-105">
                <i class="fas fa-check-circle mr-2"></i> Proses Checkout
            </button>
        </div>
    </form>
</div>
@endsection
