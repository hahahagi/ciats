<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class UserController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
    }

    // Menampilkan semua user
    public function index()
    {
        $usersRef = $this->database->getReference('users')->getValue();
        
        $users = [];
        if ($usersRef) {
            foreach ($usersRef as $id => $user) {
                $users[] = [
                    'id' => $id,
                    'name' => $user['name'] ?? '',
                    'email' => $user['email'] ?? '',
                    'role' => $user['role'] ?? 'employee',
                    'created_at' => isset($user['created_at']) ? date('Y-m-d H:i:s', $user['created_at']) : '-',
                ];
            }
        }

        $data = [
            'users' => $users,
            'title' => 'Manajemen User',
            'user' => Session::get('user'),
        ];

        return view('users.index', $data);
    }

    // Menampilkan form tambah user
    public function create()
    {
        $roles = ['admin', 'operator', 'employee'];
        
        $data = [
            'roles' => $roles,
            'title' => 'Tambah User Baru',
            'user' => Session::get('user'),
        ];

        return view('users.create', $data);
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,operator,employee',
        ]);

        try {
            // Cek apakah email sudah terdaftar
            $usersRef = $this->database->getReference('users')->getValue();
            if ($usersRef) {
                foreach ($usersRef as $user) {
                    if ($user['email'] === $request->email) {
                        return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
                    }
                }
            }

            // Simpan ke Firebase
            $newUserRef = $this->database->getReference('users')->push([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'created_at' => time(),
            ]);

            return redirect('/admin/users')->with('success', 'User berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    // Menghapus user
    public function destroy($id)
    {
        try {
            // Cek apakah user yang login adalah admin
            $currentUser = Session::get('user');
            if ($currentUser['role'] !== 'admin') {
                return redirect('/dashboard')->with('error', 'Akses ditolak!');
            }

            // Hapus user dari Firebase
            $this->database->getReference('users/' . $id)->remove();

            return redirect('/admin/users')->with('success', 'User berhasil dihapus!');
            
        } catch (\Exception $e) {
            return redirect('/admin/users')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}