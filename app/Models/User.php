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
use Illuminate\Support\Carbon;

#[Fillable(['nip', 'nama', 'name', 'email', 'password', 'role', 'unit_id', 'status_akun', 'is_aktif'])]
#[Hidden(['password', 'remember_token'])]
/**
 * @property int $id
 * @property string $nip
 * @property string $nama
 * @property string|null $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int|null $unit_id
 * @property string $status_akun
 * @property bool $is_aktif
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Unit|null $unit
 */
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
        return $this->hasMany(Tanggapan::class, 'admin_id');
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isPegawai(): bool
    {
        return in_array($this->role, ['pegawai', 'pengadu', 'petugas'], true);
    }

    public function isPengadu(): bool
    {
        return in_array($this->role, ['pegawai', 'pengadu', 'petugas'], true);
    }

    public function pengajuanDitugaskan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'petugas_id');
    }
}
