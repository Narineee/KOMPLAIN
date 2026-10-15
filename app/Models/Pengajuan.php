<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'kategori_id',
    'kategori_lainnya',
    'deskripsi',
    'isi_laporan',
    'saran_penyelesaian',
    'metode_identitas',
    'is_anonim',
    'status',
    'sub_status',
    'pernyataan_disetujui',
    'waktu_persetujuan',
    'tanggal_selesai',
])]

#[Table('pengajuan')]
class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Daftar tahapan sub-proses ketika laporan berstatus diproses.
     */
    public const SUB_STATUS_PROSES = [
        'identifikasi' => 'Identifikasi',
        'klarifikasi' => 'Klarifikasi',
        'rencana_aksi' => 'Rencana Aksi',
        'aksi' => 'Aksi / Tindakan',
    ];

    /**
     * Daftar hasil akhir ketika laporan berstatus selesai.
     */
    public const SUB_STATUS_SELESAI = [
        'selesai_ditangani' => 'Selesai Ditangani',
        'selesai_ditolak' => 'Selesai Ditolak',
    ];

    protected function casts(): array
    {
        return [
            'pernyataan_disetujui' => 'boolean',
            'waktu_persetujuan' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function getNomorTiketAttribute(): string
    {
        return '#'.str_pad((string) ($this->id ?? 0), 5, '0', STR_PAD_LEFT);
    }

    public function getJudulAttribute(): string
    {
        return Str::limit($this->deskripsi ?? '', 40);
    }

    public function getSubStatusLabelAttribute(): ?string
    {
        return match ($this->sub_status) {
            'identifikasi' => 'Identifikasi',
            'klarifikasi' => 'Klarifikasi',
            'rencana_aksi' => 'Rencana Aksi',
            'aksi' => 'Aksi / Tindakan',
            'selesai_ditangani' => 'Selesai Ditangani',
            'selesai_ditolak' => 'Selesai Ditolak',
            default => $this->sub_status ? ucfirst(str_replace('_', ' ', $this->sub_status)) : null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (in_array($this->status, ['menunggu', 'diajukan'], true)) {
            return 'Verifikasi';
        }

        if ($this->status === 'diproses' || $this->status === 'proses') {
            if ($this->sub_status && isset(self::SUB_STATUS_PROSES[$this->sub_status])) {
                return 'Diproses: '.self::SUB_STATUS_PROSES[$this->sub_status];
            }

            return 'Diproses';
        }

        if ($this->status === 'selesai') {
            if ($this->sub_status === 'selesai_ditolak') {
                return 'Selesai Ditolak';
            }
            if ($this->sub_status === 'selesai_ditangani') {
                return 'Selesai Ditangani';
            }

            return 'Selesai';
        }

        if ($this->status === 'ditolak') {
            return 'Selesai Ditolak';
        }

        return ucfirst($this->status ?? 'Verifikasi');
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
        if ($value === 'diajukan' || $value === 'verifikasi') {
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

    public function getNamaKategoriLengkapAttribute(): string
    {
        if (! $this->kategori) {
            return '-';
        }

        return $this->kategori->nama_lengkap;
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
