<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

/**
 * Middleware ValidateSignature
 *
 * Alur kerja:
 * 1. Middleware ini memvalidasi signature pada URL untuk route yang ditandatangani (signed routes).
 * 2. Parameter query string yang tercantum dalam array $except akan diabaikan saat menghitung signature.
 * 3. Jika signature tidak valid, request akan ditolak (biasanya dengan exception).
 * 4. Secara default, array $except kosong, tapi dapat diisi dengan parameter seperti 'fbclid' atau 'utm_*' yang tidak mempengaruhi signature.
 *
 * Tujuan: Memastikan integritas URL signed routes dengan mencegah modifikasi yang tidak sah.
 */
class ValidateSignature extends Middleware
{
    /**
     * The names of the query string parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'fbclid',
        // 'utm_campaign',
        // 'utm_content',
        // 'utm_medium',
        // 'utm_source',
        // 'utm_term',
    ];
}