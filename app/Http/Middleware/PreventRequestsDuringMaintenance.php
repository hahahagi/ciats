<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/**
 * Middleware PreventRequestsDuringMaintenance
 *
 * Alur kerja:
 * 1. Middleware ini memeriksa apakah aplikasi dalam mode maintenance.
 * 2. Jika dalam mode maintenance, semua request dicegah kecuali URI yang tercantum dalam array $except.
 * 3. URI yang dikecualikan (dalam $except) tetap dapat diakses selama maintenance.
 * 4. Secara default, array $except kosong, sehingga semua request dicegah selama maintenance.
 *
 * Tujuan: Mencegah akses ke aplikasi selama maintenance, sambil mengizinkan akses ke URI tertentu jika diperlukan.
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}