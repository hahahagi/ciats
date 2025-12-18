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
        $data = [
            'user' => $user,
            'title' => 'Dashboard CIATS',
        ];

        return view('dashboard', $data);
    }
}