<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'nomor_tiket',
    'user_id',
    'kategori_id',
    'kategori_lainnya',
    'judul',
    'deskripsi',
    'isi_laporan',
    'saran_penyelesaian',
    'metode_identitas',
    'is_anonim',
    'status',
    'pernyataan_disetujui',
    'waktu_persetujuan',
    'tanggal_selesai',
])]

#[Table('pengajuan')]
class Pengajuan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pernyataan_disetujui' => 'boolean',
            'waktu_persetujuan' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function getIsiLaporanAttribute(): ?string
    {
        return $this->attributes['deskripsi'] ?? $this->attributes['isi_laporan'] ?? null;
    }

    public function setIsiLaporanAttribute(?string $value): void
    {
        $this->attributes['deskripsi'] = $value;
    }

    public function getIsAnonimAttribute(): bool
    {
        return ($this->attributes['metode_identitas'] ?? 'identitas') === 'anonim';
    }

    public function setIsAnonimAttribute(mixed $value): void
    {
        $this->attributes['metode_identitas'] = $value ? 'anonim' : 'identitas';
    }

    public function setStatusAttribute(?string $value): void
    {
        if ($value === 'diajukan') {
            $value = 'menunggu';
        }
        $this->attributes['status'] = $value ?? 'menunggu';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function tokenPengajuan(): HasOne
    {
        return $this->hasOne(TokenPengajuan::class);
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function lampirans(): HasMany
    {
        return $this->hasMany(Lampiran::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}
