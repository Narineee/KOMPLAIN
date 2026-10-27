<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pengajuan_id',
    'diubah_oleh',
    'user_id',
    'status_sebelumnya',
    'status_baru',
    'sub_status_sebelumnya',
    'sub_status_baru',
    'catatan',
])]
#[Table('riwayat_status')]
class RiwayatStatus extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public function getStatusLengkapSebelumnyaAttribute(): string
    {
        return $this->formatStatusLabel($this->status_sebelumnya, $this->sub_status_sebelumnya);
    }

    public function getStatusLengkapBaruAttribute(): string
    {
        return $this->formatStatusLabel($this->status_baru, $this->sub_status_baru);
    }

    private function formatStatusLabel(?string $status, ?string $subStatus): string
    {
        if (in_array($status, ['menunggu', 'diajukan'], true)) {
            return 'Verifikasi';
        }

        if ($status === 'diproses' || $status === 'proses') {
            $labelSub = match ($subStatus) {
                'identifikasi_klarifikasi', 'identifikasi', 'klarifikasi' => 'Identifikasi / Klarifikasi',
                'rencana_aksi' => 'Rencana Aksi',
                'aksi' => 'Aksi / Tindakan',
                default => Pengajuan::SUB_STATUS_PROSES[$subStatus] ?? null,
            };

            if ($labelSub) {
                return 'Diproses ('.$labelSub.')';
            }

            return 'Diproses';
        }

        if ($status === 'selesai' || $status === 'ditolak') {
            if ($subStatus === 'selesai_setelah_tinjau_ulang') {
                return 'Selesai setelah tinjau ulang';
            }

            return 'Selesai';
        }

        return ucfirst($status ?? '-');
    }

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
