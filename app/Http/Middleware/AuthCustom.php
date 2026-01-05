<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Middleware AuthCustom
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah pengguna sudah login dengan memverifikasi keberadaan session 'user'.
 * 2. Jika session 'user' tidak ada, pengguna diarahkan ke halaman login dengan pesan error.
 * 3. Jika session 'user' ada, request dilanjutkan ke handler berikutnya.
 *
 * Tujuan: Memastikan hanya pengguna yang terautentikasi yang dapat mengakses route yang dilindungi.
 */
class AuthCustom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Periksa apakah session 'user' ada
        if (!Session::has('user')) {
            // Jika tidak ada, redirect ke login dengan pesan error
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika ada, lanjutkan request
        return $next($request);
    }
}