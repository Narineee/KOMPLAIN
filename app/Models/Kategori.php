<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kategori', 'parent_id', 'keterangan', 'aktif', 'nama', 'deskripsi'])]
#[Table('kategori')]
class Kategori extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama_kategori'] ?? $this->attributes['nama'] ?? null;
    }

    public function setNamaAttribute(?string $value): void
    {
        $this->attributes['nama_kategori'] = $value;
    }

    public function getDeskripsiAttribute(): ?string
    {
        return $this->attributes['keterangan'] ?? $this->attributes['deskripsi'] ?? null;
    }

    public function setDeskripsiAttribute(?string $value): void
    {
        $this->attributes['keterangan'] = $value;
    }

    public function getIsAktifAttribute(): bool
    {
        return (bool) ($this->attributes['aktif'] ?? true);
    }

    public function setIsAktifAttribute(mixed $value): void
    {
        $this->attributes['aktif'] = (bool) $value;
    }

    public function getNamaLengkapAttribute(): string
    {
        $nama = $this->attributes['nama_kategori'] ?? $this->attributes['nama'] ?? '';

        if ($this->relationLoaded('parent') && $this->parent) {
            return $this->parent->nama_kategori.' — '.$nama;
        }

        if ($this->parent_id) {
            $parent = $this->parent;
            if ($parent) {
                return $parent->nama_kategori.' — '.$nama;
            }
        }

        return $nama;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('nama_kategori');
    }

    public function activeChildren(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('aktif', true)->orderBy('nama_kategori');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function isChild(): bool
    {
        return $this->parent_id !== null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
