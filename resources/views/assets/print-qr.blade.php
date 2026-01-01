<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code - {{ $asset['name'] ?? 'Asset' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @media print {
        @page {
            size: auto;
            margin: 0mm;
        }

        .no-print {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
            background: white;
        }

        .print-area {
            width: 100%;
            /* Use slightly less than 100vh to avoid accidental spillover */
            min-height: 95vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            page-break-inside: avoid;
            box-shadow: none !important;
            padding: 20px !important; /* Override p-12 */
        }

        /* Reset shadow and max-width for print */
        .shadow-xl {
            box-shadow: none !important;
        }
        .max-w-4xl {
            max-width: none !important;
            margin: 0 !important;
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

            <div class="flex gap-3">
                <button onclick="downloadJPG()"
                    class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-image mr-2"></i>
                    Download JPG
                </button>

                <button onclick="window.print()"
                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-print mr-2"></i>
                    Print PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Print Area -->
    <div id="printArea" class="max-w-4xl mx-auto bg-white shadow-xl">

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
                <img id="qrImage" src="{{ $asset['qr_code_url'] }}" alt="QR Code" class="mx-auto"
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

    <!-- Hidden Capture Area for JPG -->
    <div id="jpgCaptureArea" style="position: absolute; left: -9999px; top: 0; width: 400px; background: white; padding: 20px; border-radius: 12px; text-align: center; font-family: sans-serif;">
        <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 24px; background: white;">
            <h2 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 4px;">CIATS</h2>
            <p style="color: #6b7280; font-size: 12px; margin-bottom: 20px;">Corporate IT Asset Tracking System</p>

            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                @if(!empty($asset['qr_code_url']))
                <img src="{{ $asset['qr_code_url'] }}" alt="QR Code" style="width: 200px; height: 200px;">
                @else
                <div style="width: 200px; height: 200px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <span style="color: #9ca3af;">No QR</span>
                </div>
                @endif
            </div>

            <div style="text-align: left; background: #f9fafb; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                <div style="margin-bottom: 12px;">
                    <p style="font-size: 10px; color: #6b7280; margin-bottom: 2px;">Asset Name</p>
                    <p style="font-size: 16px; font-weight: bold; color: #1f2937; margin: 0;">{{ $asset['name'] ?? 'Unknown Asset' }}</p>
                </div>
                <div>
                    <p style="font-size: 10px; color: #6b7280; margin-bottom: 2px;">Serial Number</p>
                    <p style="font-size: 14px; font-family: monospace; font-weight: bold; color: #1f2937; margin: 0;">{{ $asset['serial_number'] ?? '-' }}</p>
                </div>
            </div>

            <div style="font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 12px;">
                Generated: {{ date('d M Y') }}
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
    function downloadJPG() {
        // Use the dedicated capture area
        const captureArea = document.getElementById('jpgCaptureArea');

        if (!captureArea) {
            alert('Area capture tidak ditemukan!');
            return;
        }

        // Show loading state
        const btn = document.querySelector('button[onclick="downloadJPG()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';
        btn.disabled = true;

        // Ensure html2canvas is loaded
        if (typeof html2canvas === 'undefined') {
            alert('Library html2canvas belum siap. Mohon tunggu sebentar atau refresh halaman.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }

        // Move the capture area to the visible viewport temporarily to ensure rendering
        // But keep it hidden from user flow if possible, or just use the off-screen positioning
        // html2canvas works best if the element is in the DOM.
        // It is already in the DOM (absolute positioned off-screen).

        html2canvas(captureArea, {
            scale: 3, // High quality
            backgroundColor: null, // Transparent background for the canvas itself (element has white)
            useCORS: true,
            logging: false,
            allowTaint: true,
            windowWidth: 500, // Force a width context
        }).then(canvas => {
            try {
                // Convert to JPG
                const jpgUrl = canvas.toDataURL('image/jpeg', 0.95);

                // Trigger download
                const link = document.createElement('a');
                link.download = 'Asset-{{ $asset["serial_number"] ?? "QR" }}.jpg';
                link.href = jpgUrl;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (e) {
                console.error('Error saving image:', e);
                alert('Gagal menyimpan gambar: ' + e.message);
            }

            // Restore button
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            console.error('Error generating JPG:', err);
            alert('Gagal membuat gambar JPG: ' + err.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
    </script>
</body>

</html>
