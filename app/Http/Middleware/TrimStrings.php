<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Middleware TrimStrings
 *
 * Alur kerja:
 * 1. Middleware ini memotong (trim) spasi di awal dan akhir dari semua string input dalam request.
 * 2. Atribut yang tercantum dalam array $except tidak akan di-trim (misalnya password, karena spasi mungkin penting).
 * 3. Secara default, atribut seperti 'current_password', 'password', dan 'password_confirmation' dikecualikan.
 *
 * Tujuan: Membersihkan input string dari spasi yang tidak diinginkan untuk konsistensi data.
 */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}