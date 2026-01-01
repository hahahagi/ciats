<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class AuthController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
    }

    // Menampilkan form login
    public function showLogin()
    {
        if (Session::has('user')) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            // Ambil semua user dari Firebase
            $usersRef = $this->database->getReference('users')->getValue();

            if ($usersRef) {
                foreach ($usersRef as $userId => $userData) {
                    // Cek email dan password
                    if ($userData['email'] === $request->email) {
                        if (Hash::check($request->password, $userData['password'])) {
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

    // Logout
    public function logout()
    {
        Session::flush();
        return redirect('/login')->with('success', 'Logout berhasil!');
    }

    // Dashboard berdasarkan role
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
            // 1. Total Assets & Available
            $assetsRef = $this->database->getReference('assets')->getValue();
            if ($assetsRef) {
                foreach ($assetsRef as $asset) {
                    $stats['total_assets']++;
                    if (($asset['status'] ?? '') === 'available') {
                        $stats['available_assets']++;
                    }
                }
            }

            // 2. Transactions Stats
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

            // 3. Total Users
            $usersRef = $this->database->getReference('users')->getSnapshot();
            $stats['total_users'] = $usersRef->numChildren();

            // 4. Recent Activities
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
            'title' => 'Dashboard CIATS',
            'stats' => $stats,
            'activities' => $activities,
        ];

        return view('dashboard', $data);
    }
}
