<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

/**
 * Controller UserController
 *
 * Alur kerja:
 * 1. Konstruktor menginisialisasi koneksi Firebase dan memeriksa keberadaan file kredensial.
 * 2. Method index mengambil semua user dari Firebase dan menampilkan daftar user.
 * 3. Method create menampilkan form untuk menambah user baru.
 * 4. Method store memvalidasi input, memeriksa duplikasi email, dan menyimpan user baru ke Firebase.
 * 5. Method edit mengambil data user berdasarkan ID dan menampilkan form edit.
 * 6. Method update memvalidasi input, memeriksa duplikasi email, dan memperbarui data user di Firebase.
 * 7. Method destroy memeriksa role admin, mencegah penghapusan akun sendiri, dan menghapus user dari Firebase.
 *
 * Tujuan: Mengelola operasi CRUD untuk user menggunakan Firebase sebagai penyimpanan data.
 */
class UserController extends Controller
{
    protected $database;

    /**
     * Konstruktor: Setup koneksi Firebase dan validasi kredensial
     */
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
     * Alur: Ambil data users dari Firebase, format data, tampilkan view index.
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
     * Alur: Tampilkan view create dengan daftar roles.
     */
    public function create()
    {
        $currentUser = Session::get('user');
        $roles = ['admin', 'operator', 'employee'];

        // Hanya super_admin yang bisa membuat super_admin lain
        if (($currentUser['role'] ?? '') === 'super_admin') {
            array_unshift($roles, 'super_admin');
        }

        return view('users.create', [
            'roles' => $roles,
            'title' => 'Tambah User Baru',
            'user'  => $currentUser,
        ]);
    }

    /**
     * Simpan user baru
     * Alur: Validasi input, cek duplikasi email, simpan ke Firebase jika valid.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:super_admin,admin,operator,employee',
        ]);

        $currentUser = Session::get('user');

        // Proteksi: Hanya super_admin yang bisa assign role super_admin
        if ($request->role === 'super_admin' && ($currentUser['role'] ?? '') !== 'super_admin') {
            return back()->withErrors(['role' => 'Anda tidak memiliki hak akses untuk membuat Super Admin.']);
        }

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
     * Alur: Ambil data user berdasarkan ID dari Firebase, tampilkan view edit jika ditemukan.
     */
    public function edit($id)
    {
        try {
            $userRef = $this->database->getReference("users/{$id}")->getValue();

            if (!$userRef) {
                return redirect('/admin/users')->with('error', 'User tidak ditemukan.');
            }

            $currentUser = Session::get('user');
            $roles = ['admin', 'operator', 'employee'];

            if (($currentUser['role'] ?? '') === 'super_admin') {
                array_unshift($roles, 'super_admin');
            }

            return view('users.edit', [
                'user' => [
                    'id'    => $id,
                    'name'  => $userRef['name'] ?? '',
                    'email' => $userRef['email'] ?? '',
                    'role'  => $userRef['role'] ?? 'employee',
                ],
                'roles'        => $roles,
                'title'        => 'Edit User',
                'current_user' => $currentUser,
            ]);
        } catch (\Exception $e) {
            Log::error('Error editing user: ' . $e->getMessage());
            return redirect('/admin/users')->with('error', 'Gagal memuat data user.');
        }
    }

    /**
     * Update user
     * Alur: Validasi input, cek duplikasi email, update data di Firebase.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'role'  => 'required|in:super_admin,admin,operator,employee',
        ]);

        try {
            $userRef = $this->database->getReference("users/{$id}")->getValue();
            if (!$userRef) {
                return redirect('/admin/users')->with('error', 'User tidak ditemukan.');
            }

            $currentUser = Session::get('user');

            // Proteksi: Hanya super_admin yang bisa assign role super_admin
            if ($request->role === 'super_admin' && ($currentUser['role'] ?? '') !== 'super_admin') {
                return back()->withErrors(['role' => 'Anda tidak memiliki hak akses untuk menjadikan user ini Super Admin.']);
            }

            // Proteksi: Admin biasa tidak bisa mengubah role milik Super Admin
            if (($userRef['role'] ?? '') === 'super_admin' && ($currentUser['role'] ?? '') !== 'super_admin') {
                return back()->withErrors(['role' => 'Anda tidak bisa mengubah data Super Admin.']);
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

            // Izinkan admin dan super_admin
            if (!in_array(($currentUser['role'] ?? ''), ['admin', 'super_admin'])) {
                return redirect('/dashboard')->with('error', 'Akses ditolak!');
            }

            $userToDelete = $this->database->getReference("users/{$id}")->getValue();

            if ($userToDelete && ($userToDelete['email'] ?? '') === ($currentUser['email'] ?? '')) {
                return redirect('/admin/users')->with('error', 'Tidak dapat menghapus akun sendiri!');
            }

            // Proteksi: Admin biasa tidak bisa menghapus Super Admin
            if (($userToDelete['role'] ?? '') === 'super_admin' && ($currentUser['role'] ?? '') !== 'super_admin') {
                return redirect('/admin/users')->with('error', 'AKSES DITOLAK: Hanya sesama Super Admin yang bisa menghapus Super Admin.');
            }

            $this->database->getReference("users/{$id}")->remove();

            return redirect('/admin/users')->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect('/admin/users')->with('error', 'Gagal menghapus user.');
        }
    }
}
