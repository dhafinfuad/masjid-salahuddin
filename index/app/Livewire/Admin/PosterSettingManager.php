<?php

namespace App\Livewire\Admin;

use App\Models\PosterSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PosterSettingManager extends Component
{
    use WithFileUploads;

    public int $template = 1;
    public string $masjid_line1 = 'KPP Madya Malang';
    public string $masjid_line2 = 'Masjid Salahuddin';
    public string $footer_label = 'Informasi Kajian :';
    public string $footer_url = 'masjidsalahuddin.my.id';

    public string $jarkom_kajian_template = '';
    public string $jarkom_jumat_template = '';

    public $logo_file = null;
    public ?string $logo_url = null;
    public int $logo_size = 32;

    public int $title_font_size = 42;
    public int $desc_font_size = 15;
    public int $title_y = 10;
    public int $desc_y = 8;
    public int $title_desc_gap = 14;
    public int $schedule_gap = 20;
    public int $content_y = 0;

    public bool $show_pattern = true;
    public bool $show_leaves = true;

    // Sample preview content that can also be adjusted by the admin for testing
    public string $preview_title1 = 'Kajian';
    public string $preview_title2 = 'Tematik';
    public string $preview_subtitle = "Judul Kajian Judul Kajian Judul Kajian\nJudul Kajian Judul Kajian";
    public string $preview_date = '22 Juni 2027';
    public string $preview_time = '08.30–10.00 WIB';
    public string $preview_location = 'Masjid Salahuddin';
    public string $preview_speaker = "Nama Ustad Nama Ustad\nNama Ustad";
    public bool $isEmbedded = false;

    public function mount(bool $isEmbedded = false): void
    {
        $this->isEmbedded = $isEmbedded;
        $config = PosterSetting::getAppConfig();
        $this->template = (int) ($config['template'] ?? 1);
        $this->masjid_line1 = (string) ($config['masjid_line1'] ?? 'KPP Madya Malang');
        $this->masjid_line2 = (string) ($config['masjid_line2'] ?? 'Masjid Salahuddin');
        $this->footer_label = (string) ($config['footer_label'] ?? 'Informasi Kajian :');
        $this->footer_url = (string) ($config['footer_url'] ?? 'masjidsalahuddin.my.id');
        $this->jarkom_kajian_template = (string) ($config['jarkom_kajian_template'] ?? PosterSetting::defaultJarkomKajianTemplate());
        $this->jarkom_jumat_template = (string) ($config['jarkom_jumat_template'] ?? PosterSetting::defaultJarkomJumatTemplate());
        $rawLogo = !empty($config['logo_url']) ? (string) $config['logo_url'] : '/resources/Logo Masjid Salahuddin.svg';
        if (str_contains($rawLogo, 'Logo Masjid-Corner.svg') || str_contains($rawLogo, 'logo-resmi-masjid.svg')) {
            $rawLogo = '/resources/Logo Masjid Salahuddin.svg';
        }
        $this->logo_url = $rawLogo;
        $this->logo_size = (int) ($config['logo_size'] ?? 32);
        $this->title_font_size = (int) ($config['title_font_size'] ?? 42);
        $this->desc_font_size = (int) ($config['desc_font_size'] ?? 15);
        $this->title_y = (int) ($config['title_y'] ?? 10);
        $this->desc_y = (int) ($config['desc_y'] ?? 8);
        $this->title_desc_gap = (int) ($config['title_desc_gap'] ?? 14);
        $this->schedule_gap = (int) ($config['schedule_gap'] ?? 20);
        $this->content_y = (int) ($config['content_y'] ?? 0);
        $this->show_pattern = (bool) ($config['show_pattern'] ?? true);
        $this->show_leaves = (bool) ($config['show_leaves'] ?? true);

        $this->preview_title1 = (string) ($config['preview_title1'] ?? ($this->template === 2 ? '' : 'Kajian'));
        $title2Val = (string) ($config['preview_title2'] ?? ($this->template === 2 ? 'KAJIAN PEKANAN' : 'Tematik'));
        if ($this->template === 2 && ($title2Val === 'PEKANAN' || $title2Val === 'Tematik')) {
            $title2Val = 'KAJIAN PEKANAN';
        }
        $this->preview_title2 = $title2Val;
        $this->preview_subtitle = (string) ($config['preview_subtitle'] ?? "Judul Kajian Judul Kajian Judul Kajian\nJudul Kajian Judul Kajian");
    }

    public function selectTemplate(int $type): void
    {
        $this->template = in_array($type, [1, 2], true) ? $type : 1;
        if ($this->template === 2) {
            $this->preview_title1 = '';
            if (empty($this->preview_title2) || in_array($this->preview_title2, ['Tematik', 'PEKANAN'], true)) {
                $this->preview_title2 = 'KAJIAN PEKANAN';
            }
            $this->title_y = 6;
            $this->desc_y = 2;
        } else {
            if (empty($this->preview_title1)) {
                $this->preview_title1 = 'Kajian';
            }
            if (empty($this->preview_title2) || in_array($this->preview_title2, ['PEKANAN', 'KAJIAN PEKANAN'], true)) {
                $this->preview_title2 = 'Tematik';
            }
            $this->title_y = 10;
            $this->desc_y = 8;
        }

        $this->dispatch('poster-template-changed', [
            'template' => $this->template,
            'config' => $this->getConfigArray()
        ]);
    }

    public function applyPreset(int $type): void
    {
        if ($type === 1) {
            $this->template = 1;
            $this->title_font_size = 42;
            $this->desc_font_size = 15;
            $this->title_desc_gap = 14;
            $this->title_y = 10;
            $this->desc_y = 8;
            $this->schedule_gap = 20;
            $this->content_y = 0;
            $this->preview_title1 = 'Kajian';
            $this->preview_title2 = 'Tematik';
            $this->dispatch('toast', ['message' => 'Preset 1 (Kajian Tematik Rapi) berhasil diterapkan!']);
        } else {
            $this->template = 2;
            $this->title_font_size = 42;
            $this->desc_font_size = 15;
            $this->title_desc_gap = 14;
            $this->title_y = 6;
            $this->desc_y = 2;
            $this->schedule_gap = 20;
            $this->content_y = 0;
            $this->preview_title1 = '';
            $this->preview_title2 = 'PEKANAN';
            $this->dispatch('toast', ['message' => 'Preset 2 (Kajian Rutin Pekanan Rapi) berhasil diterapkan!']);
        }

        $this->dispatch('poster-template-changed', [
            'template' => $this->template,
            'config' => $this->getConfigArray()
        ]);
    }

    public function updatedLogoFile(): void
    {
        $this->validate([
            'logo_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
        ], [
            'logo_file.mimes' => 'Format file logo harus berupa PNG, SVG, JPG, atau WebP.',
            'logo_file.max' => 'Ukuran file logo maksimal 3 MB.',
        ]);

        if ($this->logo_file) {
            $path = $this->logo_file->store('poster-logos', 'public');
            $this->logo_url = Storage::url($path);
            $this->logo_file = null;

            // Simpan pembaruan logo ke database poster_settings
            $config = $this->getConfigArray();
            PosterSetting::updateOrCreate(
                ['name' => 'default'],
                [
                    'template_type' => $this->template,
                    'config' => $config,
                ]
            );

            $this->dispatch('toast', ['message' => 'Logo masjid berhasil diunggah & pratinjau diperbarui!']);
        }
    }

    public function useDefaultMosqueLogo(): void
    {
        $this->logo_file = null;
        $this->logo_url = '/resources/Logo Masjid Salahuddin.svg';

        $config = $this->getConfigArray();
        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => $this->template,
                'config' => $config,
            ]
        );

        $this->dispatch('toast', ['message' => 'Logo resmi Masjid Salahuddin berhasil diterapkan!']);
    }

    public function resetLogo(): void
    {
        $this->logo_file = null;
        $this->logo_url = '/resources/Logo Masjid Salahuddin.svg';

        $config = $this->getConfigArray();
        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => $this->template,
                'config' => $config,
            ]
        );

        $this->dispatch('toast', ['message' => 'Logo di-reset ke logo resmi Masjid Salahuddin.']);
    }

    public function save(): void
    {
        $this->validate([
            'template' => 'required|in:1,2',
            'masjid_line1' => 'required|string|max:100',
            'masjid_line2' => 'required|string|max:100',
            'footer_label' => 'required|string|max:100',
            'footer_url' => 'required|string|max:100',
            'logo_size' => 'required|integer|min:20|max:68',
            'title_font_size' => 'required|integer|min:26|max:54',
            'desc_font_size' => 'required|integer|min:10|max:22',
            'title_y' => 'required|integer|min:-20|max:30',
            'desc_y' => 'required|integer|min:-20|max:30',
            'title_desc_gap' => 'required|integer|min:4|max:32',
            'schedule_gap' => 'required|integer|min:8|max:26',
            'content_y' => 'required|integer|min:-50|max:40',
            'logo_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'preview_title1' => 'nullable|string|max:100',
            'preview_title2' => 'nullable|string|max:100',
            'preview_subtitle' => 'nullable|string|max:500',
            'jarkom_kajian_template' => 'nullable|string',
            'jarkom_jumat_template' => 'nullable|string',
        ]);

        if ($this->logo_file) {
            $path = $this->logo_file->store('poster-logos', 'public');
            $this->logo_url = Storage::url($path);
            $this->logo_file = null;
        }

        $config = $this->getConfigArray();

        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => $this->template,
                'config' => $config,
            ]
        );

        $this->dispatch('toast', ['message' => 'Konfigurasi template poster berhasil disimpan ke database!']);
    }

    public function saveJarkom(array $data = []): void
    {
        if (!empty($data)) {
            if (isset($data['jarkom_kajian_template'])) {
                $this->jarkom_kajian_template = (string) $data['jarkom_kajian_template'];
            }
            if (isset($data['jarkom_jumat_template'])) {
                $this->jarkom_jumat_template = (string) $data['jarkom_jumat_template'];
            }
        }

        $this->validate([
            'jarkom_kajian_template' => 'required|string',
            'jarkom_jumat_template' => 'required|string',
        ], [
            'jarkom_kajian_template.required' => 'Format template Jarkoman Kajian tidak boleh kosong.',
            'jarkom_jumat_template.required' => 'Format template Jarkoman Khutbah Jumat tidak boleh kosong.',
        ]);

        $config = $this->getConfigArray();

        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => $this->template,
                'config' => $config,
            ]
        );

        $this->dispatch('close-jarkom-modal');
        $this->dispatch('toast', ['message' => 'Format Jarkoman WhatsApp berhasil disimpan!']);
    }

    public function resetJarkomToDefault(?string $type = null): void
    {
        if ($type === 'kajian' || $type === null) {
            $this->jarkom_kajian_template = PosterSetting::defaultJarkomKajianTemplate();
        }
        if ($type === 'jumat' || $type === null) {
            $this->jarkom_jumat_template = PosterSetting::defaultJarkomJumatTemplate();
        }

        $config = $this->getConfigArray();
        PosterSetting::updateOrCreate(
            ['name' => 'default'],
            [
                'template_type' => $this->template,
                'config' => $config,
            ]
        );

        $label = $type === 'kajian' ? 'Kajian' : ($type === 'jumat' ? 'Khutbah Jumat' : 'Kajian & Khutbah Jumat');
        $this->dispatch('toast', ['message' => "Format Jarkoman {$label} dikembalikan ke default DKM."]);
    }

    public function getConfigArray(): array
    {
        return [
            'template' => $this->template,
            'masjid_line1' => $this->masjid_line1,
            'masjid_line2' => $this->masjid_line2,
            'logo_url' => $this->logo_url,
            'logo_size' => $this->logo_size,
            'title_font_size' => $this->title_font_size,
            'desc_font_size' => $this->desc_font_size,
            'title_y' => $this->title_y,
            'desc_y' => $this->desc_y,
            'title_desc_gap' => $this->title_desc_gap,
            'schedule_gap' => $this->schedule_gap,
            'footer_label' => $this->footer_label,
            'footer_url' => $this->footer_url,
            'content_y' => $this->content_y,
            'show_pattern' => $this->show_pattern,
            'show_leaves' => $this->show_leaves,
            'preview_title1' => $this->preview_title1,
            'preview_title2' => $this->preview_title2,
            'preview_subtitle' => $this->preview_subtitle,
            'jarkom_kajian_template' => $this->jarkom_kajian_template,
            'jarkom_jumat_template' => $this->jarkom_jumat_template,
        ];
    }

    public function render()
    {
        return view('livewire.admin.poster-setting-manager')
            ->layout('layouts.app', ['title' => 'Pengaturan Template Poster Kajian - Masjid Salahuddin']);
    }
}
