<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_program_id',
        'name',
        'program_name',
        'monthly_amount',
        'period',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
    ];

    public function scopeCurrentPeriod(Builder $query): Builder
    {
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        return $query->where('period', $currentPeriod);
    }

    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $this->scopeCurrentPeriod($query);
    }

    public function socialProgram()
    {
        return $this->belongsTo(SocialProgram::class, 'social_program_id');
    }
}
