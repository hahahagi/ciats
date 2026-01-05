<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Middleware TrustProxies
 *
 * Alur kerja:
 * 1. Middleware ini mengatur proxy yang dipercaya untuk aplikasi, berdasarkan properti $proxies.
 * 2. Menggunakan header tertentu ($headers) untuk mendeteksi dan memproses request yang melalui proxy.
 * 3. Header yang digunakan termasuk X-Forwarded-For, X-Forwarded-Host, dll., untuk mendapatkan informasi asli dari request.
 * 4. Jika proxy tidak dipercaya, informasi seperti IP asli pengguna mungkin tidak akurat.
 *
 * Tujuan: Memastikan aplikasi dapat mengidentifikasi IP asli pengguna dan header yang benar saat berada di balik proxy atau load balancer.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}