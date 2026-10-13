<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nip', 'nama', 'name', 'email', 'password', 'role', 'unit_id', 'status_akun', 'is_aktif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getIsAktifAttribute(): bool
    {
        return ($this->attributes['status_akun'] ?? 'aktif') === 'aktif';
    }

    public function setIsAktifAttribute(mixed $value): void
    {
        $this->attributes['status_akun'] = $value ? 'aktif' : 'nonaktif';
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPegawai(): bool
    {
        return in_array($this->role, ['pegawai', 'pengadu'], true);
    }

    public function isPengadu(): bool
    {
        return in_array($this->role, ['pegawai', 'pengadu'], true);
    }
}
