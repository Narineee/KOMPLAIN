<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengajuan_id', 'admin_id', 'user_id', 'isi_tanggapan', 'pesan', 'is_internal'])]
#[Table('tanggapan')]
class Tanggapan extends Model
{
    use HasFactory;

    public function getUserIdAttribute(): ?int
    {
        return $this->attributes['admin_id'] ?? $this->attributes['user_id'] ?? null;
    }

    public function setUserIdAttribute(?int $value): void
    {
        $this->attributes['admin_id'] = $value;
    }

    public function getPesanAttribute(): ?string
    {
        return $this->attributes['isi_tanggapan'] ?? $this->attributes['pesan'] ?? null;
    }

    public function setPesanAttribute(?string $value): void
    {
        $this->attributes['isi_tanggapan'] = $value;
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
