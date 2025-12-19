<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;

class AssetController extends Controller
{
    protected $database;
    protected $tablename = 'assets';

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
        
        // Middleware untuk semua method
        $this->middleware(function ($request, $next) {
    if (!Session::has('user')) {
        return redirect('/login');
    }
    return $next($request);
});

    }

    /**
     * Display all assets (ALL ROLES bisa lihat)
     */
    public function index()
    {
        $user = Session::get('user');
        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->getSnapshot();
        $assets = $snapshot->getValue() ?? [];

        // Format data dengan ID
        $formattedAssets = [];
        foreach ($assets as $id => $asset) {
            $asset['id'] = $id;
            $formattedAssets[] = $asset;
        }

        return view('assets.index', [
            'assets' => $formattedAssets,
            'user' => $user,
            'title' => 'Daftar Aset'
        ]);
    }

    /**
     * Show single asset (ALL ROLES bisa lihat)
     */
    public function show($id)
    {
        $user = Session::get('user');
        
        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        
        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan.');
        }
        

        // Ambil riwayat transaksi untuk aset ini
        $transactions = $this->getAssetTransactions($id);
        
        // Ambil riwayat lokasi
        // $locationHistory = $asset['locations_history'] ?? [];

        return view('assets.show', [
            'asset' => $asset,
            'assetId' => $id,
            'transactions' => $transactions,
            // 'locationHistory' => $locationHistory,
            'user' => $user,
            'title' => 'Detail Aset'
        ]);
    }

    /**
     * Show create form (HANYA OPERATOR)
     */
    public function create()
    {
        $user = Session::get('user');
        
        // Hanya operator yang boleh create
        if ($user['role'] != 'operator') {
            abort(403, 'Hanya operator yang bisa menambah aset.');
        }

        // Ambil daftar lokasi dari Firebase
        $locations = $this->database->getReference('locations')->getValue() ?? [];

        return view('assets.create', [
            'locations' => $locations,
            'user' => $user,
            'title' => 'Tambah Aset Baru'
        ]);
    }

    /**
     * Store new asset (HANYA OPERATOR)
     */
    public function store(Request $request)
    {
        $user = Session::get('user');
        
        // Hanya operator yang boleh store
        if ($user['role'] != 'operator') {
            abort(403, 'Hanya operator yang bisa menambah aset.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'serial_number' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Generate QR Code
        $qrCodePath = $this->generateQRCode($request->serial_number, $request->name);

        $newAsset = [
            'name' => $request->name,
            'category' => $request->category,
            'serial_number' => $request->serial_number,
            'location' => $request->location,
            'description' => $request->description ?? '',
            'status' => 'available',
            'qr_code_url' => $qrCodePath,
            'booked' => false,
            'current_holder' => null,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        // Tambah ke Firebase
        $this->database->getReference($this->tablename)->push($newAsset);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil ditambahkan! QR Code telah digenerate.');
    }

    /**
     * Show edit form (HANYA OPERATOR)
     */
    public function edit($id)
    {
        $user = Session::get('user');
        
        // Hanya operator yang boleh edit
        if ($user['role'] != 'operator') {
            abort(403, 'Hanya operator yang bisa mengedit aset.');
        }

        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        
        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan.');
        }

        $locations = $this->database->getReference('locations')->getValue() ?? [];

        return view('assets.edit', [
            'asset' => $asset,
            'assetId' => $id,
            'locations' => $locations,
            'user' => $user,
            'title' => 'Edit Aset'
        ]);
    }

    /**
     * Update asset (HANYA OPERATOR)
     */
    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        
        // Hanya operator yang boleh update
        if ($user['role'] != 'operator') {
            abort(403, 'Hanya operator yang bisa mengupdate aset.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'serial_number' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'status' => 'required|in:available,maintenance,damaged',
            'description' => 'nullable|string|max:500',
        ]);

        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        
        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan.');
        }

        $updateData = [
            'name' => $request->name,
            'category' => $request->category,
            'serial_number' => $request->serial_number,
            'location' => $request->location,
            'status' => $request->status,
            'description' => $request->description ?? '',
            'updated_at' => time(),
        ];

        // Jika lokasi berubah, tambahkan ke history
        if (($asset['location'] ?? '') != $request->location) {
            $historyId = time() . '_' . rand(1000, 9999);
            $this->database->getReference("{$this->tablename}/{$id}/locations_history/{$historyId}")->set([
                'location' => $request->location,
                'changed_by' => $user['id'],
                'changed_by_name' => $user['name'],
                'notes' => $request->location_change_notes ?? 'Perubahan lokasi',
                'timestamp' => time(),
            ]);
        }

        // Update data
        $this->database->getReference("{$this->tablename}/{$id}")->update($updateData);

        return redirect()->route('assets.show', $id)
            ->with('success', 'Aset berhasil diperbarui!');
    }

    /**
     * Delete asset (HANYA OPERATOR)
     */
    public function destroy($id)
    {
        $user = Session::get('user');
        
        // Hanya operator yang boleh delete
        if ($user['role'] != 'operator') {
            abort(403, 'Hanya operator yang bisa menghapus aset.');
        }

        // Cek apakah aset sedang dipinjam
        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        
        if ($asset && ($asset['status'] ?? '') == 'in_use') {
            return redirect()->route('assets.index')
                ->with('error', 'Tidak bisa menghapus aset yang sedang dipinjam!');
        }

        // Hapus dari Firebase
        $this->database->getReference("{$this->tablename}/{$id}")->remove();

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus!');
    }

    /**
     * Print QR Code (ALL ROLES)
     */
    public function printQR($id)
    {
        $user = Session::get('user');
        
        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        
        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan.');
        }

        return view('assets.print-qr', [
            'asset' => $asset,
            'assetId' => $id,
            'user' => $user,
            'title' => 'Print QR Code'
        ]);
    }

    /**
     * Generate QR Code
     */
    private function generateQRCode($serialNumber, $name)
    {
        // Install dulu: composer require simplesoftwareio/simple-qrcode
        // Jika belum install, return path kosong dulu
        
        try {
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $qrContent = json_encode([
                    'serial' => $serialNumber,
                    'name' => $name,
                    'type' => 'asset',
                    'time' => time()
                ]);
                
                // Simpan ke storage
                $filename = 'qrcode_' . $serialNumber . '_' . time() . '.png';
                $path = storage_path('app/public/qrcodes/' . $filename);
                
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(300)
                    ->generate($qrContent, $path);
                
                return '/storage/qrcodes/' . $filename;
            }
        } catch (\Exception $e) {
            // Log error jika QR Code gagal
            // \Log::error('QR Code generation failed: ' . $e->getMessage());
        }
        
        return ''; // Return empty string jika gagal
    }

    /**
     * Get transactions for an asset
     */
    private function getAssetTransactions($assetId)
    {
        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $assetTransactions = [];
        
        if ($transactionsRef) {
            foreach ($transactionsRef as $txId => $transaction) {
                if (($transaction['asset_id'] ?? '') == $assetId) {
                    $transaction['id'] = $txId;
                    $assetTransactions[] = $transaction;
                }
            }
        }
        
        return $assetTransactions;
    }
}