<?php

namespace App\Livewire\Portal;

use App\Models\ActivityGallery;
use App\Models\MasjidSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Profil Masjid & Susunan Pengurus — Masjid Salahuddin')]
class ProfilMasjidPage extends Component
{
    public string $activeTab = 'profil'; // 'profil', 'pengurus', 'galeri'
    public string $gallerySearch = '';
    public string $galleryYear = 'all';

    public function mount(): void
    {
        $tab = request()->query('tab');
        if ($tab && in_array($tab, ['profil', 'pengurus', 'galeri'])) {
            $this->activeTab = $tab;
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['profil', 'pengurus', 'galeri'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();
        $documents = array_values($settings->getTakmirDocuments());
        $pengurus = $settings->getTakmirStructure();

        ActivityGallery::ensureTableExists();

        $galleryYears = [(int) date('Y')];
        $galleries = collect();

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('activity_galleries')) {
                $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
                $rawYear = $driver === 'sqlite' ? "strftime('%Y', event_date) as year" : "YEAR(event_date) as year";

                $galleryYears = ActivityGallery::query()
                    ->selectRaw($rawYear)
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->filter()
                    ->map(fn($y) => (int) $y)
                    ->values()
                    ->toArray() ?: [(int) date('Y')];

                $galleries = ActivityGallery::query()
                    ->search($this->gallerySearch)
                    ->year($this->galleryYear)
                    ->orderByDesc('event_date')
                    ->orderByDesc('id')
                    ->get();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('livewire.portal.profil-masjid-page', [
            'settings' => $settings,
            'documents' => $documents,
            'pengurus' => $pengurus,
            'galleries' => $galleries,
            'galleryYears' => $galleryYears,
        ]);
    }
}
