<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Carbon\Carbon|null $event_date
 * @property float $budget
 * @property string $status
 * @property string|null $committee_members
 * @property string|null $report_summary
 * @property string|null $report_pdf_path
 * @property string|null $youtube_url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class Agenda extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * Memastikan kolom youtube_url tersedia di tabel agendas (Self-Healing / Auto-Migrate).
     */
    public static function ensureYoutubeColumnExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('agendas') && ! \Illuminate\Support\Facades\Schema::hasColumn('agendas', 'youtube_url')) {
                \Illuminate\Support\Facades\Schema::table('agendas', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('youtube_url', 500)->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika sudah ada atau race condition
        }
    }

    /**
     * Ekstraksi ID video YouTube dari berbagai format link.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->youtube_url, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * URL Embed YouTube yang aman digunakan dalam iframe preview.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube-nocookie.com/embed/{$id}" : null;
    }

    public function scopeDirencanakan(Builder $query): Builder
    {
        return $query->where('status', 'Direncanakan');
    }

    public function scopeBerjalan(Builder $query): Builder
    {
        return $query->where('status', 'Berjalan');
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'SELESAI');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->event_date ? Carbon::parse($this->event_date)->translatedFormat('l, d F Y') : '-';
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format($this->budget ?: 0, 0, ',', '.');
    }

    public function finances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Finance::class, 'agenda_id');
    }

    public function getTotalKasMasukAttribute(): float
    {
        return (float) $this->finances()->where('type', 'pemasukan')->sum('amount');
    }

    public function getTotalKasKeluarAttribute(): float
    {
        return (float) $this->finances()->where('type', 'pengeluaran')->sum('amount');
    }

    public function getSaldoKasAttribute(): float
    {
        return $this->total_kas_masuk - $this->total_kas_keluar;
    }

    public function getSilpaAttribute(): float
    {
        return (float) $this->budget - $this->total_kas_keluar;
    }

    public function getCommitteeListAttribute(): array
    {
        if (empty($this->committee_members)) {
            return [];
        }

        $items = preg_split('/\r\n|\r|\n/', trim($this->committee_members));
        return array_values(array_filter(array_map('trim', $items)));
    }

    public function getCommitteeCountAttribute(): int
    {
        return count($this->committee_list);
    }

    public function getKetuaPanitiaAttribute(): ?string
    {
        if (!empty($this->pic)) {
            return trim($this->pic);
        }

        if (!empty($this->committee_members)) {
            foreach ($this->committee_list as $line) {
                if (preg_match('/^(?:ketua|ketua\s+panitia|ketua\s+pelaksana|ketua\s+acara)\s*:\s*(.+)$/i', $line, $matches)) {
                    return trim($matches[1]);
                }
            }
        }

        return null;
    }
}
