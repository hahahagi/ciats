<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ ucfirst($type) }} - CIATS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white p-8">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 border-b-2 border-gray-800 pb-4">
            <div class="flex items-center space-x-4">
                <div class="text-4xl font-bold text-blue-600">CIATS</div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Laporan {{ ucfirst($type) }}</h1>
                    <p class="text-sm text-gray-600">Periode: {{ date('d M Y', strtotime($start_date)) }} - {{ date('d M Y', strtotime($end_date)) }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Dicetak oleh: {{ $user['name'] }}</p>
                <p class="text-sm text-gray-600">Tanggal: {{ date('d M Y H:i') }}</p>
            </div>
        </div>

        <!-- Content -->
        <table class="w-full text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-gray-800 text-sm uppercase">
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                    @if($type == 'transactions')
                    <th class="border border-gray-300 px-4 py-2">Peminjam</th>
                    <th class="border border-gray-300 px-4 py-2">Aset</th>
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                    @else
                    <th class="border border-gray-300 px-4 py-2">Nama Aset</th>
                    <th class="border border-gray-300 px-4 py-2">Kategori</th>
                    <th class="border border-gray-300 px-4 py-2">Lokasi</th>
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @forelse($data as $index => $item)
                <tr>
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        {{ date('d M Y H:i', $type == 'transactions' ? ($item['requested_at'] ?? 0) : ($item['created_at'] ?? 0)) }}
                    </td>
                    @if($type == 'transactions')
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="font-semibold">{{ $item['user_name'] ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $item['user_email'] ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="font-semibold">{{ $item['asset_name'] ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $item['asset_serial'] ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            {{ ($item['status'] ?? '') == 'completed' ? 'bg-green-100 text-green-800' :
                               (($item['status'] ?? '') == 'active' ? 'bg-blue-100 text-blue-800' :
                               (($item['status'] ?? '') == 'approved' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($item['status'] ?? '-') }}
                        </span>
                    </td>
                    @else
                    <td class="border border-gray-300 px-4 py-2">
                        <div class="font-semibold">{{ $item['name'] ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $item['serial_number'] ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-4 py-2 capitalize">{{ $item['category'] ?? '-' }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $item['location'] ?? '-' }}</td>
                    <td class="border border-gray-300 px-4 py-2 capitalize">{{ str_replace('_', ' ', $item['status'] ?? '-') }}</td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border border-gray-300 px-4 py-8 text-center text-gray-500">Tidak ada data ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-8 no-print text-center">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
        </div>
    </div>

</body>
</html>
