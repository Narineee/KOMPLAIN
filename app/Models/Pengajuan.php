<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    'konfirmasi_pelapor',
    'dikonfirmasi_pada',
    'jumlah_keberatan',
])]

#[Table('pengajuan')]
class Pengajuan extends Model
{
    use HasFactory;

    /**
     * Daftar tahapan sub-proses ketika laporan berstatus diproses.
     */
    public const SUB_STATUS_PROSES = [
        'identifikasi_klarifikasi' => 'Identifikasi / Klarifikasi',
        'rencana_aksi' => 'Rencana Aksi',
        'aksi' => 'Aksi / Tindakan',
    ];

    /**
     * Daftar hasil akhir ketika laporan berstatus selesai.
     */
    public const SUB_STATUS_SELESAI = [
        'selesai' => 'Selesai',
        'selesai_setelah_tinjau_ulang' => 'Selesai setelah tinjau ulang',
    ];

    protected function casts(): array
    {
        return [
            'pernyataan_disetujui' => 'boolean',
            'waktu_persetujuan' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'dikonfirmasi_pada' => 'datetime',
            'jumlah_keberatan' => 'integer',
        ];
    }

    public function getJudulAttribute(): string
    {
        return Str::limit($this->deskripsi ?? '', 40);
    }

    public function getSubStatusLabelAttribute(): ?string
    {
        return match ($this->sub_status) {
            'identifikasi_klarifikasi', 'identifikasi', 'klarifikasi' => 'Identifikasi / Klarifikasi',
            'rencana_aksi' => 'Rencana Aksi',
            'aksi' => 'Aksi / Tindakan',
            'selesai_setelah_tinjau_ulang' => 'Selesai setelah tinjau ulang',
            'selesai_ditangani', 'selesai_ditolak', 'selesai' => 'Selesai',
            default => $this->sub_status ? ucfirst(str_replace('_', ' ', $this->sub_status)) : null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->konfirmasi_pelapor === 'keberatan' && $this->status !== 'selesai') {
            return 'Ditinjau ulang';
        }

        if ($this->status === 'belum_verifikasi') {
            return 'Belum Diverifikasi';
        }

        if (in_array($this->status, ['verifikasi', 'menunggu', 'diajukan'], true)) {
            return 'Verifikasi';
        }

        if ($this->status === 'diproses' || $this->status === 'proses') {
            $labelSub = match ($this->sub_status) {
                'identifikasi_klarifikasi', 'identifikasi', 'klarifikasi' => 'Identifikasi / Klarifikasi',
                'rencana_aksi' => 'Rencana Aksi',
                'aksi' => 'Aksi / Tindakan',
                default => self::SUB_STATUS_PROSES[$this->sub_status] ?? null,
            };

            if ($labelSub) {
                return 'Diproses ('.$labelSub.')';
            }

            return 'Diproses';
        }

        if ($this->status === 'selesai' || $this->status === 'ditolak') {
            if ($this->sub_status === 'selesai_setelah_tinjau_ulang') {
                return 'Selesai setelah tinjau ulang';
            }

            return 'Selesai';
        }

        return ucfirst(str_replace('_', ' ', $this->status ?? 'Belum Diverifikasi'));
    }

    public function getPerluKonfirmasiAttribute(): bool
    {
        return $this->status === 'selesai'
            && is_null($this->konfirmasi_pelapor)
            && ($this->jumlah_keberatan ?? 0) === 0
            && $this->sub_status !== 'selesai_setelah_tinjau_ulang';
    }

    public function getBisaAjukanKeberatanAttribute(): bool
    {
        return $this->status === 'selesai'
            && ($this->jumlah_keberatan ?? 0) < 1
            && $this->sub_status !== 'selesai_setelah_tinjau_ulang'
            && $this->konfirmasi_pelapor !== 'dikonfirmasi';
    }

    public function scopePerluKonfirmasi(Builder $query): Builder
    {
        return $query->where('status', 'selesai')
            ->whereNull('konfirmasi_pelapor')
            ->where(function ($q) {
                $q->whereNull('jumlah_keberatan')->orWhere('jumlah_keberatan', 0);
            })
            ->where(function ($q) {
                $q->whereNull('sub_status')->orWhere('sub_status', '!=', 'selesai_setelah_tinjau_ulang');
            });
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
            $value = 'belum_verifikasi';
        }
        $this->attributes['status'] = $value ?? 'belum_verifikasi';
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
