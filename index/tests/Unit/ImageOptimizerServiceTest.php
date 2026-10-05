<?php

namespace Tests\Unit;

use App\Services\ImageOptimizerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerServiceTest extends TestCase
{
    public function test_converts_uploaded_image_to_webp(): void
    {
        Storage::fake('public');

        // Create a fake image 800x600 using GD
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        $img = imagecreatetruecolor(800, 600);
        $blue = imagecolorallocate($img, 10, 45, 90);
        imagefilledrectangle($img, 0, 0, 800, 600, $blue);
        imagejpeg($img, $tmpFile, 95);
        imagedestroy($img);

        $uploaded = new UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);

        $savedPath = ImageOptimizerService::convertToWebp($uploaded, 'galleries', 1920, 82);

        $this->assertStringEndsWith('.webp', $savedPath);
        $this->assertStringStartsWith('galleries/', $savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Verify stored file is WebP
        $fullPath = Storage::disk('public')->path($savedPath);
        $info = getimagesize($fullPath);
        $this->assertEquals('image/webp', $info['mime']);
        $this->assertEquals(800, $info[0]);
        $this->assertEquals(600, $info[1]);

        @unlink($tmpFile);
    }

    public function test_resizes_large_images_within_max_dimension(): void
    {
        Storage::fake('public');

        // Create a large fake image 2400x1200
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_large_') . '.jpg';
        $img = imagecreatetruecolor(2400, 1200);
        imagejpeg($img, $tmpFile, 90);
        imagedestroy($img);

        $uploaded = new UploadedFile($tmpFile, 'large.jpg', 'image/jpeg', null, true);

        // Convert with max dimension 1200
        $savedPath = ImageOptimizerService::convertToWebp($uploaded, 'galleries', 1200, 82);

        $fullPath = Storage::disk('public')->path($savedPath);
        $info = getimagesize($fullPath);
        $this->assertEquals(1200, $info[0]);
        $this->assertEquals(600, $info[1]); // Aspect ratio 2:1 preserved

        @unlink($tmpFile);
    }
}
