<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Middleware CheckRole
 *
 * Alur kerja:
 * 1. Middleware ini mengambil data pengguna dari session.
 * 2. Jika pengguna tidak ada (belum login), diarahkan ke halaman login.
 * 3. Jika pengguna ada, periksa apakah role pengguna termasuk dalam daftar roles yang diizinkan (parameter middleware).
 * 4. Jika role tidak cocok, tampilkan error 403 (Unauthorized).
 * 5. Jika role cocok, request dilanjutkan ke handler berikutnya.
 *
 * Tujuan: Memastikan hanya pengguna dengan role tertentu yang dapat mengakses route yang dilindungi.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Ambil data user dari session
        $user = Session::get('user');
        
        // Jika user tidak ada, redirect ke login
        if (!$user) {
            return redirect('/login');
        }

        // Periksa apakah role user ada dalam daftar roles yang diizinkan
        if (!in_array($user['role'], $roles)) {
            // Jika tidak, abort dengan error 403
            abort(403, 'Unauthorized access.');
        }

        // Jika role cocok, lanjutkan request
        return $next($request);
    }
}