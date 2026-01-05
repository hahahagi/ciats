<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Middleware EncryptCookies
 *
 * Alur kerja:
 * 1. Middleware ini mengenkripsi semua cookies yang dikirim dalam response, kecuali yang tercantum dalam array $except.
 * 2. Cookies yang tidak dienkripsi (dalam $except) akan dikirim dalam bentuk plain text.
 * 3. Secara default, array $except kosong, sehingga semua cookies dienkripsi untuk keamanan.
 *
 * Tujuan: Melindungi data sensitif dalam cookies dari akses tidak sah dengan enkripsi.
 */
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}