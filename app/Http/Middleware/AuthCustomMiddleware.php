<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware AuthCustomMiddleware
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah pengguna sudah login dengan memverifikasi keberadaan session 'user'.
 * 2. Jika session 'user' tidak ada, pesan error disimpan dalam flash session dan pengguna diarahkan ke halaman login.
 * 3. Jika session 'user' ada, request dilanjutkan ke handler berikutnya.
 *
 * Tujuan: Memastikan hanya pengguna yang terautentikasi yang dapat mengakses route yang dilindungi.
 */
class AuthCustomMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah session 'user' ada
        if (!Session::has('user')) {
            // Jika tidak ada, simpan pesan error dalam flash session dan redirect ke login
            Session::flash('error', 'Silakan login terlebih dahulu.');
            return redirect('/login');
        }

        // Jika ada, lanjutkan request
        return $next($request);
    }
}