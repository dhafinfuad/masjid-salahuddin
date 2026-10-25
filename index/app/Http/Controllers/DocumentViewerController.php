<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentViewerController extends Controller
{
    /**
     * Tampilkan penampil dokumen terpadu dengan bilah navigasi atas (Kembali, Cetak, Unduh).
     */
    public function show(Request $request)
    {
        $file = $request->query('file');
        $fallback = $request->query('fallback', '/');

        // Validasi keamanan parameter file
        if (empty($file)) {
            abort(404, 'Berkas dokumen tidak ditentukan.');
        }

        // Cegah skema berbahaya (javascript:, data:, dsb.)
        $cleanFile = trim($file);
        if (preg_match('/^(javascript|data|vbscript):/i', $cleanFile)) {
            abort(400, 'Tautan berkas tidak valid.');
        }

        // Jika URL eksternal, pastikan berasal dari domain aplikasi yang sama
        if (str_starts_with($cleanFile, 'http://') || str_starts_with($cleanFile, 'https://')) {
            $parsedHost = parse_url($cleanFile, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost();
            if ($parsedHost !== $appHost && $parsedHost !== request()->getHost()) {
                abort(403, 'Akses dokumen lintas domain tidak diizinkan.');
            }
        } elseif (! str_starts_with($cleanFile, '/')) {
            // Cegah path traversal
            if (str_contains($cleanFile, '..')) {
                abort(400, 'Jalur berkas tidak valid.');
            }
            $cleanFile = '/' . ltrim($cleanFile, '/');
        }

        // Redirect langsung ke file PDF fisik agar browser menggunakan PDF viewer bawaan (tanpa header wrapper)
        return redirect()->away($cleanFile, 302, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
