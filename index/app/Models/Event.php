<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'speaker_name',
        'speaker_role',
        'event_date',
        'time_display',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'registered_count',
        'status',
        'banner_path',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'capacity' => 'integer',
            'registered_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrants(): HasMany
    {
        return $this->hasMany(Registrant::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function isFull(): bool
    {
        return $this->capacity > 0 && $this->registered_count >= $this->capacity;
    }

    public function getRemainingCapacityAttribute(): int
    {
        return max(0, $this->capacity - $this->registered_count);
    }

    public function getCapacityPercentageAttribute(): int
    {
        if ($this->capacity <= 0) return 0;
        return (int) min(100, round(($this->registered_count / $this->capacity) * 100));
    }
}
