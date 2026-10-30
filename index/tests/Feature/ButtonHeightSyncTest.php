<?php

namespace Tests\Feature;

use Tests\TestCase;

class ButtonHeightSyncTest extends TestCase
{
    /** @test */
    public function test_tab_kegiatan_notula_and_youtube_buttons_have_matching_h5_height(): void
    {
        $tabKegiatan = file_get_contents(resource_path('views/livewire/admin/tabs/_tab-kegiatan.blade.php'));

        // Assert that Notula button uses h-5, leading-none, and py-0.5
        $this->assertStringContainsString('class="h-5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200/90 cursor-pointer leading-none shrink-0"', $tabKegiatan);

        // Assert that YouTube button uses h-5, leading-none, and py-0.5
        $this->assertStringContainsString('class="h-5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200/90 leading-none shrink-0"', $tabKegiatan);

        // Assert that old py-1 is no longer used in Notula button
        $this->assertStringNotContainsString('px-1.5 py-1 rounded text-[10px]', $tabKegiatan);
    }

    /** @test */
    public function test_portal_views_notula_and_youtube_buttons_have_matching_h8_height(): void
    {
        $portalPage = file_get_contents(resource_path('views/livewire/portal/portal-page.blade.php'));
        $kegiatanPage = file_get_contents(resource_path('views/livewire/portal/kegiatan-masjid-page.blade.php'));

        $this->assertStringContainsString('h-8 w-[96px] justify-center inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50', $portalPage);
        $this->assertStringContainsString('h-8 w-[96px] justify-center inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-rose-50', $portalPage);

        $this->assertStringContainsString('h-8 w-[96px] justify-center inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50', $kegiatanPage);
        $this->assertStringContainsString('h-8 w-[96px] justify-center inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-rose-50', $kegiatanPage);
    }
}

