<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Portal\ProfilMasjidPage;
use App\Models\ActivityGallery;
use App\Models\MasjidSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        MasjidSetting::getActive();
    }

    public function test_activity_gallery_model_attributes_and_scopes(): void
    {
        $gallery = ActivityGallery::create([
            'title' => 'Kajian Spesial Ramadhan',
            'event_date' => '2026-03-15',
            'location' => 'Ruang Shalat Utama',
            'photos' => ['galleries/test1.jpg', 'galleries/test2.jpg'],
        ]);

        $this->assertEquals(2, $gallery->photos_count);
        $this->assertStringContainsString('galleries/test1.jpg', $gallery->cover_photo_url);
        $this->assertCount(2, $gallery->photo_urls);

        // Test search scope
        $this->assertEquals(1, ActivityGallery::search('Ramadhan')->count());
        $this->assertEquals(1, ActivityGallery::search('Utama')->count());
        $this->assertEquals(0, ActivityGallery::search('NonExistentTerm')->count());

        // Test year scope
        $this->assertEquals(1, ActivityGallery::year('2026')->count());
        $this->assertEquals(0, ActivityGallery::year('2025')->count());
    }

    public function test_portal_profil_page_renders_galeri_tab_and_filters(): void
    {
        ActivityGallery::create([
            'title' => 'Shalat Idul Fitri 1447 H',
            'event_date' => '2026-03-21',
            'location' => 'Halaman KPP Madya Malang',
            'photos' => ['galleries/idul-fitri.jpg'],
        ]);

        Livewire::test(ProfilMasjidPage::class)
            ->assertSee('Profil Masjid')
            ->assertSee('Galeri Kegiatan')
            ->set('activeTab', 'galeri')
            ->assertSee('Shalat Idul Fitri 1447 H')
            ->assertSee('Halaman KPP Madya Malang')
            ->set('gallerySearch', 'TidakAda')
            ->assertDontSee('Shalat Idul Fitri 1447 H')
            ->assertSee('Tidak ada galeri kegiatan ditemukan')
            ->set('gallerySearch', 'Idul')
            ->assertSee('Shalat Idul Fitri 1447 H');
    }

    public function test_admin_dashboard_can_manage_activity_galleries(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $gallery = ActivityGallery::create([
            'title' => 'Santunan Anak Yatim',
            'event_date' => '2026-04-10',
            'location' => 'Masjid Salahuddin',
            'photos' => ['galleries/santunan.jpg'],
        ]);

        // Open create modal
        Livewire::actingAs($admin)
            ->test(AdminDashboard::class)
            ->set('currentTab', 'galeri')
            ->assertSee('Galeri Kegiatan Masjid')
            ->assertSee('Santunan Anak Yatim')
            ->call('openCreateGalleryModal')
            ->assertSet('showGalleryModal', true)
            ->set('galleryTitle', 'Buka Puasa Bersama')
            ->set('galleryDate', '2026-04-15')
            ->set('galleryLocation', 'Serambi Masjid')
            ->call('saveGallery')
            ->assertHasNoErrors()
            ->assertSet('showGalleryModal', false);

        $this->assertDatabaseHas('activity_galleries', [
            'title' => 'Buka Puasa Bersama',
            'location' => 'Serambi Masjid',
        ]);

        // Edit gallery
        $created = ActivityGallery::where('title', 'Buka Puasa Bersama')->first();
        Livewire::actingAs($admin)
            ->test(AdminDashboard::class)
            ->call('openEditGalleryModal', $created->id)
            ->assertSet('galleryTitle', 'Buka Puasa Bersama')
            ->set('galleryTitle', 'Buka Puasa Bersama Akbar')
            ->call('saveGallery');

        $this->assertDatabaseHas('activity_galleries', [
            'id' => $created->id,
            'title' => 'Buka Puasa Bersama Akbar',
        ]);

        // Delete gallery
        Livewire::actingAs($admin)
            ->test(AdminDashboard::class)
            ->call('deleteGallery', $created->id);

        $this->assertDatabaseMissing('activity_galleries', [
            'id' => $created->id,
        ]);
    }
}
