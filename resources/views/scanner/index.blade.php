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
        <div id="scannerArea" class="relative bg-gray-900 aspect-video flex items-center justify-center">
            <div class="text-center text-white p-8">
                <i class="fas fa-qrcode text-6xl mb-4 opacity-50"></i>
                <p class="text-lg mb-4">Scanner Ready</p>
                <button id="startScanBtn" onclick="startScanner()"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-camera mr-2"></i>
                    Start Scanner
                </button>
            </div>

            <!-- Video element (hidden initially) -->
            <video id="scannerVideo" class="hidden w-full h-full object-cover"></video>

            <!-- Scanning overlay -->
            <div id="scanOverlay" class="hidden absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 border-4 border-blue-500 animate-pulse"></div>
                <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-red-500 animate-scan"></div>
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

    <!-- Recent Scans -->
    <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Scans</h3>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-laptop text-blue-600"></i>
                    <div>
                        <p class="font-medium text-gray-800">Laptop Dell XPS 15</p>
                        <p class="text-xs text-gray-500">5 minutes ago</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-desktop text-blue-600"></i>
                    <div>
                        <p class="font-medium text-gray-800">Monitor LG 27"</p>
                        <p class="text-xs text-gray-500">1 hour ago</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-keyboard text-blue-600"></i>
                    <div>
                        <p class="font-medium text-gray-800">Keyboard Mechanical</p>
                        <p class="text-xs text-gray-500">3 hours ago</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scan {

    0%,
    100% {
        transform: translateY(-100%);
    }

    50% {
        transform: translateY(100%);
    }
}

.animate-scan {
    animation: scan 2s ease-in-out infinite;
}
</style>

<script>
let stream = null;
let scanning = false;
// Set to true only for local/dev testing to simulate a QR scan
const ALLOW_SIMULATE_QR = false;

async function startScanner() {
    try {
        // Request camera access
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment'
            }
        });

        const video = document.getElementById('scannerVideo');
        video.srcObject = stream;
        video.play();

        // Show video and overlay
        video.classList.remove('hidden');
        document.getElementById('scanOverlay').classList.remove('hidden');
        document.getElementById('startScanBtn').classList.add('hidden');
        document.getElementById('stopScanBtn').classList.remove('hidden');

        updateStatus('Scanning... Posisikan QR code di depan kamera', 'info');
        scanning = true;

        // Optional: simulate QR detection for local/dev testing only
        if (ALLOW_SIMULATE_QR) {
            setTimeout(() => {
                if (scanning) {
                    simulateQRDetection();
                }
            }, 3000);
        }

    } catch (error) {
        console.error('Error accessing camera:', error);
        updateStatus('Gagal mengakses kamera. Pastikan Anda memberikan izin kamera.', 'error');
    }
}

function stopScanner() {
    scanning = false;

    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }

    const video = document.getElementById('scannerVideo');
    video.classList.add('hidden');
    document.getElementById('scanOverlay').classList.add('hidden');
    document.getElementById('startScanBtn').classList.remove('hidden');
    document.getElementById('stopScanBtn').classList.add('hidden');

    updateStatus('Scanner stopped', 'info');
}

function resetScanner() {
    stopScanner();
    document.getElementById('resultArea').classList.add('hidden');
    document.getElementById('assetInfo').classList.add('hidden');
    updateStatus('Posisikan QR code di depan kamera', 'info');
}

function simulateQRDetection() {
    // Simulate successful scan
    stopScanner();

    const mockData = {
        type: 'asset',
        asset: {
            name: 'Laptop Dell XPS 15',
            serial_number: 'SN123456789',
            status: 'available'
        },
        assetId: 'mock-id-123'
    };

    showResult(mockData);
}

function showResult(data) {
    document.getElementById('resultArea').classList.remove('hidden');
    document.getElementById('resultText').textContent = 'Asset found: ' + data.asset.name;

    if (data.type === 'asset') {
        document.getElementById('assetInfo').classList.remove('hidden');
        document.getElementById('assetName').textContent = data.asset.name;
        document.getElementById('assetSerial').textContent = data.asset.serial_number;
        document.getElementById('assetStatus').textContent = data.asset.status;
        document.getElementById('viewAssetBtn').href = `/assets/${data.assetId}`;
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
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endsection