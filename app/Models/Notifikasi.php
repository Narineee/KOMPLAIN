<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'pengajuan_id', 'judul', 'pesan', 'tipe', 'dibaca_pada'])]
#[Table('notifikasi')]
class Notifikasi extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
        ];
    }

    public function getIsDibacaAttribute(): bool
    {
        return $this->dibaca_pada !== null;
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->whereNull('dibaca_pada');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
