<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackSuggestion extends Model
{
    use HasFactory;

    protected $table = 'feedback_suggestions';

    protected $fillable = [
        'name',
        'is_anonymous',
        'contact',
        'category',
        'title',
        'message',
        'status',
        'is_public',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_public' => 'boolean',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Memastikan tabel feedback_suggestions tersedia (Self-Healing).
     */
    public static function ensureTableExists(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('feedback_suggestions')) {
                \Illuminate\Support\Facades\Schema::create('feedback_suggestions', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('name')->nullable();
                    $table->boolean('is_anonymous')->default(false);
                    $table->string('contact')->nullable();
                    $table->string('category')->default('Fasilitas & Kebersihan');
                    $table->string('title')->nullable();
                    $table->text('message');
                    $table->string('status', 30)->default('baru');
                    $table->boolean('is_public')->default(false);
                    $table->text('admin_reply')->nullable();
                    $table->timestamp('replied_at')->nullable();
                    $table->timestamps();

                    $table->index(['status', 'created_at']);
                    $table->index(['is_public', 'created_at']);
                });
            }
        } catch (\Throwable $e) {
            // Silently continue if database connection is unavailable during build/cli
        }
    }

    /**
     * Dapatkan nama tampilan pengirim yang aman dan santun (privasi terlindungi).
     */
    public function getDisplaySenderAttribute(): string
    {
        if ($this->is_anonymous || empty(trim($this->name ?? ''))) {
            return 'Hamba Allah';
        }

        $trimmed = trim($this->name);
        $parts = explode(' ', $trimmed);

        // Jika hanya 1 kata
        if (count($parts) === 1) {
            $first = $parts[0];
            return mb_strlen($first) > 3 ? mb_substr($first, 0, 3) . '***' : $first . '***';
        }

        // Tampilkan nama depan + inisial
        $firstName = $parts[0];
        $secondInitial = isset($parts[1]) ? mb_substr($parts[1], 0, 1) . '.' : '';
        return "{$firstName} {$secondInitial}***";
    }

    /**
     * Warna badge kategori yang harmonis.
     */
    public function getCategoryColorClassAttribute(): string
    {
        return match ($this->category) {
            'Fasilitas & Kebersihan' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Ibadah & Kajian' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Pelayanan DKM' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Kas & Sosial' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
