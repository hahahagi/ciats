<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RedirectIfAuthenticated
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah pengguna sudah terautentikasi menggunakan guards yang ditentukan (atau default guard jika tidak ada).
 * 2. Jika pengguna sudah terautentikasi pada salah satu guard, diarahkan ke halaman home (RouteServiceProvider::HOME).
 * 3. Jika tidak terautentikasi pada semua guard, request dilanjutkan ke handler berikutnya.
 *
 * Tujuan: Mencegah pengguna yang sudah login mengakses halaman login atau registrasi, dengan mengarahkan mereka ke home.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Tentukan guards yang akan diperiksa (default null jika kosong)
        $guards = empty($guards) ? [null] : $guards;

        // Loop melalui setiap guard
        foreach ($guards as $guard) {
            // Jika user terautentikasi pada guard ini, redirect ke home
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Jika tidak terautentikasi, lanjutkan request
        return $next($request);
    }
}