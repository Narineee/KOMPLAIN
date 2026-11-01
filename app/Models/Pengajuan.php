<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
    'assigned_unit_id',
    'petugas_id',
    'catatan_penugasan',
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

    /**
     * Batas waktu konfirmasi pelapor dalam satuan hari sebelum dikonfirmasi otomatis oleh sistem.
     */
    public const BATAS_HARI_KONFIRMASI = 3;

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

    public function getBatasWaktuKonfirmasiAttribute(): ?Carbon
    {
        return $this->tanggal_selesai?->copy()->addDays(self::BATAS_HARI_KONFIRMASI);
    }

    public function getApakahKadaluarsaKonfirmasiAttribute(): bool
    {
        if (! $this->tanggal_selesai) {
            return false;
        }

        return $this->tanggal_selesai->copy()->addDays(self::BATAS_HARI_KONFIRMASI)->isPast();
    }

    public function getPerluKonfirmasiAttribute(): bool
    {
        return $this->status === 'selesai'
            && is_null($this->konfirmasi_pelapor)
            && ($this->jumlah_keberatan ?? 0) === 0
            && $this->sub_status !== 'selesai_setelah_tinjau_ulang'
            && ! $this->apakah_kadaluarsa_konfirmasi;
    }

    public function getBisaAjukanKeberatanAttribute(): bool
    {
        return $this->status === 'selesai'
            && ($this->jumlah_keberatan ?? 0) < 1
            && $this->sub_status !== 'selesai_setelah_tinjau_ulang'
            && ! in_array($this->konfirmasi_pelapor, ['dikonfirmasi', 'otomatis'], true)
            && ! $this->apakah_kadaluarsa_konfirmasi;
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
            })
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>', now()->subDays(self::BATAS_HARI_KONFIRMASI));
            });
    }

    public function scopeKadaluarsaKonfirmasi(Builder $query): Builder
    {
        return $query->where('status', 'selesai')
            ->whereNull('konfirmasi_pelapor')
            ->where(function ($q) {
                $q->whereNull('jumlah_keberatan')->orWhere('jumlah_keberatan', 0);
            })
            ->where(function ($q) {
                $q->whereNull('sub_status')->orWhere('sub_status', '!=', 'selesai_setelah_tinjau_ulang');
            })
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_selesai', '<=', now()->subDays(self::BATAS_HARI_KONFIRMASI));
    }

    /**
     * Memproses konfirmasi otomatis oleh sistem untuk seluruh aduan yang melewati batas waktu 3 hari.
     */
    public static function prosesAutoKonfirmasiKadaluarsa(): int
    {
        $kadaluarsa = static::kadaluarsaKonfirmasi()->get();
        $count = 0;

        foreach ($kadaluarsa as $p) {
            DB::transaction(function () use ($p) {
                $p->update([
                    'konfirmasi_pelapor' => 'otomatis',
                    'dikonfirmasi_pada' => now(),
                ]);

                RiwayatStatus::create([
                    'pengajuan_id' => $p->id,
                    'diubah_oleh' => null,
                    'status_sebelumnya' => 'selesai',
                    'status_baru' => 'selesai',
                    'sub_status_sebelumnya' => $p->sub_status,
                    'sub_status_baru' => $p->sub_status,
                    'catatan' => 'Laporan dikonfirmasi selesai secara otomatis oleh sistem karena telah melewati batas waktu 3 hari tanpa respons pelapor.',
                ]);

                if (! $p->is_anonim && $p->user_id) {
                    Notifikasi::create([
                        'user_id' => $p->user_id,
                        'pengajuan_id' => $p->id,
                        'judul' => 'Laporan Dikonfirmasi Otomatis',
                        'pesan' => "Laporan #{$p->id} telah dikonfirmasi selesai secara otomatis oleh sistem karena melewati batas waktu 3 hari.",
                        'tipe' => 'status_update',
                    ]);
                }
            });

            $count++;
        }

        return $count;
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

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function assignedUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'assigned_unit_id');
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
