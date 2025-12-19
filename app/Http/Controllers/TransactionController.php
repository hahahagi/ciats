<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
        
        $this->middleware(function ($request, $next) {
            if (!Session::has('user')) {
                return redirect('/login');
            }
            return $next($request);
        });
    }

    /**
     * ====================
     * KARYAWAN FUNCTIONS
     * ====================
     */

    // 1. Tampilkan katalog untuk employee
    public function catalog()
    {
        $user = Session::get('user');
        
        // Ambil assets yang available saja
        $assetsRef = $this->database->getReference('assets')->getValue();
        $availableAssets = [];
        
        if ($assetsRef) {
            foreach ($assetsRef as $id => $asset) {
                if (($asset['status'] ?? '') == 'available') {
                    $asset['id'] = $id;
                    $availableAssets[] = $asset;
                }
            }
        }
        
        // Group by category
        $categories = [];
        foreach ($availableAssets as $asset) {
            $category = $asset['category'] ?? 'Uncategorized';
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $asset;
        }

        return view('transactions.catalog', [
            'categories' => $categories,
            'user' => $user,
            'title' => 'Katalog Aset'
        ]);
    }

    // 2. Form pengajuan peminjaman
    public function requestForm($assetId)
    {
        $user = Session::get('user');
        
        $asset = $this->database->getReference("assets/{$assetId}")->getValue();
        
        if (!$asset || ($asset['status'] ?? '') != 'available') {
            return redirect()->route('transactions.catalog')
                ->with('error', 'Aset tidak tersedia untuk dipinjam.');
        }

        // Ambil lokasi yang tersedia
        $locations = $this->database->getReference('locations')->getValue() ?? [];

        return view('transactions.request-form', [
            'asset' => $asset,
            'assetId' => $assetId,
            'locations' => $locations,
            'user' => $user,
            'title' => 'Ajukan Peminjaman'
        ]);
    }

    // 3. Submit pengajuan peminjaman
    public function submitRequest(Request $request)
    {
        $user = Session::get('user');
        
        $request->validate([
            'asset_id' => 'required',
            'purpose' => 'required|string|min:10|max:500',
            'requested_location' => 'required|string',
            'expected_return_date' => 'required|date|after:today',
        ]);

        $assetId = $request->asset_id;
        $asset = $this->database->getReference("assets/{$assetId}")->getValue();

        if (!$asset || ($asset['status'] ?? '') != 'available') {
            return back()->with('error', 'Aset sudah tidak tersedia.');
        }

        // Buat transaction baru
        $transactionData = [
            'asset_id' => $assetId,
            'asset_name' => $asset['name'] ?? '',
            'asset_serial' => $asset['serial_number'] ?? '',
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'requested_by' => $user['name'],
            'approved_by' => null,
            'approved_by_name' => null,
            'checked_out_by' => null,
            'checked_in_by' => null,
            'status' => 'waiting_approval',
            'purpose' => $request->purpose,
            'requested_location' => $request->requested_location,
            'requested_at' => time(),
            'approved_at' => null,
            'checkout_at' => null,
            'checkin_at' => null,
            'expected_return_date' => strtotime($request->expected_return_date),
            'actual_return_date' => null,
            'condition_before' => 'good',
            'condition_after' => null,
            'damage_notes' => null,
        ];

        // Simpan transaction
        $transactionRef = $this->database->getReference('transactions')->push($transactionData);
        $transactionId = $transactionRef->getKey();

        // Update status asset menjadi "booked"
        $this->database->getReference("assets/{$assetId}")->update([
            'status' => 'booked',
            'booked' => true,
            'updated_at' => time(),
        ]);

        return redirect()->route('transactions.myRequests')
            ->with('success', 'Pengajuan berhasil! Menunggu persetujuan operator.');
    }

    // 4. Lihat request sendiri (karyawan)
    public function myRequests()
    {
        $user = Session::get('user');
        
        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $myTransactions = [];
        
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                if (($transaction['user_id'] ?? '') == $user['id']) {
                    $transaction['id'] = $id;
                    $myTransactions[] = $transaction;
                }
            }
        }
        
        // Sort by requested_at (newest first)
        usort($myTransactions, function($a, $b) {
            return ($b['requested_at'] ?? 0) <=> ($a['requested_at'] ?? 0);
        });

        return view('transactions.my-requests', [
            'transactions' => $myTransactions,
            'user' => $user,
            'title' => 'Request Saya'
        ]);
    }

    /**
     * ====================
     * OPERATOR FUNCTIONS
     * ====================
     */

    // 1. Lihat semua request yang menunggu persetujuan
    public function pendingApprovals()
    {
        $user = Session::get('user');
        
        // Hanya operator dan admin yang bisa akses
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa mengakses.');
        }

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $pendingTransactions = [];
        
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                if (($transaction['status'] ?? '') == 'waiting_approval') {
                    $transaction['id'] = $id;
                    $pendingTransactions[] = $transaction;
                }
            }
        }

        return view('transactions.pending-approvals', [
            'transactions' => $pendingTransactions,
            'user' => $user,
            'title' => 'Persetujuan Peminjaman'
        ]);
    }

    // 2. Approve request
    public function approve($id)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa approve.');
        }

        $transaction = $this->database->getReference("transactions/{$id}")->getValue();
        
        if (!$transaction || ($transaction['status'] ?? '') != 'waiting_approval') {
            return redirect()->route('transactions.pendingApprovals')
                ->with('error', 'Transaksi tidak ditemukan atau sudah diproses.');
        }

        // Update transaction
        $this->database->getReference("transactions/{$id}")->update([
            'status' => 'approved',
            'approved_by' => $user['id'],
            'approved_by_name' => $user['name'],
            'approved_at' => time(),
        ]);

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', 'Request telah disetujui! Karyawan bisa mengambil barang.');
    }

    // 3. Reject request
    public function reject(Request $request, $id)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa reject.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:200',
        ]);

        $transaction = $this->database->getReference("transactions/{$id}")->getValue();
        
        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        // Update transaction
        $this->database->getReference("transactions/{$id}")->update([
            'status' => 'rejected',
            'approved_by' => $user['id'],
            'approved_by_name' => $user['name'],
            'approved_at' => time(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Kembalikan status asset menjadi available
        $assetId = $transaction['asset_id'] ?? '';
        if ($assetId) {
            $this->database->getReference("assets/{$assetId}")->update([
                'status' => 'available',
                'booked' => false,
                'updated_at' => time(),
            ]);
        }

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', 'Request telah ditolak.');
    }

    // 4. Checkout (serahkan barang ke karyawan)
    public function checkoutForm($transactionId)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkout.');
        }

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();
        
        if (!$transaction || ($transaction['status'] ?? '') != 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Transaksi tidak ditemukan atau belum disetujui.');
        }

        $asset = $this->database->getReference("assets/{$transaction['asset_id']}")->getValue();

        return view('transactions.checkout-form', [
            'transaction' => $transaction,
            'transactionId' => $transactionId,
            'asset' => $asset,
            'user' => $user,
            'title' => 'Checkout Barang'
        ]);
    }

    // 5. Proses checkout
    public function processCheckout(Request $request, $transactionId)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkout.');
        }

        $request->validate([
            'condition' => 'required|in:good,minor_damage',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();
        
        if (!$transaction || ($transaction['status'] ?? '') != 'approved') {
            return back()->with('error', 'Transaksi tidak valid.');
        }

        // Update transaction
        $this->database->getReference("transactions/{$transactionId}")->update([
            'status' => 'active',
            'checked_out_by' => $user['id'],
            'checked_out_by_name' => $user['name'],
            'checkout_at' => time(),
            'condition_before' => $request->condition,
            'checkout_notes' => $request->notes,
        ]);

        // Update asset status
        $assetId = $transaction['asset_id'];
        $this->database->getReference("assets/{$assetId}")->update([
            'status' => 'in_use',
            'booked' => false,
            'current_holder' => $transaction['user_name'],
            'current_holder_id' => $transaction['user_id'],
            'current_transaction_id' => $transactionId,
            'updated_at' => time(),
        ]);

        return redirect()->route('transactions.activeLoans')
            ->with('success', 'Barang berhasil diserahkan ke karyawan.');
    }

    // 6. Checkin (terima barang kembali)
    public function checkinForm($transactionId)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkin.');
        }

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();
        
        if (!$transaction || ($transaction['status'] ?? '') != 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'Transaksi tidak ditemukan atau belum aktif.');
        }

        $asset = $this->database->getReference("assets/{$transaction['asset_id']}")->getValue();

        return view('transactions.checkin-form', [
            'transaction' => $transaction,
            'transactionId' => $transactionId,
            'asset' => $asset,
            'user' => $user,
            'title' => 'Checkin Barang'
        ]);
    }

    // 7. Proses checkin
    public function processCheckin(Request $request, $transactionId)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkin.');
        }

        $request->validate([
            'condition' => 'required|in:good,minor_damage,major_damage',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();
        
        if (!$transaction || ($transaction['status'] ?? '') != 'active') {
            return back()->with('error', 'Transaksi tidak valid.');
        }

        // Update transaction
        $this->database->getReference("transactions/{$transactionId}")->update([
            'status' => 'completed',
            'checked_in_by' => $user['id'],
            'checked_in_by_name' => $user['name'],
            'checkin_at' => time(),
            'actual_return_date' => time(),
            'condition_after' => $request->condition,
            'damage_notes' => $request->notes,
        ]);

        // Update asset status
        $assetId = $transaction['asset_id'];
        $updateData = [
            'status' => $request->condition == 'good' ? 'available' : 'damaged',
            'current_holder' => null,
            'current_holder_id' => null,
            'current_transaction_id' => null,
            'updated_at' => time(),
        ];

        $this->database->getReference("assets/{$assetId}")->update($updateData);

        return redirect()->route('transactions.activeLoans')
            ->with('success', 'Barang berhasil diterima kembali.');
    }

    // 8. Lihat semua peminjaman aktif
    public function activeLoans()
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa melihat.');
        }

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $activeTransactions = [];
        
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                if (($transaction['status'] ?? '') == 'active') {
                    $transaction['id'] = $id;
                    $activeTransactions[] = $transaction;
                }
            }
        }

        return view('transactions.active-loans', [
            'transactions' => $activeTransactions,
            'user' => $user,
            'title' => 'Peminjaman Aktif'
        ]);
    }

    /**
     * ====================
     * ADMIN FUNCTIONS (VIEW ONLY)
     * ====================
     */

    // 1. Lihat semua transaksi (admin view only)
    public function allTransactions()
    {
        $user = Session::get('user');
        
        if ($user['role'] != 'admin') {
            abort(403, 'Hanya admin yang bisa melihat semua transaksi.');
        }

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $allTransactions = [];
        
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                $transaction['id'] = $id;
                $allTransactions[] = $transaction;
            }
        }
        
        // Sort by requested_at (newest first)
        usort($allTransactions, function($a, $b) {
            return ($b['requested_at'] ?? 0) <=> ($a['requested_at'] ?? 0);
        });

        return view('transactions.all-transactions', [
            'transactions' => $allTransactions,
            'user' => $user,
            'title' => 'Semua Transaksi'
        ]);
    }

    /**
     * ====================
     * SCANNER FUNCTIONS
     * ====================
     */

    // 1. Scanner untuk QR Code
    public function scanner()
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa menggunakan scanner.');
        }

        return view('scanner.index', [
            'user' => $user,
            'title' => 'QR Code Scanner'
        ]);
    }

    // 2. Handle scan result
    public function handleScan(Request $request)
    {
        $user = Session::get('user');
        
        if (!in_array($user['role'], ['operator', 'admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $scanData = json_decode($request->input('data'), true);
        
        if (!$scanData || !isset($scanData['type'])) {
            return response()->json(['error' => 'Invalid QR code'], 400);
        }

        if ($scanData['type'] == 'asset') {
            $assetId = $scanData['id'] ?? '';
            $asset = $this->database->getReference("assets/{$assetId}")->getValue();
            
            if ($asset) {
                return response()->json([
                    'type' => 'asset',
                    'asset' => $asset,
                    'assetId' => $assetId,
                    'redirect_url' => route('assets.show', $assetId)
                ]);
            }
        }

        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }
}