<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengajuan_id', 'diubah_oleh', 'user_id', 'status_sebelumnya', 'status_baru', 'catatan'])]
#[Table('riwayat_status')]
class RiwayatStatus extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public function getUserIdAttribute(): ?int
    {
        return $this->attributes['diubah_oleh'] ?? $this->attributes['user_id'] ?? null;
    }

    public function setUserIdAttribute(?int $value): void
    {
        $this->attributes['diubah_oleh'] = $value;
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
