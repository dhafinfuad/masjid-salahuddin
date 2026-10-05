<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach standard HTTP security headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block/redirect direct access to ngrok tunnel when ORIGIN_SECRET is enforced
        $originSecret = env('ORIGIN_SECRET');
        if ($originSecret && ! app()->environment('local', 'testing')) {
            $host = $request->getHost();
            if (str_contains($host, 'ngrok-free.dev') || str_contains($host, 'ngrok.io') || str_contains($host, 'ngrok-free.app')) {
                if ($request->header('X-Origin-Verify') !== $originSecret) {
                    return redirect()->away('https://masjidsalahuddin.my.id', 301);
                }
            }
        }

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
