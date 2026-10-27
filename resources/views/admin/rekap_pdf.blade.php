<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Pengaduan Pegawai - KOMPLAIN</title>
    <style>
        @page {
            margin: 12mm 12mm 14mm 12mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000000;
            line-height: 1.35;
        }

        /* Kop Surat Resmi Kedinasan */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .kop-logo {
            width: 50px;
            height: 50px;
            border: 2px solid #000000;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 48px;
            font-family: 'DejaVu Serif', 'Times New Roman', serif;
        }

        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1.2;
        }

        .kop-unit {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
            margin-top: 1px;
            line-height: 1.2;
        }

        .kop-sistem {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 2px;
        }

        .kop-alamat {
            font-size: 7pt;
            color: #334155;
            margin-top: 2px;
        }

        /* Judul Dokumen Formal */
        .judul-dokumen {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .judul-utama {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .nomor-dokumen {
            font-size: 8pt;
            font-weight: normal;
            margin-top: 3px;
        }

        /* Metadata Parameter */
        .meta-box {
            width: 100%;
            border: 1px solid #000000;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .meta-box td {
            padding: 4px 6px;
            font-size: 7.5pt;
            vertical-align: top;
        }

        .meta-title {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000000;
            padding: 4px 6px;
            font-size: 8pt;
        }

        /* Sub Judul Bagian */
        .section-header {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 5px;
            color: #000000;
        }

        /* Tabel Standar Formal */
        .tabel-formal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .tabel-formal th {
            background-color: #f1f5f9;
            color: #000000;
            border: 1px solid #000000;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 5px 4px;
            text-align: center;
            text-transform: uppercase;
        }

        .tabel-formal td {
            border: 1px solid #000000;
            padding: 4px 5px;
            font-size: 7.5pt;
            vertical-align: top;
        }

        .tabel-formal tr.total-row td {
            background-color: #f8fafc;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* Tanda Tangan Kedinasan */
        .ttd-table {
            width: 100%;
            margin-top: 16px;
            page-break-inside: avoid;
            border-collapse: collapse;
        }

        .ttd-table td {
            vertical-align: top;
            font-size: 8pt;
            padding: 0;
        }

        .ttd-space {
            height: 55px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @php
        $totalAduan = $ringkasan['total'];

        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $nomorBulan = (int) now()->format('n');
        $nomorSurat = '00' . $nomorBulan . '/LRA/KOMPLAIN/' . ($bulanRomawi[$nomorBulan] ?? 'X') . '/' . now()->format('Y');
    @endphp

    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td style="width: 60px; vertical-align: middle; text-align: center;">
                <div class="kop-logo">K</div>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div class="kop-instansi">SISTEM PENGELOLAAN PENGADUAN INTERNAL PEGAWAI</div>
                <div class="kop-unit">UNIT PENGAWASAN, PELAYANAN & PENANGANAN PENGADUAN</div>
                <div class="kop-sistem">SISTEM INFORMASI KOMPLAIN TERPADU</div>
                <div class="kop-alamat">
                    Gedung Administrasi Umum Lantai 2 &bull; Surat Elektronik: pengaduan@komplain.internal &bull; Laman Resmi: https://komplain.internal
                </div>
            </td>
            <td style="width: 60px;">&nbsp;</td>
        </tr>
    </table>

    <!-- Judul Dokumen Resmi -->
    <div class="judul-dokumen">
        <div class="judul-utama">LAPORAN REKAPITULASI PENGADUAN DAN ASPIRASI PEGAWAI</div>
        <div class="nomor-dokumen">Nomor: {{ $nomorSurat }}</div>
    </div>

    <!-- Parameter dan Ruang Lingkup Data -->
    <table class="meta-box">
        <tr>
            <td colspan="4" class="meta-title">I. PARAMETER & RUANG LINGKUP LAPORAN</td>
        </tr>
        <tr>
            <td style="width: 16%;"><strong>Periode Data</strong></td>
            <td style="width: 34%;">:
                @if (!empty($filter['dari_tanggal']) || !empty($filter['sampai_tanggal']))
                    {{ !empty($filter['dari_tanggal']) ? \Carbon\Carbon::parse($filter['dari_tanggal'])->translatedFormat('d F Y') : 'Awal Sistem' }}
                    s/d
                    {{ !empty($filter['sampai_tanggal']) ? \Carbon\Carbon::parse($filter['sampai_tanggal'])->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                @else
                    Semua Periode (Keseluruhan Rekam Data)
                @endif
            </td>
            <td style="width: 16%;"><strong>Klasifikasi Kategori</strong></td>
            <td style="width: 34%;">:
                @if (!empty($filter['kategori_id']))
                    @php
                        $katTerpilih = $kategoris->firstWhere('id', (int) $filter['kategori_id']);
                    @endphp
                    {{ $katTerpilih ? ($katTerpilih->parent ? $katTerpilih->parent->nama_kategori . ' → ' . $katTerpilih->nama_kategori : $katTerpilih->nama_kategori) : 'ID: ' . $filter['kategori_id'] }}
                @else
                    Semua Klasifikasi Pengaduan
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Status Penanganan</strong></td>
            <td>:
                @if (!empty($filter['status']))
                    @php
                        $labelStatusFilter = match ($filter['status']) {
                            'belum_verifikasi' => 'Belum Diverifikasi',
                            'menunggu', 'diajukan', 'verifikasi' => 'Verifikasi',
                            'diproses' => 'Sedang Diproses',
                            'identifikasi_klarifikasi', 'identifikasi', 'klarifikasi' => 'Diproses (Identifikasi / Klarifikasi)',
                            'rencana_aksi' => 'Diproses (Rencana Aksi)',
                            'aksi' => 'Diproses (Aksi)',
                            'selesai', 'selesai_ditangani', 'selesai_ditolak', 'ditolak' => 'Selesai',
                            default => ucfirst(str_replace('_', ' ', $filter['status'])),
                        };
                    @endphp
                    {{ $labelStatusFilter }}
                @else
                    Semua Status Penanganan
                @endif
            </td>
            <td><strong>Waktu Penarikan Data</strong></td>
            <td>: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td><strong>Petugas Administrator</strong></td>
            <td>: {{ auth()->user()->nama ?? auth()->user()->name }} (NIP. {{ auth()->user()->nip ?? '-' }})</td>
            <td><strong>Total Rekam Masuk</strong></td>
            <td>: <strong>{{ $totalAduan }} berkas laporan</strong></td>
        </tr>
    </table>

    <!-- Ringkasan Eksekutif -->
    <div class="section-header">II. RINGKASAN EKSEKUTIF PENANGANAN PENGADUAN</div>
    <table class="tabel-formal">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 40%;">Status / Tahapan Penanganan</th>
                <th style="width: 20%;">Jumlah Aduan</th>
                <th style="width: 35%;">Keterangan & Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1.</td>
                <td>Dalam Verifikasi Awal (Menunggu)</td>
                <td class="text-center font-bold">{{ $ringkasan['diajukan'] }} berkas</td>
                <td>Menunggu peninjauan dan verifikasi kelayakan oleh admin</td>
            </tr>
            <tr>
                <td class="text-center">2.</td>
                <td>Sedang Ditindaklanjuti (Diproses)</td>
                <td class="text-center font-bold">{{ $ringkasan['diproses'] }} berkas</td>
                <td>Tahap analisis identifikasi/klarifikasi atau aksi solusi</td>
            </tr>
            <tr>
                <td class="text-center">3.</td>
                <td>Selesai (Tuntas Ditangani)</td>
                <td class="text-center font-bold">{{ $ringkasan['selesai'] }} berkas</td>
                <td>Telah selesai ditangani dan diberikan tindak lanjut resmi</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-center">JUMLAH KESELURUHAN LAPORAN</td>
                <td class="text-center">{{ $totalAduan }} berkas</td>
                <td>Akumulasi pengaduan masuk sesuai kriteria periode</td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #fafafa; font-size: 7pt; padding: 4px 6px;">
                    <strong>Distribusi Perlindungan Identitas:</strong>
                    Aduan Identitas Terbuka: <strong>{{ $ringkasan['identitas'] }}</strong> berkas &bull;
                    Aduan Anonim (Terlindungi): <strong>{{ $ringkasan['anonim'] }}</strong> berkas
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Daftar Rincian Pengaduan -->
    <div class="section-header">III. DAFTAR RINCIAN PENGADUAN PEGAWAI</div>
    <table class="tabel-formal">
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 7%;">No. Tiket</th>
                <th style="width: 9%;">Tgl. Masuk</th>
                <th style="width: 17%;">Identitas Pelapor & Unit</th>
                <th style="width: 15%;">Klasifikasi Pengaduan</th>
                <th style="width: 29%;">Pokok Permasalahan & Saran Solusi</th>
                <th style="width: 12%;">Status Penanganan</th>
                <th style="width: 8%;">Tgl. Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td class="text-center font-bold">#{{ $item->id }}</td>
                    <td class="text-center">{{ $item->created_at ? $item->created_at->translatedFormat('d/m/Y') : '-' }}</td>
                    <td>
                        @if ($item->is_anonim)
                            <em>Rahasia (Anonim)</em>
                        @else
                            <strong>{{ $item->user?->nama ?? $item->user?->name ?? 'Pegawai' }}</strong><br>
                            <span style="font-size: 7pt; color: #334155;">
                                NIP: {{ $item->user?->nip ?? '-' }}<br>
                                Unit: {{ $item->user?->unit?->nama_unit ?? '-' }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->nama_kategori_lengkap }}</strong>
                    </td>
                    <td>
                        <div>{{ \Illuminate\Support\Str::limit($item->deskripsi ?? $item->isi_laporan, 140) }}</div>
                        @if ($item->saran_penyelesaian)
                            <div style="font-size: 6.8pt; color: #1e3a8a; margin-top: 2px;">
                                <em>Usulan Solusi: {{ \Illuminate\Support\Str::limit($item->saran_penyelesaian, 80) }}</em>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item->status === 'belum_verifikasi')
                            BELUM DIVERIFIKASI
                        @elseif (in_array($item->status, ['menunggu', 'diajukan', 'verifikasi']))
                            VERIFIKASI
                        @elseif ($item->status === 'diproses')
                            DIPROSES<br>
                            <span style="font-size: 6.5pt;">({{ strtoupper(\App\Models\Pengajuan::SUB_STATUS_PROSES[$item->sub_status] ?? $item->sub_status ?? 'Identifikasi') }})</span>
                        @elseif ($item->status === 'selesai' || $item->status === 'ditolak')
                            @if ($item->sub_status === 'selesai_setelah_tinjau_ulang')
                                SELESAI<br><span style="font-size: 6pt; color: #047857;">(SETELAH TINJAU ULANG)</span>
                            @else
                                SELESAI
                            @endif
                        @else
                            {{ strtoupper($item->status) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item->status === 'selesai')
                            {{ $item->tanggal_selesai ? $item->tanggal_selesai->translatedFormat('d/m/Y') : ($item->updated_at ? $item->updated_at->translatedFormat('d/m/Y') : '-') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 16px; font-style: italic;">
                        Tidak terdapat catatan pengaduan yang sesuai dengan kriteria penyaringan data ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan & Pengesahan Resmi -->
    <table class="ttd-table">
        <tr>
            <td style="width: 58%;">
                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">CATATAN RESMI DOKUMEN:</div>
                <div style="font-size: 7pt; color: #1e293b; line-height: 1.4;">
                    1. Dokumen ini merupakan rekapitulasi data resmi yang dihasilkan oleh Sistem Informasi KOMPLAIN.<br>
                    2. Laporan ini sah sebagai dokumen arsip pengawasan internal dan evaluasi penanganan aduan.<br>
                    3. Kerahasiaan pelapor berstatus anonim wajib dijaga sesuai dengan ketentuan perlindungan pelapor.
                </div>
            </td>
            <td style="width: 42%; text-align: center;">
                <div>Ditetapkan pada: {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 1px;">Administrator Pengelola Sistem KOMPLAIN</div>
                <div class="ttd-space"></div>
                <div class="ttd-nama">{{ auth()->user()->nama ?? auth()->user()->name }}</div>
                <div style="font-size: 7.5pt; color: #334155;">NIP. {{ auth()->user()->nip ?? '-' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
