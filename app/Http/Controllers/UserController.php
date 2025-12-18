<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class UserController extends Controller
{
    protected $database;

    public function __construct()
    {
        try {
            $credentialsPath = config('firebase.credentials');

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found!");
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri(config('firebase.database.url'));

            $this->database = $factory->createDatabase();

        } catch (\Exception $e) {
            Log::error('Firebase UserController error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Menampilkan semua user
     */
    public function index()
    {
        try {
            $usersRef = $this->database->getReference('users')->getValue();

            $users = [];
            if ($usersRef) {
                foreach ($usersRef as $id => $user) {
                    $users[] = [
                        'id'         => $id,
                        'name'       => $user['name'] ?? '',
                        'email'      => $user['email'] ?? '',
                        'role'       => $user['role'] ?? 'employee',
                        'created_at' => isset($user['created_at'])
                            ? date('Y-m-d H:i:s', $user['created_at'])
                            : '-',
                    ];
                }
            }

            return view('users.index', [
                'users' => $users,
                'title' => 'Manajemen User',
                'user'  => Session::get('user'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data user.');
        }
    }

    /**
     * Form tambah user
     */
    public function create()
    {
        return view('users.create', [
            'roles' => ['admin', 'operator', 'employee'],
            'title' => 'Tambah User Baru',
            'user'  => Session::get('user'),
        ]);
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,operator,employee',
        ]);

        try {
            // Cek email duplikat
            $usersRef = $this->database->getReference('users')->getValue();
            if ($usersRef) {
                foreach ($usersRef as $user) {
                    if (($user['email'] ?? '') === $request->email) {
                        return back()
                            ->withErrors(['email' => 'Email sudah terdaftar.'])
                            ->withInput();
                    }
                }
            }

            // Simpan ke Firebase
            $this->database->getReference('users')->push([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role'       => $request->role,
                'created_at' => time(),
            ]);

            return redirect('/admin/users')->with('success', 'User berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan.'])
                ->withInput();
        }
    }

    /**
     * Form edit user
     */
    public function edit($id)
    {
        try {
            $userRef = $this->database->getReference("users/{$id}")->getValue();

            if (!$userRef) {
                return redirect('/admin/users')->with('error', 'User tidak ditemukan.');
            }

            return view('users.edit', [
                'user' => [
                    'id'    => $id,
                    'name'  => $userRef['name'] ?? '',
                    'email' => $userRef['email'] ?? '',
                    'role'  => $userRef['role'] ?? 'employee',
                ],
                'roles'        => ['admin', 'operator', 'employee'],
                'title'        => 'Edit User',
                'current_user' => Session::get('user'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error editing user: ' . $e->getMessage());
            return redirect('/admin/users')->with('error', 'Gagal memuat data user.');
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'role'  => 'required|in:admin,operator,employee',
        ]);

        try {
            $userRef = $this->database->getReference("users/{$id}")->getValue();
            if (!$userRef) {
                return redirect('/admin/users')->with('error', 'User tidak ditemukan.');
            }

            // Cek email duplikat
            $usersRef = $this->database->getReference('users')->getValue();
            if ($usersRef) {
                foreach ($usersRef as $uid => $user) {
                    if ($uid !== $id && ($user['email'] ?? '') === $request->email) {
                        return back()
                            ->withErrors(['email' => 'Email sudah digunakan user lain.'])
                            ->withInput();
                    }
                }
            }

            $updateData = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'min:6|confirmed',
                ]);
                $updateData['password'] = Hash::make($request->password);
            }

            $this->database->getReference("users/{$id}")->update($updateData);

            return redirect('/admin/users')->with('success', 'User berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan.']);
        }
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        try {
            $currentUser = Session::get('user');

            if (($currentUser['role'] ?? '') !== 'admin') {
                return redirect('/dashboard')->with('error', 'Akses ditolak!');
            }

            $userToDelete = $this->database->getReference("users/{$id}")->getValue();

            if ($userToDelete && ($userToDelete['email'] ?? '') === ($currentUser['email'] ?? '')) {
                return redirect('/admin/users')->with('error', 'Tidak dapat menghapus akun sendiri!');
            }

            $this->database->getReference("users/{$id}")->remove();

            return redirect('/admin/users')->with('success', 'User berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect('/admin/users')->with('error', 'Gagal menghapus user.');
        }
    }
}
