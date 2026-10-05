<?php

namespace App\Models;

use App\Notifications\QueuedResetPassword;
use App\Notifications\QueuedVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Pastikan nama pengguna selalu berformat Capitalized / Title Case (misal: "Deril Amrizal Kholid").
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Str::title($value) : $value,
            set: fn (?string $value) => $value ? Str::title(trim($value)) : $value,
        );
    }

    /**
     * Level Pengurus Tertinggi (Master / Admin)
     */
    public function isMaster(): bool
    {
        return in_array($this->role, ['Master', 'admin']);
    }

    /**
     * Kompatibilitas isAdmin
     */
    public function isAdmin(): bool
    {
        return $this->isMaster();
    }

    /**
     * Level Pengurus DKM (Master, Ketua, Sekretaris, Bendahara)
     */
    public function isOfficer(): bool
    {
        return in_array($this->role, ['Master', 'Ketua', 'Sekretaris', 'Bendahara', 'admin', 'operator']);
    }

    public function isOperator(): bool
    {
        return in_array($this->role, ['Sekretaris', 'operator']);
    }

    /**
     * Level Jamaah / Viewer
     */
    public function isJamaah(): bool
    {
        return in_array($this->role, ['Jamaah', 'viewer']);
    }

    public function isViewer(): bool
    {
        return $this->isJamaah();
    }

    /**
     * Hak akses pengelolaan penuh (CRUD)
     */
    public function canManage(): bool
    {
        return $this->isOfficer() && ($this->status ?? 'AKTIF') === 'AKTIF';
    }

    /**
     * Mode Read-Only (Hanya melihat)
     */
    public function isReadOnly(): bool
    {
        return ! $this->canManage();
    }

    /**
     * Send email verification notification using queued notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail);
    }

    /**
     * Send password reset notification using queued notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
