<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DocumentViewerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@masjidsalahuddin.id',
            'password' => Hash::make('password'),
            'role' => 'Master',
            'status' => 'AKTIF',
        ]);
    }

    public function test_document_viewer_renders_properly_with_valid_file(): void
    {
        $response = $this->get('/document-viewer?file=/sk/sk-takmir.pdf&fallback=/profil');

        $response->assertStatus(200);
        $response->assertSee('Penampil Dokumen — Masjid Salahuddin');
        $response->assertSee('handleSmartBack');
        $response->assertSee('Kembali');
        $response->assertSee('Cetak');
        $response->assertDontSee('Unduh');
        $response->assertSee('src="/sk/sk-takmir.pdf"', false);
        $response->assertSee('href="/sk/sk-takmir.pdf"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertDontSee('contentWindow.print');
    }

    public function test_document_viewer_aborts_when_file_is_missing(): void
    {
        $response = $this->get('/document-viewer');

        $response->assertStatus(404);
    }

    public function test_document_viewer_blocks_malicious_schemes(): void
    {
        $response = $this->get('/document-viewer?file=javascript:alert(1)');
        $response->assertStatus(400);

        $response2 = $this->get('/document-viewer?file=data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==');
        $response2->assertStatus(400);
    }

    public function test_document_viewer_blocks_path_traversal(): void
    {
        $response = $this->get('/document-viewer?file=../../etc/passwd');
        $response->assertStatus(400);
    }

    public function test_document_viewer_blocks_cross_domain_external_urls(): void
    {
        $response = $this->get('/document-viewer?file=https://malicious-site.com/evil.pdf');
        $response->assertStatus(403);
    }

    public function test_print_views_render_visible_smart_back_button(): void
    {
        $this->actingAs($this->admin);

        // Uji salah satu view cetak, misalnya pratinjau SK takmir resmi
        $response = $this->get('/admin/kepengurusan/cetak-sk');

        $response->assertStatus(200);
        $response->assertSee('window.history.back', false);
        $response->assertSee('Kembali', false);
        // Pastikan tidak disembunyikan di layar HP
        $response->assertDontSee('hidden sm:inline-flex', false);
    }
}
