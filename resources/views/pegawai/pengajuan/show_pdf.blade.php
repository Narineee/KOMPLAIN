<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Pengaduan #{{ $pengajuan->id }} - KOMPLAIN</title>
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

        .notice-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 5px;
            padding: 8px 12px;
            margin-top: 15px;
            font-size: 8pt;
            color: #166534;
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
                <div class="header-subtitle">TANDA TERIMA & BUKTI REGISTRASI PENGADUAN PEGAWAI</div>
                <div class="header-desc">Dokumen tanda bukti registrasi pengaduan resmi pegawai dalam sistem.</div>
            </td>
            <td style="width: 200px; text-align: right; vertical-align: middle; font-size: 8pt; color: #475569;">
                <div><strong>No. Tiket:</strong> #{{ $pengajuan->id }}</div>
                <div><strong>Dicetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <!-- 1. Rincian Registrasi -->
    <div class="section-title">1. Rincian Tiket & Data Pengadu</div>
    <table class="info-table">
        <tr>
            <td style="width: 22%;"><strong>Nomor Tiket:</strong></td>
            <td style="width: 28%; font-weight: bold; color: #3730a3;">#{{ $pengajuan->id }}</td>
            <td style="width: 22%;"><strong>Tanggal Pengaduan:</strong></td>
            <td style="width: 28%;">{{ $pengajuan->created_at ? $pengajuan->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB</td>
        </tr>
        <tr>
            <td><strong>Klasifikasi Aduan:</strong></td>
            <td colspan="3"><strong>{{ $pengajuan->nama_kategori_lengkap }}</strong></td>
        </tr>
        <tr>
            <td><strong>Status Pelaporan:</strong></td>
            <td>
                @if ($pengajuan->is_anonim)
                    <span class="badge badge-gray">Anonim (Data Dirahasiakan)</span>
                @else
                    <span class="badge badge-info">Identitas Terbuka</span>
                @endif
            </td>
            <td><strong>Status Penanganan:</strong></td>
            <td>
                @if (in_array($pengajuan->status, ['menunggu', 'diajukan', 'verifikasi']))
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
            <td><strong>Nama Pengadu:</strong></td>
            <td>{{ $pengajuan->user?->nama ?? $pengajuan->user?->name ?? auth()->user()->nama ?? auth()->user()->name }}</td>
            <td><strong>NIP & Unit Kerja:</strong></td>
            <td>{{ $pengajuan->user?->nip ?? auth()->user()->nip ?? '-' }} ({{ $pengajuan->user?->unit?->nama_unit ?? auth()->user()->unit?->nama_unit ?? '-' }})</td>
        </tr>
        @if ($pengajuan->status === 'selesai' && $pengajuan->konfirmasi_pelapor)
            <tr>
                <td><strong>Konfirmasi Anda:</strong></td>
                <td colspan="3">
                    @if ($pengajuan->konfirmasi_pelapor === 'dikonfirmasi')
                        <span class="badge badge-success">Sudah Dikonfirmasi Sesuai</span>
                        @if ($pengajuan->dikonfirmasi_pada)
                            pada {{ $pengajuan->dikonfirmasi_pada->translatedFormat('d F Y, H:i') }} WIB
                        @endif
                    @elseif ($pengajuan->konfirmasi_pelapor === 'keberatan')
                        <span class="badge badge-danger">Mengajukan Keberatan</span>
                        @if ($pengajuan->dikonfirmasi_pada)
                            pada {{ $pengajuan->dikonfirmasi_pada->translatedFormat('d F Y, H:i') }} WIB
                        @endif
                    @endif
                </td>
            </tr>
        @endif
    </table>

    <!-- 2. Isi Pengaduan -->
    <div class="section-title">2. Isi & Uraian Pengaduan</div>
    <div class="content-box">
        {!! nl2br(e($pengajuan->deskripsi ?? $pengajuan->isi_laporan)) !!}
    </div>

    @if ($pengajuan->saran_penyelesaian)
        <div style="font-weight: bold; font-size: 8pt; color: #1e40af; margin-bottom: 3px;">Saran Solusi yang Diajukan:</div>
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
            <em>Tidak ada berkas lampiran pendukung.</em>
        @endif
    </div>

    <!-- 3. Riwayat Tahapan -->
    <div class="section-title">3. Riwayat Status Penanganan</div>
    <table class="timeline-table">
        <thead>
            <tr>
                <th style="width: 25%;">Waktu</th>
                <th style="width: 35%;">Tahapan</th>
                <th style="width: 40%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuan->riwayatStatuses as $riwayat)
                <tr>
                    <td>{{ $riwayat->created_at ? $riwayat->created_at->translatedFormat('d/m/Y H:i') : '-' }} WIB</td>
                    <td>
                        <strong>{{ ucfirst($riwayat->status_baru) }}</strong>
                        @if ($riwayat->sub_status_baru)
                            <span style="font-size: 7.5pt; color: #64748b;">
                                ({{ \App\Models\Pengajuan::SUB_STATUS_PROSES[$riwayat->sub_status_baru] ?? str_replace('_', ' ', $riwayat->sub_status_baru) }})
                            </span>
                        @endif
                    </td>
                    <td>{{ $riwayat->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada riwayat status.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 4. Tanggapan Resmi Petugas -->
    @if ($pengajuan->tanggapans && $pengajuan->tanggapans->count() > 0)
        <div class="section-title">4. Tanggapan Resmi Petugas</div>
        <div style="margin-bottom: 10px;">
            @foreach ($pengajuan->tanggapans as $tanggapan)
                <div class="content-box">
                    <div style="font-size: 7.5pt; color: #64748b; margin-bottom: 3px;">
                        <strong>Petugas Pengelola</strong>
                        &bull; {{ $tanggapan->created_at ? $tanggapan->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                    </div>
                    <div>{!! nl2br(e($tanggapan->isi ?? $tanggapan->pesan)) !!}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="notice-box">
        <strong>Pemberitahuan Sistem:</strong>
        <p style="margin: 2px 0 0 0;">
            Surat/lembar ini merupakan bukti elektronik resmi yang diterbitkan oleh Sistem KOMPLAIN.
            Simpan nomor tiket #{{ $pengajuan->id }} untuk memantau pembaruan status dan tindak lanjut aduan Anda secara berkala.
        </p>
    </div>
</body>
</html>
