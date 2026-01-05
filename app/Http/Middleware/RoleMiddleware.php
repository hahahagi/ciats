<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RoleMiddleware
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah pengguna sudah login dengan memverifikasi keberadaan session 'user'.
 * 2. Jika pengguna belum login, diarahkan ke halaman login dengan pesan error.
 * 3. Jika sudah login, ambil data pengguna dari session dan periksa apakah role pengguna cocok dengan role yang diperlukan (parameter $role).
 * 4. Jika role tidak cocok, simpan pesan error dalam flash session dan arahkan ke dashboard.
 * 5. Jika role cocok, request dilanjutkan ke handler berikutnya.
 *
 * Tujuan: Memastikan hanya pengguna dengan role spesifik yang dapat mengakses route tertentu.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user sudah login
        if (!Session::has('user')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Session::get('user');
        
        // Cek role
        if ($user['role'] !== $role) {
            Session::flash('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
            return redirect('/dashboard');
        }

        return $next($request);
    }
}