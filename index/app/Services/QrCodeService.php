<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Throwable;

class QrCodeService
{
    /**
     * Generate Base64 Data URI (PNG) from given URL or string
     *
     * @param string $data The content/URL to encode
     * @param int $scale Pixel size per QR module
     * @return string Data URI string: 'data:image/png;base64,...'
     */
    public static function generateBase64(string $data, int $scale = 4): string
    {
        try {
            $options = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'scale' => $scale,
                'imageBase64' => true,
            ]);

            return (new QRCode($options))->render($data);
        } catch (Throwable $e) {
            // Fallback to basic SVG data URI if GD encounters any issues
            try {
                return (new QRCode)->render($data);
            } catch (Throwable) {
                return '';
            }
        }
    }
}
