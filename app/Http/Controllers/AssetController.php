<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;
use App\Services\AuditLogger;

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

        $this->middleware(function ($request, $next) {
            if (!Session::has('user')) {
                return redirect('/login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Session::get('user');
        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->getSnapshot();
        $assets = $snapshot->getValue() ?? [];

        // 1. Get Pending Counts to subtract from Available
        $transactionsRef = $this->database->getReference('transactions')->getValue();
        $pendingCounts = [];

        if ($transactionsRef) {
            foreach ($transactionsRef as $transaction) {
                if (in_array(($transaction['status'] ?? ''), ['waiting_approval', 'approved'])) {
                    $aId = $transaction['asset_id'] ?? null;
                    if ($aId) {
                        $pendingCounts[$aId] = ($pendingCounts[$aId] ?? 0) + 1;
                    }
                }
            }
        }

        $formattedAssets = [];
        foreach ($assets as $id => $asset) {
            $asset['id'] = $id;

            $items = $asset['items'] ?? [];
            $totalStock = count($items);

            // 2. Count Physical Availability
            $physicalAvailable = 0;
            if (!empty($items)) {
                foreach ($items as $item) {
                    if (($item['status'] ?? 'available') === 'available') {
                        $physicalAvailable++;
                    }
                }
            } else {
                if (($asset['status'] ?? 'available') === 'available') {
                    $physicalAvailable = 1;
                    $totalStock = 1;
                } else {
                    $totalStock = 1; // Assuming singular asset exists
                }
            }

            // 3. Calculate Real Available (Physical - Pending)
            $pendingForThisAsset = $pendingCounts[$id] ?? 0;
            $realAvailable = $physicalAvailable - $pendingForThisAsset;
            if ($realAvailable < 0) $realAvailable = 0;

            $asset['total_stock'] = $totalStock;
            $asset['available_stock'] = $realAvailable; // Use Real Available
            $asset['items_count'] = $totalStock;

            $formattedAssets[] = $asset;
        }

        // Calculate Global Stats for the View Cards
        $stats = [
            'total_items' => 0,
            'available_items' => 0,
            'in_use_items' => 0,
            'issue_items' => 0
        ];

        foreach ($assets as $asset) {
            $items = $asset['items'] ?? [];
            if (!empty($items)) {
                foreach ($items as $item) {
                    $stats['total_items']++;

                    $st = $item['status'] ?? 'available';
                    if ($st == 'available') $stats['available_items']++;
                    elseif ($st == 'in_use') $stats['in_use_items']++;
                    elseif (in_array($st, ['damaged', 'maintenance'])) $stats['issue_items']++;
                }
            } else {
                // Legacy Single Asset Fallback
                $stats['total_items']++;
                $st = $asset['status'] ?? 'available';
                if ($st == 'available') $stats['available_items']++;
                elseif ($st == 'in_use') $stats['in_use_items']++;
                elseif (in_array($st, ['damaged', 'maintenance'])) $stats['issue_items']++;
            }
        }

        // Adjust for pending approvals (If pending, it's technically reserved, so remove from available?)
        // The previous logic in index deducted pending from available.
        // Let's stick to physical status for the cards unless requested otherwise,
        // BUT usually "Available" on dashboard means "Can be borrowed".
        // Let's refine:
        $pendingTotal = array_sum($pendingCounts);
        $stats['available_items'] = max(0, $stats['available_items'] - $pendingTotal);


        return view('assets.index', [
            'assets' => $formattedAssets,
            'stats' => $stats,
            'user' => $user,
            'title' => 'Daftar Aset'
        ]);
    }

    public function show($id)
    {
        $user = Session::get('user');
        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();

        if (!$asset) {
            return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan.');
        }

        // Fetch transactions for this asset to calculate reserved items
        $allTransactions = $this->database->getReference('transactions')->getValue() ?? [];
        $assetTransactions = [];
        $reservedCount = 0;

        foreach ($allTransactions as $transId => $trans) {
            if (($trans['asset_id'] ?? '') === $id) {
                $trans['id'] = $transId;
                $assetTransactions[] = $trans;

                // Count active reservations (pending or approved but not yet checked out)
                if (in_array(($trans['status'] ?? ''), ['waiting_approval', 'approved'])) {
                    $reservedCount++;
                }
            }
        }

        $items = $asset['items'] ?? [];
        $formattedItems = [];

        // We will "visually" assign 'booked' status to the first N available items
        $itemsToMarkAsReserved = $reservedCount;

        foreach ($items as $itemId => $item) {
            $item['id'] = $itemId;

            // Logic to visually reserve items
            if ($itemsToMarkAsReserved > 0 && ($item['status'] ?? 'available') === 'available') {
                $item['status_display'] = 'booked'; // Custom display status
                $item['status_label'] = 'Booked (Queue)';
                $itemsToMarkAsReserved--;
            } else {
                $item['status_display'] = $item['status'] ?? 'available';
                $item['status_label'] = ucfirst($item['status'] ?? 'available');
            }

            $formattedItems[] = $item;
        }

        // Sort by requested_at descending
        usort($assetTransactions, function ($a, $b) {
            return ($b['requested_at'] ?? 0) <=> ($a['requested_at'] ?? 0);
        });

        return view('assets.show', [
            'asset' => $asset,
            'items' => $formattedItems,
            'assetId' => $id,
            'user' => $user,
            'transactions' => $assetTransactions,
            'reservedCount' => $reservedCount,
            'title' => 'Detail Aset'
        ]);
    }

    public function create()
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $locations = $this->database->getReference('locations')->getValue() ?? [];
        $categories = $this->database->getReference('categories')->getValue() ?? [];

        return view('assets.create', [
            'locations' => $locations,
            'categories' => $categories,
            'user' => $user,
            'title' => 'Tambah Aset Baru'
        ]);
    }

    public function store(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->move(public_path('storage/assets'), $imageName);
            $imagePath = 'storage/assets/' . $imageName;
        }

        // Cek apakah asset dengan nama dan kategori yang sama sudah ada (Case Insensitive)
        $allAssets = $this->database->getReference($this->tablename)->getValue() ?? [];
        $existingAssetId = null;
        $currentQty = 0;

        foreach ($allAssets as $id => $asset) {
            if (
                strcasecmp($asset['name'] ?? '', $request->name) === 0 &&
                strcasecmp($asset['category'] ?? '', $request->category) === 0
            ) {
                $existingAssetId = $id;
                $currentQty = count($asset['items'] ?? []);
                // Keep the existing image if not provided new one
                if (!$imagePath && isset($asset['image'])) {
                    $imagePath = $asset['image'];
                }
                break;
            }
        }

        $items = [];
        $prefix = strtoupper(substr($request->category, 0, 3));
        for ($i = 0; $i < $request->quantity; $i++) {
            $sn = $prefix . '-' . date('Ymd') . '-' . strtoupper(uniqid());
            // Gunakan push key firebase untuk ID item agar unik
            $newRefItem = $this->database->getReference($this->tablename)->push()->getKey(); // Just generate a key
            // Actually, we are building an array to push. Better to generate keys manually or let firebase push.
            // Using uniqid('item_') is what I did before.
            $itemId = uniqid('item_');

            $items[$itemId] = [
                'serial_number' => $sn,
                'qr_code' => $sn,
                'condition' => 'good',
                'status' => 'available',
                'created_at' => time()
            ];
        }

        if ($existingAssetId) {
            // Tambahkan items ke asset yang sudah ada
            foreach ($items as $itemKey => $itemData) {
                $this->database->getReference("{$this->tablename}/{$existingAssetId}/items/{$itemKey}")->set($itemData);
            }

            // Update info lain jika perlu (misal deskripsi atau lokasi update terakhir)
            $updateData = [
                'updated_at' => time()
            ];
            if ($imagePath) $updateData['image'] = $imagePath;
            if ($request->location) $updateData['location'] = $request->location;

            $this->database->getReference("{$this->tablename}/{$existingAssetId}")->update($updateData);

            AuditLogger::log('asset_stock_added', [
                'asset_id' => $existingAssetId,
                'name' => $request->name,
                'added_quantity' => $request->quantity
            ], $user['id'], $user['name']);

            return redirect()->route('assets.index')
                ->with('success', "Stok asset '{$request->name}' berhasil ditambahkan sebanyak {$request->quantity} unit.");
        } else {
            // Buat Asset Baru (Parent)
            $newAsset = [
                'name' => $request->name,
                'category' => $request->category,
                'location' => $request->location,
                'description' => $request->description ?? '',
                'image' => $imagePath,
                'created_at' => time(),
                'updated_at' => time(),
                'items' => $items
            ];

            $newRef = $this->database->getReference($this->tablename)->push($newAsset);

            AuditLogger::log('asset_created', [
                'asset_id' => $newRef->getKey(),
                'name' => $request->name,
                'quantity' => $request->quantity
            ], $user['id'], $user['name']);

            return redirect()->route('assets.index')
                ->with('success', "Aset baru berhasil ditambahkan dengan {$request->quantity} unit stok.");
        }
    }

    public function edit($id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403);
        }

        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        if (!$asset) {
            return redirect()->route('assets.index');
        }

        $locations = $this->database->getReference('locations')->getValue() ?? [];
        $categories = $this->database->getReference('categories')->getValue() ?? [];

        return view('assets.edit', [
            'asset' => $asset,
            'assetId' => $id,
            'locations' => $locations,
            'categories' => $categories,
            'user' => $user,
            'title' => 'Edit Aset'
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|max:2048'
        ]);

        $ref = $this->database->getReference("{$this->tablename}/{$id}");
        $asset = $ref->getValue();

        $updateData = [
            'name' => $request->name,
            'category' => $request->category,
            'location' => $request->location,
            'description' => $request->description ?? '',
            'updated_at' => time()
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->move(public_path('storage/assets'), $imageName);
            $updateData['image'] = 'storage/assets/' . $imageName;
        }

        $ref->update($updateData);

        return redirect()->route('assets.show', $id)->with('success', 'Aset updated.');
    }

    public function destroy($id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403);
        }

        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        $items = $asset['items'] ?? [];
        foreach ($items as $item) {
            if (($item['status'] ?? '') === 'in_use') {
                return redirect()->route('assets.index')->with('error', 'Cannot delete asset with items currently in use.');
            }
        }

        $this->database->getReference("{$this->tablename}/{$id}")->remove();
        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }

    public function printQR($id, Request $request)
    {
        $asset = $this->database->getReference("{$this->tablename}/{$id}")->getValue();
        if (!$asset) {
            abort(404);
        }

        $itemsToPrint = [];
        $targetSerial = $request->query('serial');

        if ($targetSerial) {
            // Print specific item
            $itemsToPrint[] = ['serial' => $targetSerial];
        } else {
            // Print all items
            $items = $asset['items'] ?? [];
            foreach ($items as $item) {
                $itemsToPrint[] = ['serial' => $item['serial_number']];
            }
        }

        return view('assets.print-qr', [
            'asset' => $asset,
            'assetId' => $id,
            'assetName' => $asset['name'],
            'itemsToPrint' => $itemsToPrint
        ]);
    }

    public function editItem($assetId, $itemId)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403);
        }

        $asset = $this->database->getReference("{$this->tablename}/{$assetId}")->getValue();
        if (!$asset) {
            abort(404);
        }

        $item = $this->database->getReference("{$this->tablename}/{$assetId}/items/{$itemId}")->getValue();
        if (!$item) {
            abort(404);
        }
        $item['id'] = $itemId;

        return view('assets.edit-item', [
            'asset' => $asset,
            'item' => $item,
            'assetId' => $assetId,
            'itemId' => $itemId,
            'user' => $user,
            'title' => 'Edit Item Aset'
        ]);
    }

    public function updateItem(Request $request, $assetId, $itemId)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'serial_number' => 'required|string|max:100',
            'condition' => 'required|in:good,minor_damage,major_damage',
            'status' => 'required|in:available,in_use,maintenance,damaged',
        ]);

        $path = "{$this->tablename}/{$assetId}/items/{$itemId}";

        $this->database->getReference($path)->update([
            'serial_number' => $request->serial_number,
            'condition' => $request->condition,
            'status' => $request->status,
            'updated_at' => time()
        ]);

        AuditLogger::log('asset_item_updated', [
            'asset_id' => $assetId,
            'item_id' => $itemId,
            'serial_number' => $request->serial_number
        ], $user['id'], $user['name']);

        return redirect()->route('assets.show', $assetId)
            ->with('success', 'Data item berhasil diperbarui.');
    }
}
