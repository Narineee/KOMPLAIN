<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kategori', 'parent_id', 'aktif', 'nama'])]
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

    public function getIsAktifAttribute(): bool
    {
        return (bool) ($this->attributes['aktif'] ?? true);
    }

    public function setIsAktifAttribute(mixed $value): void
    {
        $this->attributes['aktif'] = (bool) $value;
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }
}
