<?php

namespace Tests\Feature;

use Tests\TestCase;

class GlobalConfirmDeleteModalTest extends TestCase
{
    /**
     * Memverifikasi struktur markup global confirmDeleteModal tidak dapat dimorph atau dirusak oleh Livewire.
     */
    public function test_global_confirm_delete_modal_has_proper_wire_ignore_and_layout(): void
    {
        $modalsPath = resource_path('views/livewire/admin/tabs/_modals.blade.php');
        $this->assertFileExists($modalsPath);
        $content = file_get_contents($modalsPath);

        // 1. Pastikan modal TIDAK dibungkus dalam <template x-teleport="body"> dan menggunakan wire:ignore.self standar (seperti modal lainnya)
        $this->assertStringNotContainsString('<template x-teleport="body">', $content, 'confirmDeleteModal dilarang dibungkus template x-teleport agar tidak menduplikasi modal di body');
        $this->assertStringContainsString('x-show="confirmDeleteModal.show" x-cloak wire:ignore.self', $content, 'confirmDeleteModal harus menggunakan wire:ignore.self standar seperti modal lainnya');

        // 2. Pastikan footer menggunakan flex justify-end space-x-2 (identik dengan modal lain) dan BEBAS dari wire:key sintetis
        $this->assertMatchesRegularExpression('/flex justify-end space-x-2 border-t/i', $content, 'Footer modal konfirmasi hapus harus menggunakan standar modal lain flex justify-end space-x-2');
        $this->assertStringNotContainsString('wire:key="global-confirm-delete-modal-footer"', $content, 'Footer modal tidak boleh memiliki wire:key yang memicu duplikasi morphdom');
        $this->assertStringNotContainsString('wire:key="global-confirm-delete-modal-submit-btn"', $content, 'Tombol submit tidak boleh memiliki wire:key yang memicu duplikasi morphdom');
        $this->assertStringNotContainsString('wire:key="global-confirm-delete-modal-cancel-btn"', $content, 'Tombol cancel tidak boleh memiliki wire:key yang memicu duplikasi morphdom');

        // 3. Tombol Batal tetap tampil (tidak di-hide dengan x-show) dan memiliki :disabled="confirmDeleteModal.loading"
        $modalPos = strpos($content, 'confirmDeleteModal.show');
        $this->assertNotFalse($modalPos);
        $footerPos = strpos($content, 'flex justify-end space-x-2 border-t', $modalPos);
        $this->assertNotFalse($footerPos);
        $footerHtml = substr($content, $footerPos, 600);
        $this->assertStringNotContainsString('x-show="!confirmDeleteModal.loading"', $footerHtml, 'Tombol Batal tetap harus tampil saat loading');
        $this->assertStringContainsString(':disabled="confirmDeleteModal.loading"', $footerHtml, 'Tombol Batal harus memiliki :disabled="confirmDeleteModal.loading" saat status loading aktif');

        // 4. Pastikan DILARANG KERAS menggunakan <template x-if> di dalam SVG / button (mencegah error fatal cloneNode Alpine)
        $this->assertStringNotContainsString('<template x-if="confirmDeleteModal.loading">', $content, 'Dilarang menggunakan template x-if untuk loading icon');
        $this->assertStringNotContainsString('<template x-if="confirmDeleteModal.type !== \'primary\'">', $content, 'Dilarang menggunakan template x-if di dalam SVG badge');
        $this->assertStringNotContainsString('<template x-if="!confirmDeleteModal.loading', $content, 'Dilarang menggunakan template x-if di dalam tombol submit');
    }

    /**
     * Memverifikasi pemetaan seluruh pemanggil openDeleteModal di seluruh tab admin.
     */
    public function test_all_delete_actions_route_to_global_delete_modal(): void
    {
        $viewsPath = resource_path('views/livewire/admin/tabs');
        $files = glob($viewsPath . '/_tab-*.blade.php');
        $files[] = resource_path('views/livewire/admin/tabs/_modals.blade.php');
        $this->assertNotEmpty($files);

        $actionsFound = [];
        $totalOpenDeleteCalls = 0;
        foreach ($files as $file) {
            $content = file_get_contents($file);
            preg_match_all('/openDeleteModal\s*\(\s*\{\{\s*Js::from\(\[\s*([^\}]+?)\s*\]\)\s*\}\}\s*\)/s', $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $totalOpenDeleteCalls++;
                    if (preg_match('/\'action\'\s*=>\s*\'([^\']+)\'/', $match, $actionMatch)) {
                        $actionsFound[] = $actionMatch[1];
                    }
                }
            }
        }

        // Pastikan total panggilan openDeleteModal mencakup seluruh tab (25 lokasi pemanggilan)
        $this->assertGreaterThanOrEqual(25, $totalOpenDeleteCalls, "Harus ada minimal 25 lokasi pemanggilan openDeleteModal di seluruh antarmuka admin");

        // Pastikan seluruh 13 aksi hapus utama terpetakan dengan benar
        $expectedActions = [
            'deleteFinance',
            'deleteFinanceCategory',
            'deleteKajian',
            'deleteAgenda',
            'deletePrayerDuty',
            'deleteEmployee',
            'deleteSocialProgram',
            'deleteParticipant',
            'deleteUser',
            'deleteGallery',
            'deleteSaran',
            'removeUstadzPhoto',
            'deleteUstadz',
        ];

        foreach ($expectedActions as $expected) {
            $this->assertContains($expected, $actionsFound, "Aksi hapus '$expected' harus terpetakan ke openDeleteModal");
        }
    }

    /**
     * Memverifikasi versi service worker untuk memastikan PWA auto-update berjalan.
     */
    public function test_pwa_service_worker_version(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);
        $content = file_get_contents($swPath);

        $this->assertStringContainsString("CACHE_NAME = 'masjid-salahuddin-v1.0.9'", $content);
    }
}
