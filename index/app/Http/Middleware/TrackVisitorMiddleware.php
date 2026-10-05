<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware
{
    /**
     * Handle an incoming request (Pass through instantly).
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Terminate request lifecycle (Runs in background AFTER response is sent to browser).
     * 0 millisecond delay impact on visitor page load.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            // 1. Hanya catat request GET sukses (Status 200)
            if ($request->method() !== 'GET' || $response->getStatusCode() !== 200) {
                return;
            }

            // 2. Filter rute yang tidak perlu dicatat
            $path = '/' . ltrim($request->path(), '/');

            // Abaikan rute internal, admin, livewire, auth, healthcheck, dan aset PWA offline
            if (
                $request->is('admin*') ||
                $request->is('livewire*') ||
                $request->is('auth*') ||
                $request->is('email*') ||
                $request->is('offline*') ||
                $request->is('up') ||
                $request->is('resources*') ||
                $request->is('images*') ||
                $request->ajax()
            ) {
                return;
            }

            // Abaikan file statis atau aset
            if (preg_match('/\.(ico|png|jpg|jpeg|webp|svg|gif|css|js|map|txt|xml|json|pdf|xlsx|woff|woff2|ttf)$/i', $path)) {
                return;
            }

            $userAgent = $request->userAgent() ?? '';

            // 3. Abaikan Bot, Crawler, Scanner, dan Link Previewer
            if (empty($userAgent) || preg_match('/(bot|crawl|spider|slurp|curl|wget|uptime|ahrefs|semrush|bytespider|facebookexternalhit|whatsapp|preview)/i', $userAgent)) {
                return;
            }

            $ip = $request->ip();
            if (empty($ip)) {
                return;
            }

            // 4. Deduplikasi Cerdas (Cache 15 Menit):
            // Satu IP membuka halaman yang sama berulang kali dalam 15 menit hanya dihitung 1 sesi kunjungan
            $cacheKey = 'visitor_hit:' . md5($ip . '|' . $path);
            if (Cache::has($cacheKey)) {
                return;
            }
            Cache::put($cacheKey, 1, now()->addMinutes(15));

            // 5. Deteksi Perangkat, Platform, dan Browser secara ringan
            $deviceType = 'desktop';
            if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $userAgent)) {
                $deviceType = 'tablet';
            } elseif (preg_match('/(mobile|iphone|ipod|android|blackberry|opera mini|iemobile)/i', $userAgent)) {
                $deviceType = 'mobile';
            }

            // Platform OS
            $platform = 'Lainnya';
            if (preg_match('/android/i', $userAgent)) {
                $platform = 'Android';
            } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
                $platform = 'iOS';
            } elseif (preg_match('/windows nt/i', $userAgent)) {
                $platform = 'Windows';
            } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
                $platform = 'macOS';
            } elseif (preg_match('/linux/i', $userAgent)) {
                $platform = 'Linux';
            }

            // Browser
            $browser = 'Lainnya';
            if (preg_match('/edg/i', $userAgent)) {
                $browser = 'Edge';
            } elseif (preg_match('/chrome|crios/i', $userAgent)) {
                $browser = 'Chrome';
            } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
                $browser = 'Firefox';
            } elseif (preg_match('/safari/i', $userAgent)) {
                $browser = 'Safari';
            } elseif (preg_match('/opr|opera/i', $userAgent)) {
                $browser = 'Opera';
            }

            // 6. Simpan Log Ringkas ke Database (Pastikan tabel sudah ada)
            VisitorLog::ensureTableExists();
            VisitorLog::create([
                'ip_address' => $ip,
                'url' => Str::limit($path, 250),
                'method' => 'GET',
                'device_type' => $deviceType,
                'platform' => $platform,
                'browser' => $browser,
                'user_agent' => Str::limit($userAgent, 490),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Fail-safe: Jangan pernah membiarkan logging mengganggu atau menghasilkan error
        }
    }
}
