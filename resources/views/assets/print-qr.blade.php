<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $asset['name'] ?? 'Asset' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media print {
        .no-print {
            display: none;
        }

        body {
            margin: 0;
            padding: 20px;
        }

        .print-area {
            page-break-inside: avoid;
        }
    }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Action Buttons (No Print) -->
    <div class="no-print max-w-4xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('assets.show', $assetId) }}"
                class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Detail Aset
            </a>

            <button onclick="window.print()"
                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                <i class="fas fa-print mr-2"></i>
                Print QR Code
            </button>
        </div>
    </div>

    <!-- Print Area -->
    <div class="max-w-4xl mx-auto bg-white shadow-xl">

        <!-- Single QR Label (Full Page) -->
        <div class="print-area p-12 text-center">

            <!-- Company Header -->
            <div class="mb-8 pb-6 border-b-2 border-gray-200">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">CIATS</h1>
                <p class="text-gray-600">Corporate IT Asset Tracking System</p>
            </div>

            <!-- QR Code -->
            <div class="mb-8">
                @if(!empty($asset['qr_code_url']))
                <img src="{{ $asset['qr_code_url'] }}" alt="QR Code" class="mx-auto"
                    style="width: 300px; height: 300px;">
                @else
                <div class="w-72 h-72 mx-auto bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-qrcode text-gray-400 text-6xl mb-3"></i>
                        <p class="text-gray-500">QR Code Not Available</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Asset Information -->
            <div class="space-y-4 text-left max-w-md mx-auto">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Asset Name</p>
                    <p class="text-xl font-bold text-gray-800">{{ $asset['name'] ?? 'Unknown Asset' }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1">Serial Number</p>
                    <p class="text-lg font-mono font-bold text-gray-800">{{ $asset['serial_number'] ?? '-' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Category</p>
                        <p class="font-medium text-gray-800 capitalize">{{ $asset['category'] ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Location</p>
                        <p class="font-medium text-gray-800">{{ $asset['location'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-6 border-t-2 border-gray-200 text-sm text-gray-500">
                <p>Scan this QR code to view asset details</p>
                <p class="mt-2">Generated: {{ date('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Small Labels Grid (Commented - Alternative Layout) -->
        <!--
        <div class="grid grid-cols-2 gap-8 p-8">
            @for($i = 0; $i < 6; $i++)
            <div class="print-area border-2 border-dashed border-gray-300 p-6 text-center">
                <div class="mb-3">
                    @if(!empty($asset['qr_code_url']))
                    <img src="{{ $asset['qr_code_url'] }}" 
                         alt="QR Code" 
                         class="mx-auto"
                         style="width: 150px; height: 150px;">
                    @endif
                </div>
                
                <p class="font-bold text-sm mb-1">{{ $asset['name'] ?? 'Unknown' }}</p>
                <p class="text-xs font-mono text-gray-600">{{ $asset['serial_number'] ?? '-' }}</p>
            </div>
            @endfor
        </div>
        -->
    </div>

    <!-- Instructions (No Print) -->
    <div class="no-print max-w-4xl mx-auto p-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <h3 class="font-bold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Tips untuk Print yang Optimal
            </h3>
            <ul class="list-disc list-inside space-y-2 text-sm text-blue-800">
                <li>Gunakan kertas ukuran A4 atau Letter</li>
                <li>Pastikan printer dalam mode kualitas tinggi</li>
                <li>Gunakan kertas sticker untuk kemudahan pemasangan</li>
                <li>Test scan QR code setelah di-print untuk memastikan readable</li>
                <li>Lindungi QR code dengan laminating agar tahan lama</li>
            </ul>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center space-x-3 mb-2">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    <span class="font-semibold text-gray-800">Recommended Paper</span>
                </div>
                <p class="text-gray-600">Glossy sticker paper untuk hasil terbaik</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center space-x-3 mb-2">
                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                    <span class="font-semibold text-gray-800">Protection</span>
                </div>
                <p class="text-gray-600">Laminate untuk perlindungan ekstra</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center space-x-3 mb-2">
                    <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                    <span class="font-semibold text-gray-800">Placement</span>
                </div>
                <p class="text-gray-600">Tempel di tempat yang mudah terlihat</p>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>

</html>