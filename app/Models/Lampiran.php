<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengajuan_id', 'nama_file', 'path_file', 'path', 'mime_type', 'ukuran_bytes', 'ukuran_file', 'ekstensi'])]
#[Table('lampiran')]
class Lampiran extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public function getPathAttribute(): ?string
    {
        return $this->attributes['path_file'] ?? $this->attributes['path'] ?? null;
    }

    public function setPathAttribute(?string $value): void
    {
        $this->attributes['path_file'] = $value;
    }

    public function getUkuranFileAttribute(): ?int
    {
        return $this->attributes['ukuran_bytes'] ?? $this->attributes['ukuran_file'] ?? null;
    }

    public function setUkuranFileAttribute(?int $value): void
    {
        $this->attributes['ukuran_bytes'] = $value;
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
