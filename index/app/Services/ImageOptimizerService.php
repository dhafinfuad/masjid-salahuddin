<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Mengonversi berkas foto yang diunggah ke format WebP terkompresi
     * dengan tetap mempertahankan ketajaman, warna, dan detail foto.
     *
     * @param UploadedFile|string $file Berkas foto yang diunggah atau path file lokal
     * @param string $directory Subdirektori penyimpanan pada disk public (misal: 'galleries')
     * @param int $maxDimension Batas dimensi maksimal lebar/tinggi dalam piksel (default: 1920)
     * @param int $quality Kualitas WebP (1-100, default: 82 untuk kompresi optimal tanpa penurunan visual)
     * @return string Path berkas webp tersimpan pada disk public
     */
    public static function convertToWebp(
        UploadedFile|string $file,
        string $directory = 'galleries',
        int $maxDimension = 1920,
        int $quality = 82
    ): string {
        $realPath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        // Fallback jika ekstensi GD atau fungsi imagewebp tidak aktif
        if (! file_exists($realPath) || ! function_exists('imagewebp') || ! function_exists('imagecreatefromstring')) {
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return $file;
        }

        try {
            $imageData = @file_get_contents($realPath);
            if (! $imageData) {
                throw new \RuntimeException('Gagal membaca data berkas gambar.');
            }

            $sourceImage = @imagecreatefromstring($imageData);
            if (! $sourceImage) {
                throw new \RuntimeException('Format gambar tidak dikenali atau berkas rusak.');
            }

            // 1. Koreksi orientasi EXIF (khusus kamera smartphone iPhone / Samsung)
            if ($file instanceof UploadedFile && function_exists('exif_read_data')) {
                try {
                    $exif = @exif_read_data($realPath);
                    if (! empty($exif['Orientation'])) {
                        $sourceImage = match ((int) $exif['Orientation']) {
                            3 => imagerotate($sourceImage, 180, 0),
                            6 => imagerotate($sourceImage, -90, 0),
                            8 => imagerotate($sourceImage, 90, 0),
                            default => $sourceImage,
                        };
                    }
                } catch (\Throwable $e) {
                    // Abaikan jika EXIF tidak dapat dibaca
                }
            }

            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // 2. Hitung dimensi proporsional (hanya perkecil jika melebihi batas, tanpa upscaling)
            $targetWidth = $originalWidth;
            $targetHeight = $originalHeight;

            if ($originalWidth > $maxDimension || $originalHeight > $maxDimension) {
                if ($originalWidth >= $originalHeight) {
                    $targetWidth = $maxDimension;
                    $targetHeight = (int) round(($originalHeight / $originalWidth) * $maxDimension);
                } else {
                    $targetHeight = $maxDimension;
                    $targetWidth = (int) round(($originalWidth / $originalHeight) * $maxDimension);
                }
            }

            // 3. Resampling gambar dengan kualitas tinggi (anti-aliasing & ketajaman detail)
            $optimizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // Menjaga transparansi untuk gambar PNG/WebP berlatar transparan
            imagealphablending($optimizedImage, false);
            imagesavealpha($optimizedImage, true);
            $transparent = imagecolorallocatealpha($optimizedImage, 0, 0, 0, 127);
            imagefilledrectangle($optimizedImage, 0, 0, $targetWidth, $targetHeight, $transparent);

            imagecopyresampled(
                $optimizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $targetWidth, $targetHeight,
                $originalWidth, $originalHeight
            );

            // 4. Encode ke format WebP berkualitas tinggi
            ob_start();
            imagewebp($optimizedImage, null, $quality);
            $webpBinary = ob_get_clean();

            // Bebaskan resource memori GD
            imagedestroy($sourceImage);
            imagedestroy($optimizedImage);

            if (! $webpBinary) {
                throw new \RuntimeException('Gagal melakukan kompresi WebP.');
            }

            // 5. Simpan ke Storage disk public dengan nama unik
            $filename = Str::random(40) . '.webp';
            $storagePath = trim($directory, '/') . '/' . $filename;

            Storage::disk('public')->put($storagePath, $webpBinary);

            return $storagePath;
        } catch (\Throwable $e) {
            report($e);
            // Fail-safe jika terjadi kesalahan konversi
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return $file;
        }
    }
}
