<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $group
 * @property string $color
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class FinanceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'group',
        'color',
    ];

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class, 'category_id');
    }

    public function scopePenerimaan($query)
    {
        return $query->where('group', 'penerimaan');
    }

    public function scopePengeluaranRutin($query)
    {
        return $query->where('group', 'pengeluaran_rutin');
    }

    public function scopePengeluaranNonRutin($query)
    {
        return $query->where('group', 'pengeluaran_nonrutin');
    }

    public function getGroupLabelAttribute(): string
    {
        return match ($this->group) {
            'penerimaan' => 'Penerimaan',
            'pengeluaran_rutin' => 'Pengeluaran Rutin',
            'pengeluaran_nonrutin' => 'Pengeluaran Non-Rutin',
            default => 'Pos Anggaran',
        };
    }
}
