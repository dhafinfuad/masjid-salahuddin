<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string|null $nip_pendek
 * @property string|null $nip_panjang
 * @property string $name
 * @property string|null $jabatan
 * @property string $unit_kerja
 * @property string|null $gender
 * @property string $status
 * @property int|null $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 */
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_pendek',
        'nip_panjang',
        'name',
        'jabatan',
        'unit_kerja',
        'gender',
        'status',
        'user_id',
    ];

    /**
     * Pastikan nama pegawai selalu Title Case dan mempertahankan format gelar akademik.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value,
            set: function (?string $value) {
                if (! $value) {
                    return $value;
                }
                $trimmed = trim($value);
                if (strtolower($trimmed) === $trimmed || strtoupper($trimmed) === $trimmed) {
                    $titled = Str::title($trimmed);
                    return preg_replace_callback('/\b(s|m)\.(kom|pd|e|t|si|ag|sn|sos)\b/i', fn ($m) => strtoupper($m[1]) . '.' . ucfirst(strtolower($m[2])), $titled);
                }

                return preg_replace_callback('/\b([a-z])/i', fn ($m) => strtoupper($m[1]), $trimmed);
            },
        );
    }

    /**
     * Relasi opsional ke Akun Pengguna / User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cek apakah pegawai ini sudah memiliki akun pengguna
     */
    public function isLinkedToUser(): bool
    {
        return ! is_null($this->user_id);
    }

    /**
     * Scope untuk pegawai yang aktif di kantor
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'AKTIF');
    }

    /**
     * Scope pencarian multi-kolom (Nama, NIP 9 digit, NIP 18 digit, atau Jabatan)
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);
        $cleanDigits = preg_replace('/\D/', '', $term);

        return $query->where(function (Builder $q) use ($term, $cleanDigits) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('jabatan', 'like', "%{$term}%");

            if (! empty($cleanDigits)) {
                $q->orWhere('nip_pendek', 'like', "%{$cleanDigits}%")
                  ->orWhere('nip_panjang', 'like', "%{$cleanDigits}%");
            }
        });
    }
}
