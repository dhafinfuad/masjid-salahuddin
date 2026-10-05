<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ustadz extends Model
{
    protected $table = 'ustadzs';

    protected $guarded = ['id'];

    public function kajians(): HasMany
    {
        return $this->hasMany(Kajian::class, 'speaker_name', 'name');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    /**
     * Synchronize a photo across all kajian records for a given speaker name
     */
    public static function syncPhotoToKajians(string $speakerName, ?string $photoPath): void
    {
        $speakerName = trim($speakerName);
        if (empty($speakerName)) {
            return;
        }

        DB::table('kajians')
            ->where('speaker_name', $speakerName)
            ->update(['speaker_photo' => $photoPath]);
    }
}
