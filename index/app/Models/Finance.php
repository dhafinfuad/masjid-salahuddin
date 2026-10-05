<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'type',
        'category_id',
        'agenda_id',
        'program_name',
        'amount',
        'description',
        'receipt_path',
        'receipt_paths',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'receipt_paths' => 'array',
    ];

    /**
     * Memastikan kolom receipt_paths tersedia pada tabel finances (Self-Healing).
     */
    public static function ensureReceiptPathsColumnExists(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('finances') && ! \Illuminate\Support\Facades\Schema::hasColumn('finances', 'receipt_paths')) {
                \Illuminate\Support\Facades\Schema::table('finances', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->json('receipt_paths')->nullable()->after('receipt_path');
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if connection not ready
        }
    }

    /**
     * Mengembalikan daftar URL bukti transaksi terstruktur untuk carousel.
     * Kompatibel penuh dengan transaksi lama yang hanya memiliki receipt_path tunggal.
     *
     * @return array<int, array{path: string, url: string, is_pdf: bool}>
     */
    public function getReceiptUrlsAttribute(): array
    {
        $paths = $this->receipt_paths;
        if (empty($paths) && !empty($this->receipt_path)) {
            $paths = [$this->receipt_path];
        }

        if (empty($paths) || !is_array($paths)) {
            return [];
        }

        return array_values(array_map(function ($path) {
            $cleanPath = ltrim((string) $path, '/');
            return [
                'path' => $cleanPath,
                'url' => asset('storage/' . $cleanPath),
                'is_pdf' => str_ends_with(strtolower($cleanPath), '.pdf'),
            ];
        }, $paths));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopePemasukan(Builder $query): Builder
    {
        return $query->where('type', 'pemasukan');
    }

    public function scopePengeluaran(Builder $query): Builder
    {
        return $query->where('type', 'pengeluaran');
    }

    public function scopeFilterPeriod(Builder $query, ?string $month = null, ?string $year = null): Builder
    {
        if ($month && $month !== 'all') {
            $query->whereMonth('transaction_date', (int)$month);
        }
        if ($year && $year !== 'all') {
            $query->whereYear('transaction_date', (int)$year);
        }
        return $query;
    }
}
