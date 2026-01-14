<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

/**
 * Controller AuthController
 *
 * Alur kerja:
 * 1. Konstruktor menginisialisasi koneksi ke Firebase database menggunakan kredensial dari config.
 * 2. Method showLogin menampilkan form login, atau redirect ke dashboard jika sudah login.
 * 3. Method login memvalidasi input, mencari user di Firebase berdasarkan email, verifikasi password dengan Hash::check, dan set session jika berhasil.
 * 4. Method logout membersihkan session dan redirect ke login.
 * 5. Method dashboard memeriksa session, mengambil statistik dan aktivitas dari Firebase, dan menampilkan dashboard sesuai role user.
 *
 * Tujuan: Mengelola autentikasi pengguna menggunakan Firebase sebagai penyimpanan data, dan menampilkan dashboard dengan statistik real-time.
 */
class AuthController extends Controller
{
    protected $database;

    /**
     * Konstruktor: Inisialisasi koneksi Firebase
     */
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
    }

    /**
     * Menampilkan form login employee
     */
    public function showLogin()
    {
        return redirect()->route('login.employee');
    }

    public function showLoginEmployee()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login_employee', ['title' => 'Login Employee']);
    }

    /**
     * Menampilkan form login admin
     */
    public function showLoginAdmin()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login_admin', ['title' => 'Login Admin']);
    }

    /**
     * Menampilkan form login operator
     */
    public function showLoginOperator()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login_operator', ['title' => 'Login Operator']);
    }

    /**
     * Menampilkan form login super admin
     */
    public function showLoginSuperAdmin()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login_super_admin', ['title' => 'Login Super Admin']);
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $loginType = $request->input('login_type', 'employee');

        try {
            // Ambil semua user dari Firebase
            $usersRef = $this->database->getReference('users')->getValue();

            if ($usersRef) {
                foreach ($usersRef as $userId => $userData) {
                    if ($userData['email'] === $request->email) {
                        if (Hash::check($request->password, $userData['password'])) {

                            // Role Check Strict
                            $role = $userData['role'];

                            // 1. Employee Login
                            if ($loginType === 'employee') {
                                if ($role !== 'employee') {
                                    return back()->withErrors(['email' => 'Halaman ini khusus untuk Employee.']);
                                }
                            }

                            // 2. Admin Login
                            elseif ($loginType === 'admin') {
                                if ($role !== 'admin') {
                                    return back()->withErrors(['email' => 'Halaman ini khusus untuk Admin.']);
                                }
                            }

                            // 3. Operator Login
                            elseif ($loginType === 'operator') {
                                if ($role !== 'operator') {
                                    return back()->withErrors(['email' => 'Halaman ini khusus untuk Operator.']);
                                }
                            }

                            // 4. Super Admin Login
                            elseif ($loginType === 'super_admin') {
                                if ($role !== 'super_admin') {
                                    return back()->withErrors(['email' => 'Halaman ini khusus untuk Super Admin.']);
                                }
                            }

                            // Login berhasil
                            Session::put('user', [
                                'id' => $userId,
                                'name' => $userData['name'],
                                'email' => $userData['email'],
                                'role' => $userData['role'],
                            ]);

                            return redirect('/dashboard')->with('success', 'Login berhasil!');
                        }
                        break;
                    }
                }
            }

            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Terjadi kesalahan pada server.',
            ])->withInput();
        }
    }

    /**
     * Logout
     * Alur: Flush semua session, redirect ke login dengan pesan sukses.
     */
    public function logout()
    {
        Session::flush();
        return redirect('/login')->with('success', 'Logout berhasil!');
    }

    /**
     * Dashboard berdasarkan role
     * Alur: Cek session, fetch stats dari Firebase (assets, transactions, users), ambil recent activities, tampilkan view dashboard.
     */
    public function dashboard()
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        $user = Session::get('user');

        // Fetch stats from Firebase
        $stats = [
            'total_assets' => 0,
            'borrowed_assets' => 0,
            'total_users' => 0,
            'pending_requests' => 0,
            'available_assets' => 0,
            'my_requests' => 0,
            'my_active_loans' => 0,
            'my_pending_requests' => 0,
        ];

        try {
            // 1. Total Assets & Available: Loop assets, hitung total dan available items
            $stats['total_assets'] = 0; // Reset
            $stats['available_assets'] = 0; // Reset

            $assetsRef = $this->database->getReference('assets')->getValue();
            if ($assetsRef) {
                foreach ($assetsRef as $asset) {
                    $items = $asset['items'] ?? [];
                    if (!empty($items)) {
                        foreach ($items as $item) {
                            $stats['total_assets']++;
                            if (($item['status'] ?? 'available') === 'available') {
                                $stats['available_assets']++;
                            }
                        }
                    } else {
                        // Legacy Fallback
                        $stats['total_assets']++;
                        if (($asset['status'] ?? 'available') === 'available') {
                            $stats['available_assets']++;
                        }
                    }
                }
            }

            // 2. Transactions Stats: Loop transactions, hitung borrowed, pending, dan stats user
            $transactionsRef = $this->database->getReference('transactions')->getValue();
            if ($transactionsRef) {
                foreach ($transactionsRef as $transaction) {
                    $status = $transaction['status'] ?? '';

                    // Global stats
                    if ($status === 'active') {
                        $stats['borrowed_assets']++;
                    } elseif ($status === 'waiting_approval') {
                        $stats['pending_requests']++;
                    }

                    // User specific stats
                    if (($transaction['user_id'] ?? '') === $user['id']) {
                        $stats['my_requests']++;

                        if ($status === 'active') {
                            $stats['my_active_loans']++;
                        } elseif ($status === 'waiting_approval') {
                            $stats['my_pending_requests']++;
                        }
                    }
                }
            }

            // 3. Total Users: Hitung jumlah children di users
            $usersRef = $this->database->getReference('users')->getSnapshot();
            $stats['total_users'] = $usersRef->numChildren();

            // 4. Recent Activities: Ambil 5 transaksi terbaru berdasarkan timestamp
            $activities = [];
            if ($transactionsRef) {
                foreach ($transactionsRef as $id => $transaction) {
                    $transaction['id'] = $id;
                    $activities[] = $transaction;
                }

                // Sort by timestamp desc
                usort($activities, function ($a, $b) {
                    $tA = $a['updated_at'] ?? $a['created_at'] ?? 0;
                    $tB = $b['updated_at'] ?? $b['created_at'] ?? 0;
                    return $tB - $tA;
                });

                $activities = array_slice($activities, 0, 5);
            }
        } catch (\Exception $e) {
            // Log error or handle gracefully
            $activities = [];
        }

        $data = [
            'user' => $user,
            'title' => 'Dashboard',
            'stats' => $stats,
            'activities' => $activities,
        ];

        return view('dashboard', $data);
    }
}
