<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

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