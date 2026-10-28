<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalToastNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifikasi bahwa layout utama memiliki satu toast global yang melayang di bawah header.
     */
    public function test_layout_app_contains_single_global_toast_positioned_at_top(): void
    {
        $layoutContent = file_get_contents(resource_path('views/layouts/app.blade.php'));

        // Harus memiliki listener toast global
        $this->assertStringContainsString('@toast.window="trigger($event.detail.message)"', $layoutContent);

        // Harus diposisikan di atas layar melayang anggun di bawah header (top-16 pada mobile)
        $this->assertStringContainsString('top-16', $layoutContent);
        $this->assertStringContainsString('inset-x-4', $layoutContent);
        $this->assertStringContainsString('-translate-y-4', $layoutContent);

        // Hanya boleh ada 1 kemunculan @toast.window di layout
        $occurrences = substr_count($layoutContent, '@toast.window');
        $this->assertSame(1, $occurrences, 'Layout app.blade.php harus memiliki tepat 1 listener toast global.');
    }

    /**
     * Verifikasi bahwa markup toast lokal di admin-dashboard.blade.php telah dihapus bersih.
     */
    public function test_admin_dashboard_does_not_contain_duplicate_toast_listener(): void
    {
        $dashboardContent = file_get_contents(resource_path('views/livewire/admin/admin-dashboard.blade.php'));

        $this->assertStringNotContainsString('@toast.window', $dashboardContent, 'admin-dashboard.blade.php tidak boleh memiliki listener toast lokal.');
        $this->assertStringNotContainsString('Floating Auto-Dismiss Toast Notification', $dashboardContent);
    }

    /**
     * Verifikasi ketika halaman admin dirender, hanya terdapat 1 instance toast di seluruh DOM HTML.
     */
    public function test_rendered_admin_page_has_exactly_one_toast_element(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get(route('admin.tab', ['tab' => 'programs']));

        $response->assertStatus(200);

        // Pastikan HTML hasil render hanya memiliki satu listener toast window
        $html = $response->getContent();
        $toastCount = substr_count($html, '@toast.window');
        $this->assertSame(1, $toastCount, 'Halaman admin yang dirender hanya boleh memiliki tepat 1 elemen toast global.');
    }
}
