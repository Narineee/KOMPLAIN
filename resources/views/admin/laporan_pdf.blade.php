<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lembar Pengaduan #{{ $pengajuan->id }} - KOMPLAIN</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .logo-box {
            background-color: #312e81;
            color: #ffffff;
            width: 44px;
            height: 44px;
            line-height: 44px;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            border-radius: 6px;
        }

        .header-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1e1b4b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-size: 10pt;
            font-weight: bold;
            color: #334155;
            margin-top: 2px;
        }

        .header-desc {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            background-color: #f1f5f9;
            padding: 4px 8px;
            border-left: 3px solid #3b82f6;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .info-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .content-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 8.5pt;
            line-height: 1.45;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; }
        .badge-gray { background-color: #f1f5f9; color: #475569; }

        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .timeline-table th {
            background-color: #334155;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            padding: 5px 6px;
            text-align: left;
            border: 1px solid #1e293b;
        }

        .timeline-table td {
            font-size: 8pt;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .timeline-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .sign-table {
            width: 100%;
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .sign-table td {
            vertical-align: top;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 55px; vertical-align: middle;">
                <div class="logo-box">K</div>
            </td>
            <td style="vertical-align: middle;">
                <div class="header-title">Sistem Informasi Pengaduan Internal (KOMPLAIN)</div>
                <div class="header-subtitle">LEMBAR PENGADUAN & DISPOSISI TINDAK LANJUT</div>
                <div class="header-desc">Dokumen Rekam Jejak Pengaduan Resmi #{{ $pengajuan->id }}</div>
            </td>
            <td style="width: 200px; text-align: right; vertical-align: middle; font-size: 8pt; color: #475569;">
                <div><strong>ID Aduan:</strong> #{{ $pengajuan->id }}</div>
                <div><strong>Tgl Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div><strong>Petugas:</strong> {{ auth()->user()->nama ?? auth()->user()->name }}</div>
            </td>
        </tr>
    </table>

    <!-- 1. Data Informasi Pengaduan & Pelapor -->
    <div class="section-title">1. Informasi Pengaduan & Identitas Pelapor</div>
    <table class="info-table">
        <tr>
            <td style="width: 22%;"><strong>Nomor Tiket:</strong></td>
            <td style="width: 28%; font-weight: bold; color: #3730a3;">#{{ $pengajuan->id }}</td>
            <td style="width: 22%;"><strong>Tanggal Masuk:</strong></td>
            <td style="width: 28%;">{{ $pengajuan->created_at ? $pengajuan->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Klasifikasi Kategori:</strong></td>
            <td colspan="3"><strong>{{ $pengajuan->nama_kategori_lengkap }}</strong></td>
        </tr>
        <tr>
            <td><strong>Status Pelapor:</strong></td>
            <td>
                @if ($pengajuan->is_anonim)
                    <span class="badge badge-gray">Anonim (Rahasia)</span>
                @else
                    <span class="badge badge-info">Identitas Terbuka</span>
                @endif
            </td>
            <td><strong>Status Penanganan:</strong></td>
            <td>
                @if ($pengajuan->status === 'belum_verifikasi')
                    <span class="badge badge-danger">Belum Diverifikasi</span>
                @elseif (in_array($pengajuan->status, ['menunggu', 'diajukan', 'verifikasi']))
                    <span class="badge badge-warning">Verifikasi</span>
                @elseif ($pengajuan->status === 'diproses')
                    <span class="badge badge-info">Diproses ({{ $pengajuan->sub_status_label ?? 'Identifikasi / Klarifikasi' }})</span>
                @elseif ($pengajuan->status === 'selesai' || $pengajuan->status === 'ditolak')
                    <span class="badge badge-success">{{ $pengajuan->sub_status === 'selesai_setelah_tinjau_ulang' ? 'Selesai setelah tinjau ulang' : 'Selesai' }}</span>
                @else
                    <span class="badge badge-gray">{{ ucfirst($pengajuan->status) }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Identitas Pelapor:</strong></td>
            <td colspan="3">
                @if ($pengajuan->is_anonim && !($isAnonimUnlocked ?? false))
                    <em>(Identitas pelapor dirahasiakan oleh sistem)</em>
                @else
                    <strong>{{ $pengajuan->user?->nama ?? $pengajuan->user?->name ?? 'Pegawai' }}</strong>
                    &nbsp;—&nbsp; NIP: {{ $pengajuan->user?->nip ?? '-' }}
                    &nbsp;—&nbsp; Unit Kerja: {{ $pengajuan->user?->unit?->nama_unit ?? '-' }}
                    &nbsp;—&nbsp; Email: {{ $pengajuan->user?->email ?? '-' }}
                    @if ($pengajuan->is_anonim && ($isAnonimUnlocked ?? false))
                        <span style="color: #b91c1c; font-size: 7.5pt; font-weight: bold;">[Kunci Terbuka Sesi Admin]</span>
                    @endif
                @endif
            </td>
        </tr>
    </table>

    <!-- 2. Uraian Masalah & Saran Solusi -->
    <div class="section-title">2. Uraian Masalah Pengaduan</div>
    <div class="content-box">
        {!! nl2br(e($pengajuan->deskripsi ?? $pengajuan->isi_laporan)) !!}
    </div>

    @if ($pengajuan->saran_penyelesaian)
        <div style="font-weight: bold; font-size: 8pt; color: #1e40af; margin-bottom: 3px;">Saran / Rekomendasi Solusi Pelapor:</div>
        <div class="content-box" style="background-color: #eff6ff; border-color: #bfdbfe;">
            {!! nl2br(e($pengajuan->saran_penyelesaian)) !!}
        </div>
    @endif

    <!-- Lampiran -->
    <div style="font-size: 8pt; margin-bottom: 10px;">
        <strong>Berkas Lampiran Pendukung:</strong>
        @if ($pengajuan->lampirans && $pengajuan->lampirans->count() > 0)
            <ul style="margin: 3px 0 0 15px; padding: 0;">
                @foreach ($pengajuan->lampirans as $lampiran)
                    <li>{{ $lampiran->nama_file }} ({{ round(($lampiran->ukuran_file ?? $lampiran->ukuran_bytes ?? 0) / 1024) }} KB)</li>
                @endforeach
            </ul>
        @else
            <em>Tidak ada berkas lampiran yang diunggah.</em>
        @endif
    </div>

    <!-- 3. Riwayat Penanganan & Tindak Lanjut -->
    <div class="section-title">3. Riwayat Status & Tindak Lanjut</div>
    <table class="timeline-table">
        <thead>
            <tr>
                <th style="width: 22%;">Waktu</th>
                <th style="width: 22%;">Diubah Oleh</th>
                <th style="width: 24%;">Status & Tahapan</th>
                <th style="width: 32%;">Catatan Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuan->riwayatStatuses as $riwayat)
                <tr>
                    <td>{{ $riwayat->created_at ? $riwayat->created_at->translatedFormat('d/m/Y H:i') : '-' }} WIB</td>
                    <td>{{ $riwayat->user?->nama ?? $riwayat->user?->name ?? 'Sistem' }}</td>
                    <td>
                        <strong>{{ ucfirst($riwayat->status_baru) }}</strong>
                        @if ($riwayat->sub_status_baru)
                            <div style="font-size: 7pt; color: #64748b;">
                                ({{ \App\Models\Pengajuan::SUB_STATUS_PROSES[$riwayat->sub_status_baru] ?? str_replace('_', ' ', $riwayat->sub_status_baru) }})
                            </div>
                        @endif
                    </td>
                    <td>{{ $riwayat->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #94a3b8;">Belum ada riwayat perubahan status.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. Tanggapan Resmi Petugas Admin -->
    @if ($pengajuan->tanggapans && $pengajuan->tanggapans->count() > 0)
        <div class="section-title">4. Tanggapan & Komentar Petugas</div>
        <div style="margin-bottom: 10px;">
            @foreach ($pengajuan->tanggapans as $tanggapan)
                <div class="content-box" style="margin-bottom: 6px;">
                    <div style="font-size: 7.5pt; color: #64748b; margin-bottom: 3px;">
                        <strong>{{ $tanggapan->user?->nama ?? $tanggapan->user?->name ?? 'Admin' }}</strong>
                        &bull; {{ $tanggapan->created_at ? $tanggapan->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                    </div>
                    <div>{!! nl2br(e($tanggapan->isi ?? $tanggapan->pesan)) !!}</div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 5. Bagian Pengesahan & Tanda Tangan -->
    <table class="sign-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-weight: bold; color: #334155;">Verifikasi & Disposisi:</div>
                <div style="border: 1px dashed #cbd5e1; height: 75px; margin-top: 4px; padding: 6px; font-size: 7.5pt; color: #94a3b8;">
                    Catatan khusus pimpinan / tindak lanjut eksternal:
                </div>
            </td>
            <td style="width: 40%; text-align: center;">
                <div>Ditetapkan pada: {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 2px;">Administrator Pengelola</div>
                <div style="height: 45px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    {{ auth()->user()->nama ?? auth()->user()->name }}
                </div>
                <div style="font-size: 7.5pt; color: #475569;">
                    NIP: {{ auth()->user()->nip ?? '-' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
