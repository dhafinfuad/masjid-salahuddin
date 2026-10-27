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

        // 1. Pastikan modal memiliki wire:ignore (BUKAN wire:ignore.self) untuk mencegah Livewire morphdom merusak tombol saat response server tiba
        $this->assertStringContainsString('x-show="confirmDeleteModal.show" x-cloak wire:ignore wire:key="global-confirm-delete-modal"', $content, 'confirmDeleteModal harus menggunakan wire:ignore penuh dan memiliki wire:key');
        $this->assertStringNotContainsString('x-show="confirmDeleteModal.show" x-cloak wire:ignore.self', $content, 'confirmDeleteModal dilarang menggunakan wire:ignore.self karena menyebabkan duplikasi tombol saat morphing');

        // 2. Pastikan footer menggunakan flex gap-2.5, wire:ignore, dan wire:key untuk proteksi mutlak
        $this->assertMatchesRegularExpression('/flex items-center justify-end gap-2\.5 border-t/i', $content, 'Footer modal konfirmasi hapus harus menggunakan gap-2.5');
        $this->assertStringContainsString('wire:ignore wire:key="global-confirm-delete-modal-footer"', $content, 'Footer modal harus memiliki isolasi wire:ignore dan wire:key tersendiri');
        $this->assertStringContainsString('wire:key="global-confirm-delete-modal-submit-btn"', $content, 'Tombol submit harus memiliki wire:key stabil');
        $this->assertStringContainsString('wire:key="global-confirm-delete-modal-cancel-btn"', $content, 'Tombol cancel harus memiliki wire:key stabil');

        // 3. Pastikan tombol konfirmasi memiliki dimensi stabil, tidak menggunakan disabled:opacity-50 (mencegah ghosting pink)
        $this->assertMatchesRegularExpression('/min-w-\[130px\]\s+h-9\s+px-4/i', $content, 'Tombol konfirmasi harus memiliki min-w-[130px] h-9 untuk menjaga stabilitas layout');
        $this->assertStringNotContainsString('disabled:opacity-50', substr($content, strpos($content, 'executeConfirmDelete()'), 200), 'Tombol konfirmasi tidak boleh menggunakan disabled:opacity-50');

        // 4. Pastikan ikon SVG dimuat secara eksklusif menggunakan template x-if
        $this->assertStringContainsString('<template x-if="confirmDeleteModal.loading">', $content);
        $this->assertStringContainsString('<template x-if="!confirmDeleteModal.loading && confirmDeleteModal.type === \'primary\'">', $content);
        $this->assertStringContainsString('<template x-if="!confirmDeleteModal.loading && confirmDeleteModal.type !== \'primary\'">', $content);
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

        $this->assertStringContainsString("CACHE_NAME = 'masjid-salahuddin-v1.0.6'", $content);
    }
}
