<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property float $target_amount
 * @property string $period_type
 * @property string $status
 * @property string|null $icon
 * @property string|null $color
 * @property int $active_participants_count
 * @property float $monthly_commitment_total
 * @property float $total_collected
 * @property float $total_disbursed
 * @property float $current_balance
 * @property float $target_progress_percentage
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class SocialProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'target_amount',
        'period_type',
        'status',
        'icon',
        'color',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'AKTIF');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ProgramParticipant::class, 'social_program_id');
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class, 'program_name', 'name');
    }

    public function getTotalCollectedAttribute(): float
    {
        if (array_key_exists('total_collected', $this->attributes) && $this->attributes['total_collected'] !== null) {
            return (float) $this->attributes['total_collected'];
        }

        $now = now();
        $ytdPeriods = [];
        for ($m = 1; $m <= (int) $now->month; $m++) {
            $ytdPeriods[] = "Periode {$m}/{$now->year}";
        }

        return (float) ProgramParticipant::where(function ($q) {
            $q->where('social_program_id', $this->id)
              ->orWhere('program_name', $this->name)
              ->orWhere('program_name', str_replace('Program ', '', $this->name));
        })->whereIn('period', $ytdPeriods)->sum('monthly_amount');
    }

    public function getTotalDisbursedAttribute(): float
    {
        if (array_key_exists('total_disbursed', $this->attributes) && $this->attributes['total_disbursed'] !== null) {
            return (float) $this->attributes['total_disbursed'];
        }
        return (float) $this->finances()->where('type', 'pengeluaran')->sum('amount');
    }

    public function getCurrentBalanceAttribute(): float
    {
        return $this->total_collected - $this->total_disbursed;
    }

    public function getActiveParticipantsCountAttribute(): int
    {
        if (array_key_exists('active_participants_count', $this->attributes) && $this->attributes['active_participants_count'] !== null) {
            return (int) $this->attributes['active_participants_count'];
        }
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        return (int) ProgramParticipant::where(function ($q) {
            $q->where('social_program_id', $this->id)
              ->orWhere('program_name', $this->name)
              ->orWhere('program_name', str_replace('Program ', '', $this->name));
        })->where('period', $currentPeriod)->count();
    }

    public function getMonthlyCommitmentTotalAttribute(): float
    {
        if (array_key_exists('monthly_commitment_total', $this->attributes) && $this->attributes['monthly_commitment_total'] !== null) {
            return (float) $this->attributes['monthly_commitment_total'];
        }
        $currentPeriod = 'Periode ' . now()->month . '/' . now()->year;
        return (float) ProgramParticipant::where(function ($q) {
            $q->where('social_program_id', $this->id)
              ->orWhere('program_name', $this->name)
              ->orWhere('program_name', str_replace('Program ', '', $this->name));
        })->where('period', $currentPeriod)->sum('monthly_amount');
    }

    public function getTargetProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return round(($this->total_collected / (float) $this->target_amount) * 100, 1);
    }
}
