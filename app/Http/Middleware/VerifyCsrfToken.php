<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Middleware VerifyCsrfToken
 *
 * Alur kerja:
 * 1. Middleware ini memverifikasi token CSRF pada request yang mengubah data (POST, PUT, PATCH, DELETE).
 * 2. URI yang tercantum dalam array $except dikecualikan dari verifikasi CSRF.
 * 3. Jika token CSRF tidak valid atau hilang, request akan ditolak dengan exception.
 * 4. Secara default, array $except kosong, sehingga semua request diverifikasi.
 *
 * Tujuan: Melindungi aplikasi dari serangan Cross-Site Request Forgery (CSRF) dengan memastikan token valid.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}