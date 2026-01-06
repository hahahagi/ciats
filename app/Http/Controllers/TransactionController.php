<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;
use App\Services\AuditLogger;

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

        // A. Calculate User Stats & Global Pending Counts first
        $stats = [
            'my_active' => 0,
            'my_pending' => 0,
        ];
        $pendingCounts = [];

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        if ($transactionsRef) {
            foreach ($transactionsRef as $transaction) {
                // Stats for current user
                if (($transaction['user_id'] ?? '') == $user['id']) {
                    $status = $transaction['status'] ?? '';
                    if ($status == 'active') {
                        $stats['my_active']++;
                    } elseif ($status == 'waiting_approval') {
                        $stats['my_pending']++;
                    }
                }

                // Global Pending Count (Waiting Approval) -> Reduces Stock
                if (in_array(($transaction['status'] ?? ''), ['waiting_approval', 'approved'])) {
                    $aId = $transaction['asset_id'] ?? null;
                    if ($aId) {
                        $pendingCounts[$aId] = ($pendingCounts[$aId] ?? 0) + 1;
                    }
                }
            }
        }

        // B. Get Assets and Filter by Real Availability
        $assetsRef = $this->database->getReference('assets')->getValue();
        $availableAssets = [];

        if ($assetsRef) {
            foreach ($assetsRef as $id => $asset) {
                // 1. Calculate Physical Availability
                $physicalCount = 0;
                $items = $asset['items'] ?? [];

                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (($item['status'] ?? 'available') === 'available') {
                            $physicalCount++;
                        }
                    }
                } else {
                    // Legacy/Single
                    if (($asset['status'] ?? 'available') === 'available') {
                        $physicalCount = 1;
                    }
                }

                // 2. Subtract Pending Requests
                $pendingForThisAsset = $pendingCounts[$id] ?? 0;
                $realAvailable = $physicalCount - $pendingForThisAsset;

                // 3. Add to list if available > 0
                if ($realAvailable > 0) {
                    $asset['id'] = $id;
                    $asset['available_count'] = $realAvailable; // Inject calculated count
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
            'stats' => $stats,
            'user' => $user,
            'title' => 'Katalog Aset'
        ]);
    }

    // Bulk Approve
    public function bulkApprove(Request $request)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $ids = $request->input('tx_ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada transaksi yang dipilih.');
        }

        $count = 0;
        foreach ($ids as $id) {
            $transaction = $this->database->getReference("transactions/{$id}")->getValue();
            if ($transaction && ($transaction['status'] ?? '') === 'waiting_approval') {
                $this->database->getReference("transactions/{$id}")->update([
                    'status' => 'approved',
                    'approved_by' => $user['id'],
                    'approved_by_name' => $user['name'],
                    'approved_at' => time(),
                ]);

                // Audit
                AuditLogger::log('transaction_approved', ['transaction_id' => $id], $user['id'], $user['name']);
                $count++;
            }
        }

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', "{$count} Permintaan berhasil disetujui sekaligus!");
    }

    // Bulk Reject
    public function bulkReject(Request $request)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $ids = $request->input('tx_ids', []);
        $reason = $request->input('bulk_rejection_reason');

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada transaksi yang dipilih.');
        }

        $count = 0;
        foreach ($ids as $id) {
            $transaction = $this->database->getReference("transactions/{$id}")->getValue();
            if ($transaction && ($transaction['status'] ?? '') === 'waiting_approval') {
                $this->database->getReference("transactions/{$id}")->update([
                    'status' => 'rejected',
                    'approved_by' => $user['id'],
                    'approved_by_name' => $user['name'],
                    'approved_at' => time(), // Rejection time
                    'rejection_reason' => $reason
                ]);

                // Audit
                AuditLogger::log('transaction_rejected', ['transaction_id' => $id], $user['id'], $user['name']);
                $count++;
            }
        }

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', "{$count} Permintaan berhasil ditolak sekaligus.");
    }


    // Bulk Checkout Form (Step 1)
    public function bulkCheckoutForm(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) abort(403);

        $assetId = $request->input('asset_id');
        $serials = $request->input('item_serials', []);

        if (empty($serials)) return back()->with('error', 'Tidak ada item yang dipilih.');

        // 1. Get Pending Approved Transactions (FIFO)
        $txRef = $this->database->getReference('transactions')->getValue();
        $pendingTxs = [];
        if ($txRef) {
            foreach ($txRef as $key => $tx) {
                if (($tx['asset_id'] ?? '') == $assetId && ($tx['status'] ?? '') == 'approved') {
                    $tx['id'] = $key;
                    $pendingTxs[] = $tx;
                }
            }
        }
        // Sort Oldest First
        usort($pendingTxs, function ($a, $b) {
            return ($a['approved_at'] ?? 0) <=> ($b['approved_at'] ?? 0);
        });

        if (count($pendingTxs) < count($serials)) {
            return back()->with('error', 'Jumlah item dipilih (' . count($serials) . ') melebihi jumlah permintaan approved (' . count($pendingTxs) . ').');
        }

        // Map items
        $checkoutItems = [];
        foreach ($serials as $index => $serial) {
            if (isset($pendingTxs[$index])) {
                $tx = $pendingTxs[$index];
                $checkoutItems[] = [
                    'serial' => $serial,
                    'tx_id' => $tx['id'],
                    'user_name' => $tx['user_name'],
                    'purpose' => $tx['purpose'] ?? '-'
                ];
            }
        }

        return view('transactions.bulk-checkout-form', [
            'checkoutItems' => $checkoutItems,
            'assetId' => $assetId,
            'user' => $user,
            'title' => 'Bulk Checkout'
        ]);
    }

    // Process Bulk Checkout (Step 2)
    public function processBulkCheckout(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) abort(403);

        $assetId = $request->asset_id;
        $items = $request->items; // Array of [serial, tx_id, condition, notes]

        // Map Asset Items to get Item ID from Serial
        $assetRef = $this->database->getReference("assets/{$assetId}")->getValue();
        $serialToItemId = [];
        if (isset($assetRef['items'])) {
            foreach ($assetRef['items'] as $itemId => $itemData) {
                if (isset($itemData['serial_number'])) {
                    $serialToItemId[$itemData['serial_number']] = $itemId;
                }
            }
        }

        $processed = 0;
        foreach ($items as $item) {
            $txId = $item['tx_id'] ?? null;
            $serial = $item['serial'] ?? null;
            $condition = $item['condition'] ?? 'good';
            $notes = $item['notes'] ?? null;
            $itemId = $serialToItemId[$serial] ?? null;

            if ($itemId && $txId) {
                // Update Transaction
                $this->database->getReference("transactions/{$txId}")->update([
                    'status' => 'active',
                    'checked_out_by' => $user['id'],
                    'checked_out_by_name' => $user['name'],
                    'checkout_at' => time(),
                    'item_id' => $itemId,
                    'asset_serial' => $serial,
                    'condition_before' => $condition,
                    'checkout_notes' => $notes,
                ]);

                // Update Item
                $this->database->getReference("assets/{$assetId}/items/{$itemId}")->update([
                    'status' => 'in_use',
                    'current_holder' => $item['holder'] ?? 'Employee',
                    'updated_at' => time(),
                ]);
            }
            $processed++;
        }

        return redirect()->route('assets.show', $assetId)
            ->with('success', "{$processed} item berhasil diproses (Checkout).");
    }

    // Bulk Checkin Form
    public function bulkCheckinForm(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) abort(403);

        $assetId = $request->input('asset_id');
        $serials = $request->input('item_serials', []);

        if (empty($serials)) return back()->with('error', 'Tidak ada item yang dipilih.');

        // Find Active Transactions for these serials
        $txRef = $this->database->getReference('transactions')->getValue();
        $checkinItems = [];

        // Build map: Serial -> Active TX
        $activeTxMap = [];
        if ($txRef) {
            foreach ($txRef as $txId => $tx) {
                // Modified to allow mixed assets if assetId is not provided
                $isAssetMatch = $assetId ? ($tx['asset_id'] ?? '') == $assetId : true;

                if (
                    $isAssetMatch &&
                    ($tx['status'] ?? '') == 'active' &&
                    !empty($tx['asset_serial'])
                ) {
                    $activeTxMap[$tx['asset_serial']] = $tx;
                    $activeTxMap[$tx['asset_serial']]['id'] = $txId;
                }
            }
        }

        foreach ($serials as $serial) {
            if (isset($activeTxMap[$serial])) {
                $tx = $activeTxMap[$serial];
                $checkinItems[] = [
                    'serial' => $serial,
                    'tx_id' => $tx['id'],
                    'holder' => $tx['user_name'],
                    'asset_id' => $tx['asset_id'] // Add asset_id for per-item processing
                ];
            }
        }

        return view('transactions.bulk-checkin-form', [
            'checkinItems' => $checkinItems,
            'assetId' => $assetId,
            'locations' => $this->database->getReference('locations')->getValue() ?? [],
            'user' => $user,
            'title' => 'Bulk Checkin'
        ]);
    }

    // Process Bulk Checkin
    public function processBulkCheckin(Request $request)
    {
        $user = Session::get('user');
        $globalAssetId = $request->asset_id;
        $items = $request->items;

        $processed = 0;
        $loadedAssets = []; // Cache to prevent fetching same asset multiple times

        foreach ($items as $item) {
            $txId = $item['tx_id'];
            $serial = $item['serial'];

            // Determine Asset ID for this item (Per-item or Global fallback)
            $currentAssetId = $item['asset_id'] ?? $globalAssetId;

            if (!$currentAssetId) continue; // Skip if no asset ID found

            // Load Asset Map if not loaded (Optimization)
            if (!isset($loadedAssets[$currentAssetId])) {
                $ref = $this->database->getReference("assets/{$currentAssetId}")->getValue();
                $map = [];
                if (isset($ref['items'])) {
                    foreach ($ref['items'] as $id => $data) {
                        if (isset($data['serial_number'])) $map[$data['serial_number']] = $id;
                    }
                }
                $loadedAssets[$currentAssetId] = ['ref' => $ref, 'map' => $map];
            }

            $assetData = $loadedAssets[$currentAssetId];
            $itemId = $assetData['map'][$serial] ?? null;
            $assetRef = $assetData['ref']; // Used for location fallback

            // Extract per-item inputs
            $condition = $item['condition'] ?? 'good';
            $notes = $item['notes'] ?? null;

            // Get original asset location if possible
            $returnedLocation = $assetRef['location'] ?? 'Storage';

            // Update Transaction
            $this->database->getReference("transactions/{$txId}")->update([
                'status' => 'completed',
                'checked_in_by' => $user['id'],
                'checkin_at' => time(),
                'actual_return_date' => time(),
                'condition_after' => $condition,
                'checkin_notes' => $notes,
                'returned_location' => $returnedLocation
            ]);

            // Update Item
            if ($itemId) {
                // Determine status based on condition
                $newStatus = ($condition == 'damaged') ? 'damaged' : 'available';

                $this->database->getReference("assets/{$currentAssetId}/items/{$itemId}")->update([
                    'status' => $newStatus,
                    'condition' => $condition,
                    'current_holder' => null,
                    'updated_at' => time(),
                ]);
            }
            $processed++;
        }

        // Redirect logic
        if ($globalAssetId) {
            return redirect()->route('assets.show', $globalAssetId)
                ->with('success', "{$processed} item berhasil dikembalikan (Check-in).");
        } else {
            return redirect()->route('transactions.activeLoans')
                ->with('success', "{$processed} item berhasil dikembalikan (Check-in).");
        }
    }

    // Bulk Checkout (Legacy Direct - Removed/Replaced)
    public function bulkCheckout(Request $request)
    {
        // This method is now likely unused if we routed form to bulkCheckoutForm
        // But for safety, let's keep it or redirect.
        // The View form POSTs to here? No, I updated JS to post to bulkCheckoutForm.
        return redirect()->back();
    }


    // 2. Form pengajuan peminjaman
    public function requestForm($assetId)
    {
        $user = Session::get('user');

        $asset = $this->database->getReference("assets/{$assetId}")->getValue();

        // 1. Calculate Physical Availability
        $physicalCount = 0;
        $items = $asset['items'] ?? [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if (($item['status'] ?? 'available') === 'available') {
                    $physicalCount++;
                }
            }
        } else {
            // Legacy/Single check
            if (($asset['status'] ?? 'available') === 'available') {
                $physicalCount = 1;
            }
        }

        // 2. Count Pending Requests (Waiting Approval)
        $pendingCount = 0;
        $transactionsRef = $this->database->getReference('transactions')->getValue();
        if ($transactionsRef) {
            foreach ($transactionsRef as $t) {
                if (($t['asset_id'] ?? '') == $assetId && in_array(($t['status'] ?? ''), ['waiting_approval', 'approved'])) {
                    $pendingCount++;
                }
            }
        }

        // 3. Real Availability
        $realAvailable = $physicalCount - $pendingCount;

        if (!$asset || $realAvailable <= 0) {
            return redirect()->route('transactions.catalog')
                ->with('error', 'Aset tidak tersedia untuk dipinjam (Stok habis/sedang diproses).');
        }

        // Ambil lokasi yang tersedia
        $locations = $this->database->getReference('locations')->getValue() ?? [];

        return view('transactions.request-form', [
            'asset' => $asset,
            'assetId' => $assetId,
            'availableCount' => $realAvailable, // Pass Real Available
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
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|min:10|max:500',
            'requested_location' => 'required|string',
            'expected_return_date' => 'required|date|after:today',
        ], [
            'quantity.required' => 'Jumlah aset wajib diisi.',
            'quantity.min' => 'Jumlah aset minimal 1.',
            'purpose.required' => 'Tujuan peminjaman wajib diisi.',
            'purpose.min' => 'Tujuan peminjaman minimal 10 karakter.',
            'purpose.max' => 'Tujuan peminjaman maksimal 500 karakter.',
            'requested_location.required' => 'Lokasi penggunaan wajib dipilih.',
            'expected_return_date.required' => 'Perkiraan tanggal pengembalian wajib diisi.',
            'expected_return_date.date' => 'Format tanggal tidak valid.',
            'expected_return_date.after' => 'Tanggal pengembalian harus setelah hari ini.',
        ]);

        $assetId = $request->asset_id;
        $quantity = (int)$request->quantity;
        $asset = $this->database->getReference("assets/{$assetId}")->getValue();

        // 1. Calculate Physical Availability
        $items = $asset['items'] ?? [];
        $physicalCount = 0;

        if (!empty($items)) {
            foreach ($items as $item) {
                if (($item['status'] ?? 'available') === 'available') {
                    $physicalCount++;
                }
            }
        } else {
            // Fallback legacy singular check
            if (($asset['status'] ?? 'available') === 'available') {
                $physicalCount = 1;
            }
        }

        // 2. Count Pending Requests (Waiting Approval)
        $pendingCount = 0;
        $transactionsRef = $this->database->getReference('transactions')->getValue();
        if ($transactionsRef) {
            foreach ($transactionsRef as $t) {
                if (($t['asset_id'] ?? '') == $assetId && in_array(($t['status'] ?? ''), ['waiting_approval', 'approved'])) {
                    $pendingCount++;
                }
            }
        }

        // 3. Real Availability
        $realAvailable = $physicalCount - $pendingCount;

        if (!$asset || $realAvailable < $quantity) {
            return back()->with('error', "Stok tidak mencukupi. Tersedia: {$realAvailable}, Diminta: {$quantity}.");
        }

        // Loop create transaction for each quantity requested
        for ($i = 0; $i < $quantity; $i++) {
            // Buat transaction baru
            $transactionData = [
                'asset_id' => $assetId,
                'asset_name' => $asset['name'] ?? '',
                'asset_serial' => 'Assigned at Checkout', // Will be set by operator
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
                'request_group_id' => $quantity > 1 ? uniqid('grp_') : null, // Optional grouping
            ];

            // Simpan transaction
            $transactionRef = $this->database->getReference('transactions')->push($transactionData);
            $transactionId = $transactionRef->getKey();

            // Audit Log (Bulk info?)
            AuditLogger::log('transaction_requested', [
                'transaction_id' => $transactionId,
                'asset_id' => $assetId,
                'asset_name' => $asset['name'] ?? '',
                'quantity_index' => $i + 1
            ], $user['id'], $user['name']);
        }

        // Redirect message
        $msg = $quantity > 1
            ? "Berhasil mengajukan {$quantity} item! Menunggu persetujuan operator."
            : 'Pengajuan berhasil! Menunggu persetujuan operator.';

        return redirect()->route('transactions.myRequests')
            ->with('success', $msg);
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
        usort($myTransactions, function ($a, $b) {
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
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa mengakses.');
        }

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $pendingTransactions = [];
        $stats = [
            'approved_week' => 0,
            'rejected_week' => 0,
        ];

        $oneWeekAgo = time() - (7 * 24 * 60 * 60);

        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                $status = $transaction['status'] ?? '';
                $processedAt = $transaction['approved_at'] ?? 0;

                // Filter pending transactions for list
                if ($status == 'waiting_approval') {
                    $transaction['id'] = $id;
                    $pendingTransactions[] = $transaction;
                }

                // Calculate stats
                if ($processedAt >= $oneWeekAgo) {
                    if ($status == 'rejected') {
                        $stats['rejected_week']++;
                    } else {
                        // Count as approved if it has a processed date and is not rejected
                        // This includes: approved, active, completed
                        $stats['approved_week']++;
                    }
                }
            }
        }

        return view('transactions.pending-approvals', [
            'transactions' => $pendingTransactions,
            'stats' => $stats,
            'user' => $user,
            'title' => 'Persetujuan Peminjaman'
        ]);
    }

    // 2. Approve request
    public function approve($id)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
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

        // Audit Log
        AuditLogger::log('transaction_approved', [
            'transaction_id' => $id
        ], $user['id'], $user['name']);

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', 'Request telah disetujui! Karyawan bisa mengambil barang.');
    }

    // 3. Reject request
    public function reject(Request $request, $id)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
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

        // Audit Log
        AuditLogger::log('transaction_rejected', [
            'transaction_id' => $id,
            'reason' => $request->rejection_reason
        ], $user['id'], $user['name']);

        return redirect()->route('transactions.pendingApprovals')
            ->with('success', 'Request telah ditolak.');
    }

    // 4. Checkout (serahkan barang ke karyawan)
    public function checkoutForm($transactionId)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkout.');
        }

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();

        if (!$transaction || ($transaction['status'] ?? '') != 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Transaksi tidak ditemukan atau belum disetujui.');
        }

        $asset = $this->database->getReference("assets/{$transaction['asset_id']}")->getValue();

        // Get Available Items
        $items = $asset['items'] ?? [];
        $availableItems = [];
        foreach ($items as $itemId => $item) {
            if (($item['status'] ?? 'available') === 'available') {
                $item['id'] = $itemId;
                $availableItems[] = $item;
            }
        }

        return view('transactions.checkout-form', [
            'transaction' => $transaction,
            'transactionId' => $transactionId,
            'asset' => $asset,
            'availableItems' => $availableItems,
            'user' => $user,
            'title' => 'Checkout Barang'
        ]);
    }

    // 5. Proses checkout
    public function processCheckout(Request $request, $transactionId)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa checkout.');
        }

        $request->validate([
            'condition' => 'required|in:good,minor_damage',
            'item_id' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = $this->database->getReference("transactions/{$transactionId}")->getValue();

        if (!$transaction || ($transaction['status'] ?? '') != 'approved') {
            return back()->with('error', 'Transaksi tidak valid.');
        }

        $assetId = $transaction['asset_id'];
        $itemId = $request->item_id;
        $item = $this->database->getReference("assets/{$assetId}/items/{$itemId}")->getValue();

        if (!$item) {
            return back()->with('error', 'Item tidak valid atau tidak ditemukan.');
        }

        // Update transaction
        $this->database->getReference("transactions/{$transactionId}")->update([
            'status' => 'active',
            'checked_out_by' => $user['id'],
            'checked_out_by_name' => $user['name'],
            'checkout_at' => time(),
            'condition_before' => $request->condition,
            'checkout_notes' => $request->notes,
            'item_id' => $itemId,
            'asset_serial' => $item['serial_number']
        ]);

        // Update Item status
        $this->database->getReference("assets/{$assetId}/items/{$itemId}")->update([
            'status' => 'in_use',
            'current_holder' => $transaction['user_name'],
            'updated_at' => time(),
        ]);

        // Audit Log
        AuditLogger::log('transaction_checkout', [
            'transaction_id' => $transactionId,
            'asset_id' => $assetId,
            'condition' => $request->condition
        ], $user['id'], $user['name']);

        return redirect()->route('transactions.activeLoans')
            ->with('success', 'Barang berhasil diserahkan ke karyawan.');
    }

    // 6. Checkin (terima barang kembali)
    public function checkinForm($transactionId)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
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

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
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

        // Update asset item status
        $assetId = $transaction['asset_id'];
        $itemId = $transaction['item_id'] ?? null;

        if ($itemId) {
            $updateData = [
                'status' => $request->condition == 'good' ? 'available' : 'damaged',
                'condition' => $request->condition, // Update condition in item
                'current_holder' => null,
                'updated_at' => time(),
            ];
            $this->database->getReference("assets/{$assetId}/items/{$itemId}")->update($updateData);
        } else {
            // Legacy fallback: Unlock parent asset if no item_id was recorded
            $this->database->getReference("assets/{$assetId}")->update([
                'status' => 'available',
                'booked' => false,
                'updated_at' => time(),
            ]);
        }

        // Audit Log
        AuditLogger::log('transaction_checkin', [
            'transaction_id' => $transactionId,
            'asset_id' => $assetId,
            'condition_after' => $request->condition
        ], $user['id'], $user['name']);

        return redirect()->route('transactions.activeLoans')
            ->with('success', 'Barang berhasil diterima kembali.');
    }

    // 8. Lihat semua peminjaman aktif
    public function activeLoans()
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa melihat.');
        }

        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $activeTransactions = [];
        $stats = [
            'due_today' => 0,
            'overdue' => 0,
            'on_time' => 0,
        ];

        $now = time();
        $startOfDay = strtotime("today", $now);
        $endOfDay = strtotime("tomorrow", $now) - 1;

        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $transaction) {
                if (($transaction['status'] ?? '') == 'active') {
                    $transaction['id'] = $id;
                    $activeTransactions[] = $transaction;

                    // Calculate stats
                    $expectedReturn = $transaction['expected_return_date'] ?? 0;

                    if ($expectedReturn < $startOfDay) {
                        $stats['overdue']++;
                    } elseif ($expectedReturn >= $startOfDay && $expectedReturn <= $endOfDay) {
                        $stats['due_today']++;
                    } else {
                        $stats['on_time']++;
                    }
                }
            }
        }

        return view('transactions.active-loans', [
            'transactions' => $activeTransactions,
            'stats' => $stats,
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

        if (!in_array($user['role'], ['admin', 'super_admin'])) {
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
        usort($allTransactions, function ($a, $b) {
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

        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa menggunakan scanner.');
        }

        // Ambil transaksi terakhir sebagai "Recent Activity"
        // Menggunakan orderByKey().limitToLast(10) untuk menghindari error "Index not defined" pada updated_at
        $transactionsRef = $this->database->getReference('transactions')
            ->orderByKey()
            ->limitToLast(10)
            ->getValue();

        $recentTransactions = [];
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $tx) {
                $tx['id'] = $id;
                $recentTransactions[] = $tx;
            }
            // Sort descending (newest first) by updated_at in PHP
            usort($recentTransactions, function ($a, $b) {
                return ($b['updated_at'] ?? 0) <=> ($a['updated_at'] ?? 0);
            });

            // Ambil 5 teratas setelah sorting
            $recentTransactions = array_slice($recentTransactions, 0, 5);
        }

        return view('scanner.index', [
            'user' => $user,
            'title' => 'QR Code Scanner',
            'recentTransactions' => $recentTransactions
        ]);
    }

    // 2. Handle scan result
    public function handleScan(Request $request)
    {
        try {
            $user = Session::get('user');

            if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $rawData = $request->input('data');

            // Try to decode JSON
            $scanData = json_decode($rawData, true);

            // If not JSON, maybe it's a direct URL or string?
            // For now, we expect JSON as per the user's phone scanner output.
            // But let's handle the case where it's just a string (maybe a serial number?)
            if (!$scanData) {
                // Attempt to treat the raw data as a serial number if it looks like one
                // This is a fallback for non-JSON QR codes
                $scanData = ['type' => 'asset', 'serial' => $rawData];
            }

            if (!isset($scanData['type'])) {
                // If we decoded JSON but it doesn't have 'type', maybe it's just the serial?
                if (isset($scanData['serial'])) {
                    $scanData['type'] = 'asset';
                } else {
                    return response()->json(['error' => 'Invalid QR code format'], 400);
                }
            }

            if ($scanData['type'] == 'asset') {
                $assetId = $scanData['id'] ?? null;
                $serialNumber = $scanData['serial'] ?? null;

                $asset = null;

                // Jika ada ID, cari by ID
                if ($assetId) {
                    $asset = $this->database->getReference("assets/{$assetId}")->getValue();
                }

                // Jika tidak ada ID (atau ID tidak ketemu) tapi ada Serial, cari by Serial
                if (!$asset && $serialNumber) {
                    // FIX: Menggunakan orderByChild memerlukan index di Firebase Rules (".indexOn": "serial_number")
                    // Kita gunakan fallback: ambil semua assets dan filter di PHP untuk menghindari error "Index not defined".

                    try {
                        // Cara 1: Coba query standard (akan gagal jika index belum ada)
                        $assets = $this->database->getReference('assets')
                            ->orderByChild('serial_number')
                            ->equalTo($serialNumber)
                            ->getValue();

                        if ($assets) {
                            $assetId = array_key_first($assets);
                            $asset = $assets[$assetId];
                        }
                    } catch (\Exception $e) {
                        // Cara 2: Fallback manual filter (lebih lambat tapi pasti jalan tanpa index)
                        $allAssets = $this->database->getReference('assets')->getValue();
                        if ($allAssets) {
                            foreach ($allAssets as $key => $val) {
                                // Check Parent Serial (Legacy)
                                if (isset($val['serial_number']) && (string)$val['serial_number'] === (string)$serialNumber) {
                                    $asset = $val;
                                    $assetId = $key;
                                    break;
                                }
                                // Check Items Serial (New)
                                if (isset($val['items']) && is_array($val['items'])) {
                                    foreach ($val['items'] as $itemKey => $item) {
                                        if (isset($item['serial_number']) && (string)$item['serial_number'] === (string)$serialNumber) {
                                            $asset = $val;
                                            $assetId = $key;
                                            // We could pass itemId to view, but assets.show handles listing items.
                                            // Maybe highlight the scanned item?
                                            // For now, just showing the asset detail is sufficient.
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($asset && $assetId) {
                    // Normalize return data
                    $displaySerial = $asset['serial_number'] ?? '-';
                    $displayStatus = $asset['status'] ?? 'Unknown';

                    // If we found it via strict serial match on the logic above, let's try to be specific
                    // logic above: "If found via item..." -> but we broke the loop.
                    // Let's refine the loop to capture the specific item details.

                    // RE-RUN SEARCH for Specific Item to get exact status/serial for display
                    if (isset($asset['items'])) {
                        foreach ($asset['items'] as $item) {
                            if (($item['serial_number'] ?? '') === $serialNumber) {
                                $displaySerial = $item['serial_number'];
                                $displayStatus = $item['status'] ?? 'available';
                                break;
                            }
                        }
                    }

                    return response()->json([
                        'type' => 'asset',
                        'asset' => [
                            'name' => $asset['name'] ?? 'Unknown Asset',
                            'serial_number' => $displaySerial,
                            'status' => $displayStatus
                        ],
                        'assetId' => $assetId,
                        'redirect_url' => route('assets.show', $assetId)
                    ]);
                }
            }

            return response()->json(['error' => 'Data aset tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
