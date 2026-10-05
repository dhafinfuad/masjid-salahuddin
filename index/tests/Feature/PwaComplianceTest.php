<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaComplianceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that Web App Manifest exists and is valid JSON with PWA requirements.
     */
    public function test_web_app_manifest_is_accessible_and_valid(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $content = file_get_contents($manifestPath);
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertEquals('Masjid Salahuddin — KPP Madya Malang', $data['name']);
        $this->assertEquals('Salahuddin', $data['short_name']);
        $this->assertEquals('/?source=pwa', $data['start_url']);
        $this->assertEquals('standalone', $data['display']);
        $this->assertEquals('#102a43', $data['theme_color']);
        $this->assertEquals('#0a192f', $data['background_color']);

        $this->assertNotEmpty($data['icons']);
        $iconSizes = array_column($data['icons'], 'sizes');
        $this->assertContains('192x192', $iconSizes);
        $this->assertContains('512x512', $iconSizes);

        $hasMaskable = false;
        foreach ($data['icons'] as $icon) {
            if (isset($icon['purpose']) && str_contains($icon['purpose'], 'maskable')) {
                $hasMaskable = true;
                break;
            }
        }
        $this->assertTrue($hasMaskable, 'Manifest must contain maskable icon for Android adaptive launcher');
    }

    /**
     * Test that Service Worker file exists and has correct Livewire-safe rules.
     */
    public function test_service_worker_file_exists_and_contains_safety_rules(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $swContent = file_get_contents($swPath);
        $this->assertStringContainsString('masjid-salahuddin', $swContent);
        $this->assertStringContainsString('/livewire/', $swContent, 'Service Worker must exclude /livewire/ requests from caching');
        $this->assertStringContainsString('/offline', $swContent, 'Service Worker must pre-cache offline fallback');
        $this->assertStringContainsString('/admin', $swContent, 'Service Worker must bypass caching for admin CMS');
    }

    /**
     * Test that offline fallback page is rendered properly.
     */
    public function test_offline_page_is_accessible(): void
    {
        $response = $this->get('/offline');
        $response->assertStatus(200);
        $response->assertSee('Anda Sedang Di Luar Jaringan');
        $response->assertSee('Masjid Salahuddin PWA');
    }

    /**
     * Test that PWA icons exist on disk.
     */
    public function test_required_pwa_icons_exist_on_disk(): void
    {
        $icons = [
            'icon-72x72.png',
            'icon-96x96.png',
            'icon-128x128.png',
            'icon-144x144.png',
            'icon-152x152.png',
            'icon-192x192.png',
            'icon-384x384.png',
            'icon-512x512.png',
            'icon-maskable-192x192.png',
            'icon-maskable-512x512.png',
            'apple-touch-icon.png',
        ];

        foreach ($icons as $icon) {
            $path = public_path('images/icons/' . $icon);
            $this->assertFileExists($path, "Icon {$icon} must exist in public/images/icons/");
            $this->assertGreaterThan(1000, filesize($path), "Icon {$icon} must not be empty");
        }

        $this->assertFileExists(public_path('apple-touch-icon.png'), 'Root apple-touch-icon.png must exist for legacy iOS');
    }

    /**
     * Test that layout includes PWA meta tags and script.
     */
    public function test_homepage_includes_pwa_meta_tags(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<link rel="manifest" href="/manifest.json">', false);
        $response->assertSee('<meta name="theme-color" content="#102a43">', false);
        $response->assertSee('<link rel="apple-touch-icon" href="/images/icons/apple-touch-icon.png">', false);
        $response->assertSee('navigator.serviceWorker.register(\'/sw.js\')', false);
        $response->assertSee('pwa-install-banner');
    }
}
