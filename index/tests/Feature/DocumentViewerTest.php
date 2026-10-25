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

    public function test_document_viewer_redirects_directly_to_valid_file(): void
    {
        $response = $this->get('/document-viewer?file=/sk/sk-takmir.pdf&fallback=/profil');

        $response->assertStatus(302);
        $response->assertRedirect('/sk/sk-takmir.pdf');
        $this->assertStringContainsString('no-store', (string)$response->headers->get('Cache-Control'));
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
        $response->assertSee('Kembali', false);
        $response->assertSee('href=', false);
        // Pastikan tombol Kembali tidak disembunyikan di layar HP
        $response->assertDontSee('hidden sm:inline-flex items-center gap-1.5" title="Kembali', false);
        $response->assertSee('inline-flex items-center gap-1.5', false);
    }

    public function test_profil_page_renders_direct_pdf_links_with_target_blank(): void
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('.pdf', false);
    }

    public function test_print_views_render_mobile_download_and_desktop_print(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/kepengurusan/cetak-sk');

        $response->assertStatus(200);
        $response->assertSee('downloadOfficialSkPdf', false);
        $response->assertSee('Unduh PDF', false);
        $response->assertSee('window.print()', false);
        $response->assertSee('afterprint', false);
    }
}
