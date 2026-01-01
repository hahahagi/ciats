@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="gradient-bg rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="bg-white bg-opacity-20 p-4 rounded-xl">
                <i class="fas fa-qrcode text-4xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2">QR Code Scanner</h1>
                <p class="text-purple-100">Scan QR code untuk akses cepat informasi aset</p>
            </div>
        </div>
    </div>

    <!-- Scanner Interface -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <!-- Scanner Area -->
        <div id="scannerArea" class="relative bg-gray-900 overflow-hidden flex justify-center items-center" style="height: 400px; max-height: 400px;">
            <div id="reader" class="w-full h-full object-cover"></div>

            <!-- Scan Frame Overlay (Visible when scanning) -->
            <div id="scanFrame" class="hidden absolute inset-0 pointer-events-none flex items-center justify-center">
                <div class="relative w-64 h-64 border-2 border-blue-500 bg-transparent shadow-[0_0_0_9999px_rgba(0,0,0,0.5)] rounded-lg">
                    <!-- Corner Markers -->
                    <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-blue-400 -mt-1 -ml-1"></div>
                    <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-blue-400 -mt-1 -mr-1"></div>
                    <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-blue-400 -mb-1 -ml-1"></div>
                    <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-blue-400 -mb-1 -mr-1"></div>
                    <!-- Scanning Line Animation -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-red-500 shadow-[0_0_10px_rgba(255,0,0,0.5)] animate-scan-line"></div>
                </div>
            </div>

            <!-- Start Overlay -->
            <div id="startOverlay" class="absolute inset-0 flex items-center justify-center bg-gray-900 z-10">
                <div class="text-center text-white p-8">
                    <i class="fas fa-qrcode text-6xl mb-4 opacity-50"></i>
                    <p class="text-lg mb-4">Scanner Ready</p>
                    <div class="flex flex-col gap-3">
                        <button id="startScanBtn" onclick="startScanner()"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-camera mr-2"></i>
                            Start Camera Scan
                        </button>

                        <div class="relative">
                            <input type="file" id="qrInputFile" accept="image/*" class="hidden" onchange="scanFromFile(this)">
                            <button onclick="document.getElementById('qrInputFile').click()"
                                class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition font-semibold w-full">
                                <i class="fas fa-file-image mr-2"></i>
                                Scan from Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Result -->
        <div class="p-6">
            <div id="statusArea" class="mb-4">
                <div class="flex items-center space-x-3 text-gray-600">
                    <i class="fas fa-info-circle"></i>
                    <span>Posisikan QR code di depan kamera</span>
                </div>
            </div>

            <!-- Result Area -->
            <div id="resultArea" class="hidden">
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                    <p class="font-semibold text-green-900 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>
                        QR Code Detected!
                    </p>
                    <p class="text-sm text-green-700" id="resultText"></p>
                </div>

                <div id="assetInfo" class="hidden bg-gray-50 rounded-lg p-4">
                    <h3 class="font-bold text-gray-800 mb-3">Asset Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-medium" id="assetName">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Serial:</span>
                            <span class="font-mono font-medium" id="assetSerial">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium" id="assetStatus">-</span>
                        </div>
                    </div>

                    <a id="viewAssetBtn" href="#"
                        class="mt-4 block text-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-eye mr-2"></i>
                        View Full Details
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-4">
                <button id="stopScanBtn" onclick="stopScanner()"
                    class="hidden flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    <i class="fas fa-stop mr-2"></i>
                    Stop Scanner
                </button>

                <button onclick="resetScanner()"
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
        <h3 class="font-bold text-blue-900 mb-3">
            <i class="fas fa-lightbulb mr-2"></i>
            Cara Menggunakan Scanner
        </h3>
        <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
            <li>Klik tombol "Start Scanner" untuk mengaktifkan kamera</li>
            <li>Izinkan akses kamera pada browser Anda</li>
            <li>Posisikan QR code aset di depan kamera</li>
            <li>Scanner akan otomatis membaca QR code</li>
            <li>Informasi aset akan ditampilkan setelah scan berhasil</li>
        </ol>
    </div>

    <!-- Recent Activity -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h3>

        <div class="space-y-3">
            @forelse($recentTransactions as $tx)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-100 p-2 rounded-full">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $tx['asset_name'] ?? 'Unknown Asset' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ ucfirst($tx['status']) }} by {{ $tx['user_name'] ?? 'Unknown' }} •
                            {{ \Carbon\Carbon::createFromTimestamp($tx['updated_at'] ?? time())->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded
                    @if($tx['status'] == 'approved') bg-green-100 text-green-700
                    @elseif($tx['status'] == 'pending') bg-yellow-100 text-yellow-700
                    @elseif($tx['status'] == 'rejected') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst($tx['status']) }}
                </span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Belum ada aktivitas terbaru.</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
@keyframes scan-line {
    0% { top: 0; }
    50% { top: 100%; }
    100% { top: 0; }
}

.animate-scan-line {
    animation: scan-line 2s linear infinite;
}

/* Fix for scanner height issue */
#reader img {
    max-height: 400px !important;
    object-fit: contain;
}
</style>

<script>
let html5QrCode = null;

function startScanner() {
    // Use a more robust config
    const config = {
        fps: 10,
        // qrbox: { width: 250, height: 250 }, // Removed explicit box to allow full frame scanning
        aspectRatio: 1.0,
        disableFlip: false,
        focusMode: "continuous" // Try to force continuous focus if supported
    };

    // Hide overlay
    document.getElementById('startOverlay').classList.add('hidden');
    document.getElementById('stopScanBtn').classList.remove('hidden');
    document.getElementById('scanFrame').classList.remove('hidden');

    html5QrCode = new Html5Qrcode("reader");

    updateStatus('Starting camera...', 'info');

    // Try to get cameras first to ensure we have permission
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            // Use the first camera or the back camera if available
            // const cameraId = devices[0].id; // Not used directly, relying on facingMode

            html5QrCode.start(
                { facingMode: "environment" }, // Prefer back camera
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                updateStatus('Scanning... Posisikan QR code di dalam frame', 'info');

                // Apply video constraints for better focus if possible
                // This is a bit of a hack as html5-qrcode handles this internally usually
                // but sometimes explicit constraints help on mobile
            }).catch(err => {
                console.error("Error starting scanner", err);
                updateStatus('Gagal mengakses kamera: ' + err, 'error');
                stopScanner();
            });
        } else {
            updateStatus('Tidak ada kamera yang ditemukan', 'error');
            stopScanner();
        }
    }).catch(err => {
        updateStatus('Permission denied or error: ' + err, 'error');
        stopScanner();
    });
}

function scanFromFile(input) {
    if (input.files.length === 0) {
        return;
    }

    const imageFile = input.files[0];

    // Hide overlay temporarily
    document.getElementById('startOverlay').classList.add('hidden');
    updateStatus('Processing image...', 'info');

    // Create a new instance if not exists
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("reader");
    }

    html5QrCode.scanFile(imageFile, true)
        .then(decodedText => {
            // success
            console.log(`Scan result from file: ${decodedText}`);
            handleScanResult(decodedText);

            // Clear the reader to remove the large image preview
            if (html5QrCode) {
                html5QrCode.clear();
            }

            document.getElementById('startOverlay').classList.remove('hidden');
        })
        .catch(err => {
            // failure
            console.error(`Error scanning file. Reason: ${err}`);
            updateStatus('Gagal membaca QR Code dari gambar. Pastikan gambar jelas.', 'error');

            if (html5QrCode) {
                html5QrCode.clear();
            }

            document.getElementById('startOverlay').classList.remove('hidden');
        });
}

function onScanSuccess(decodedText, decodedResult) {
    // Handle on success condition with the decoded message.
    console.log(`Scan result: ${decodedText}`, decodedResult);

    // Stop scanning immediately to prevent duplicate reads
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            html5QrCode = null;
            document.getElementById('scanFrame').classList.add('hidden');
            document.getElementById('stopScanBtn').classList.add('hidden');
        }).catch(err => console.error(err));
    }

    handleScanResult(decodedText);
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // console.warn(`Code scan error = ${error}`);
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then((ignore) => {
            // QR Code scanning is stopped.
            html5QrCode.clear();
            html5QrCode = null;
        }).catch((err) => {
            // Stop failed, handle it.
            console.error("Failed to stop scanner", err);
        });
    }

    document.getElementById('startOverlay').classList.remove('hidden');
    document.getElementById('stopScanBtn').classList.add('hidden');
    document.getElementById('scanFrame').classList.add('hidden');

    updateStatus('Scanner stopped', 'info');
}

function resetScanner() {
    stopScanner();
    document.getElementById('resultArea').classList.add('hidden');
    document.getElementById('assetInfo').classList.add('hidden');
    updateStatus('Posisikan QR code di depan kamera', 'info');
}

async function handleScanResult(qrData) {
    updateStatus('Processing data...', 'info');

    try {
        const response = await fetch('{{ route("scanner.handle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' // Explicitly ask for JSON
            },
            body: JSON.stringify({ data: qrData })
        });

        // Check if response is ok first
        if (!response.ok) {
            // Try to get error message from JSON
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const errResult = await response.json();
                throw new Error(errResult.error || 'Server Error ' + response.status);
            } else {
                const text = await response.text();
                console.error("Server Error HTML:", text);
                throw new Error("Server returned non-JSON response (" + response.status + ")");
            }
        }

        const result = await response.json();

        if (result.type === 'asset') {
            // Show SweetAlert Popup
            Swal.fire({
                title: 'Asset Found!',
                html: `
                    <div class="text-left">
                        <p class="mb-2"><strong>Name:</strong> ${result.asset.name}</p>
                        <p class="mb-2"><strong>Serial:</strong> ${result.asset.serial_number}</p>
                        <p class="mb-2"><strong>Status:</strong> ${result.asset.status}</p>
                    </div>
                `,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'View Details',
                cancelButtonText: 'Scan Again',
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280'
            }).then((swalResult) => {
                if (swalResult.isConfirmed) {
                    window.location.href = result.redirect_url;
                } else {
                    resetScanner();
                }
            });
        }

        showResult(result);
        updateStatus('Scan berhasil!', 'success');

    } catch (error) {
        console.error('Error:', error);
        updateStatus('Terjadi kesalahan: ' + error.message, 'error');

        Swal.fire({
            title: 'Error',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

function showResult(data) {
    document.getElementById('resultArea').classList.remove('hidden');
    document.getElementById('resultText').textContent = 'Asset found: ' + (data.asset.name || 'Unknown');

    if (data.type === 'asset') {
        document.getElementById('assetInfo').classList.remove('hidden');
        document.getElementById('assetName').textContent = data.asset.name;
        document.getElementById('assetSerial').textContent = data.asset.serial_number;
        document.getElementById('assetStatus').textContent = data.asset.status;
        document.getElementById('viewAssetBtn').href = data.redirect_url;
    }
}

function updateStatus(message, type = 'info') {
    const statusArea = document.getElementById('statusArea');
    const icons = {
        info: 'fa-info-circle text-blue-600',
        error: 'fa-exclamation-circle text-red-600',
        success: 'fa-check-circle text-green-600'
    };

    statusArea.innerHTML = `
            <div class="flex items-center space-x-3 text-gray-600">
                <i class="fas ${icons[type]}"></i>
                <span>${message}</span>
            </div>
        `;

    // Scroll to status area to ensure user sees the message
    if (type === 'error' || type === 'success') {
        statusArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.error(err));
    }
});
</script>
@endsection
