<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware Authenticate
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah pengguna sudah terautentikasi menggunakan sistem autentikasi Laravel.
 * 2. Jika pengguna tidak terautentikasi, method redirectTo menentukan path redirect berdasarkan jenis request.
 * 3. Untuk request JSON (API), tidak ada redirect (return null); untuk request web, redirect ke route 'login'.
 *
 * Tujuan: Mengelola redirect untuk pengguna yang tidak terautentikasi, sesuai dengan konteks aplikasi (web atau API).
 */
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jika request expects JSON (untuk API), tidak redirect; jika web, redirect ke login
        return $request->expectsJson() ? null : route('login');
    }
}