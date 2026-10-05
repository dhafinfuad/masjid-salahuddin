<?php

namespace App\Livewire\Portal;

use App\Models\FeedbackSuggestion;
use App\Models\MasjidSetting;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kotak Saran & Aspirasi Jamaah — Masjid Salahuddin')]
class SaranKritikPage extends Component
{
    // Form Input Properties
    public string $name = '';
    public bool $is_anonymous = false;
    public string $contact = '';
    public string $category = 'Fasilitas & Kebersihan';
    public string $title = '';
    public string $message = '';
    public string $honey_pot = ''; // Anti-spam bot trap

    // State
    public bool $submitted = false;
    public string $publicCategoryFilter = 'all';

    public array $categories = [
        'Fasilitas & Kebersihan',
        'Ibadah & Kajian',
        'Pelayanan DKM',
        'Kas & Sosial',
        'Lainnya',
    ];

    public function updatedIsAnonymous($val): void
    {
        if ($val) {
            $this->name = '';
        }
    }

    public function setPublicCategoryFilter(string $category): void
    {
        $this->publicCategoryFilter = $category;
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'is_anonymous', 'contact', 'category', 'title', 'message', 'submitted', 'honey_pot']);
        $this->category = 'Fasilitas & Kebersihan';
    }

    public function submit(): void
    {
        // Anti-spam honeypot
        if (!empty($this->honey_pot)) {
            $this->submitted = true;
            return;
        }

        // Rate limiter per IP: max 5 saran per 10 menit
        $throttleKey = 'saran-submission:' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('message', "Terlalu banyak permintaan. Mohon tunggu {$seconds} detik sebelum mengirim saran kembali.");
            return;
        }

        $this->validate([
            'category' => 'required|string|in:' . implode(',', $this->categories),
            'name' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:100',
            'title' => 'nullable|string|max:150',
            'message' => 'required|string|min:10|max:2000',
        ], [
            'category.required' => 'Pilih salah satu kategori saran.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'message.required' => 'Isi saran, kritik, atau usulan wajib diisi.',
            'message.min' => 'Isi masukan minimal 10 karakter.',
            'message.max' => 'Isi masukan maksimal 2000 karakter.',
        ]);

        FeedbackSuggestion::ensureTableExists();

        FeedbackSuggestion::create([
            'name' => $this->is_anonymous ? null : (trim($this->name) ?: null),
            'is_anonymous' => $this->is_anonymous,
            'contact' => trim($this->contact) ?: null,
            'category' => $this->category,
            'title' => trim($this->title) ?: null,
            'message' => trim($this->message),
            'status' => 'baru',
            'is_public' => false, // Default privat, takmir yang kurasi untuk ditampilkan
        ]);

        RateLimiter::hit($throttleKey, 600);

        $this->submitted = true;
        $this->dispatch('toast', detail: ['message' => 'Jazakallahu Khairan! Saran Anda berhasil terkirim ke Pengurus Takmir Masjid Salahuddin.']);
    }

    public function render()
    {
        $settings = MasjidSetting::getActive();
        FeedbackSuggestion::ensureTableExists();

        $publicFeedbacks = collect();

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('feedback_suggestions')) {
                $query = FeedbackSuggestion::query()
                    ->where('is_public', true)
                    ->orderByDesc('created_at');

                if ($this->publicCategoryFilter !== 'all') {
                    $query->where('category', $this->publicCategoryFilter);
                }

                $publicFeedbacks = $query->take(20)->get();
            }
        } catch (\Throwable $e) {
            $publicFeedbacks = collect();
        }

        return view('livewire.portal.saran-kritik-page', [
            'settings' => $settings,
            'publicFeedbacks' => $publicFeedbacks,
        ]);
    }
}
