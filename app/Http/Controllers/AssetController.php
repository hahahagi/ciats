<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class AssetController extends Controller
{
    protected $database;
    protected $tablename = 'assets';

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->getSnapshot();
        $assets = $snapshot->getValue();

        if (!$assets) $assets = [];

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'location' => 'required'
        ]);

        $newData = [
            'name' => $request->name,
            'category' => $request->category,
            'serial_number' => $request->serial_number ?? '-',
            'status' => 'available',
            'location' => $request->location,
            'created_at' => time()
        ];

        $this->database->getReference($this->tablename)->push($newData);

        return redirect('/assets')->with('success', 'Aset berhasil ditambahkan!');
    }

    // --- FITUR BARU: EDIT ---
    public function edit($id)
    {
        // Ambil data spesifik berdasarkan ID (Key)
        $reference = $this->database->getReference($this->tablename . '/' . $id);
        $snapshot = $reference->getSnapshot();
        $asset = $snapshot->getValue();

        if (!$asset) {
            return redirect('/assets')->with('error', 'Data tidak ditemukan');
        }

        // Kirim ID dan Data ke View
        return view('assets.edit', compact('asset', 'id'));
    }

    // --- FITUR BARU: UPDATE ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'location' => 'required'
        ]);

        $updateData = [
            'name' => $request->name,
            'category' => $request->category,
            'serial_number' => $request->serial_number ?? '-',
            'location' => $request->location,
            // Status tidak diupdate disini dulu (nanti via fitur tracking)
        ];

        // Update ke Firebase path spesifik
        $this->database->getReference($this->tablename . '/' . $id)->update($updateData);

        return redirect('/assets')->with('success', 'Data aset berhasil diperbarui!');
    }

    // --- FITUR BARU: DELETE ---
    public function destroy($id)
    {
        // Hapus node berdasarkan ID
        $this->database->getReference($this->tablename . '/' . $id)->remove();

        return redirect('/assets')->with('success', 'Aset berhasil dihapus!');
    }
}
