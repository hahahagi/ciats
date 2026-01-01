<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;

class LocationController extends Controller
{
    protected $database;
    protected $tablename = 'locations';

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
     * Display all locations
     */
    public function index()
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa mengakses.');
        }

        $reference = $this->database->getReference($this->tablename);
        $snapshot = $reference->getSnapshot();
        $locations = $snapshot->getValue() ?? [];

        // Format data with ID
        $formattedLocations = [];
        foreach ($locations as $id => $location) {
            $location['id'] = $id;
            $formattedLocations[] = $location;
        }

        return view('locations.index', [
            'locations' => $formattedLocations,
            'user' => $user,
            'title' => 'Daftar Lokasi'
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa menambah lokasi.');
        }

        return view('locations.create', [
            'user' => $user,
            'title' => 'Tambah Lokasi'
        ]);
    }

    /**
     * Store new location
     */
    public function store(Request $request)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa menambah lokasi.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $newLocation = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'created_by' => $user['id'],
            'created_at' => time(),
        ];

        $this->database->getReference($this->tablename)->push($newLocation);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa mengedit lokasi.');
        }

        $location = $this->database->getReference("{$this->tablename}/{$id}")->getValue();

        if (!$location) {
            return redirect()->route('locations.index')->with('error', 'Lokasi tidak ditemukan.');
        }

        return view('locations.edit', [
            'location' => $location,
            'locationId' => $id,
            'user' => $user,
            'title' => 'Edit Lokasi'
        ]);
    }

    /**
     * Update location
     */
    public function update(Request $request, $id)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa mengupdate lokasi.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $location = $this->database->getReference("{$this->tablename}/{$id}")->getValue();

        if (!$location) {
            return redirect()->route('locations.index')->with('error', 'Lokasi tidak ditemukan.');
        }

        $updateData = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'updated_at' => time(),
        ];

        $this->database->getReference("{$this->tablename}/{$id}")->update($updateData);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil diperbarui!');
    }

    /**
     * Delete location
     */
    public function destroy($id)
    {
        $user = Session::get('user');

        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Hanya operator dan admin yang bisa menghapus lokasi.');
        }

        // Cek apakah lokasi sedang digunakan oleh aset (opsional, tapi disarankan)
        // Untuk saat ini kita skip validasi penggunaan lokasi di aset demi kecepatan,
        // tapi idealnya dicek dulu.

        $this->database->getReference("{$this->tablename}/{$id}")->remove();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus!');
    }
}
