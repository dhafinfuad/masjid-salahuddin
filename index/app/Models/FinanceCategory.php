<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
