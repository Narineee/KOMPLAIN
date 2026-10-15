<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pengaduan #{{ $pengajuan->id }} - KOMPLAIN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    <!-- Navbar Pengadu -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-lg text-white">
                            K
                        </div>
                        <div>
                            <span class="font-bold text-base leading-tight block text-slate-900">KOMPLAIN</span>
                            <span class="text-[10px] text-blue-600 block font-semibold uppercase tracking-wider">Portal Pengadu</span>
                        </div>
                    </a>

                    <div class="hidden sm:flex items-center gap-2 ml-6">
                        <a href="{{ route('pegawai.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                            Daftar Aduan Saya
                        </a>
                        <a href="{{ route('pegawai.pengajuan.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                            + Buat Pengaduan Baru
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] text-slate-500">Pengadu / Pegawai (NIP: {{ auth()->user()->nip ?? '-' }})</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="py-1.5 px-3 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb / Tombol Kembali -->
        <div class="mb-6">
            <a href="{{ route('pegawai.dashboard') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Histori Aduan
            </a>
        </div>

        @php
            $isSelesaiDitolak = ($pengajuan->status === 'selesai' && $pengajuan->sub_status === 'selesai_ditolak') || $pengajuan->status === 'ditolak';

            if (in_array($pengajuan->status, ['diajukan', 'menunggu'])) {
                $currentStep = 1;
            } elseif ($pengajuan->status === 'diproses' || $pengajuan->status === 'proses') {
                $currentStep = match($pengajuan->sub_status) {
                    'identifikasi' => 2,
                    'klarifikasi' => 3,
                    'rencana_aksi' => 4,
                    'aksi' => 5,
                    default => 2,
                };
            } elseif ($pengajuan->status === 'selesai') {
                $currentStep = 6;
            } else {
                $currentStep = 1;
            }

            $stepperItems = [
                1 => ['label' => 'Verifikasi', 'desc' => 'Validasi berkas'],
                2 => ['label' => 'Identifikasi', 'desc' => 'Analisis masalah'],
                3 => ['label' => 'Klarifikasi', 'desc' => 'Konfirmasi pihak'],
                4 => ['label' => 'Rencana Aksi', 'desc' => 'Penyusunan solusi'],
                5 => ['label' => 'Aksi', 'desc' => 'Tindakan lapangan'],
                6 => [
                    'label' => $isSelesaiDitolak ? 'Selesai Ditolak' : 'Selesai Ditangani',
                    'desc' => $isSelesaiDitolak ? 'Tidak dapat ditindaklanjuti' : 'Solusi berhasil diterapkan'
                ],
            ];
        @endphp

        <!-- Visual Stepper: Alur Progress Penanganan Aduan -->
        <div class="mb-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-7">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Alur &amp; Tahapan Penanganan Aduan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pantau secara transparan tahapan proses tindak lanjut yang sedang berjalan.</p>
                </div>
                <div class="text-xs font-semibold">
                    @if ($isSelesaiDitolak)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Status Akhir: Selesai Ditolak
                        </span>
                    @elseif ($currentStep === 6)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Status Akhir: Selesai Ditangani
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Tahap {{ $currentStep }} dari 6: {{ $stepperItems[$currentStep]['label'] }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stepper Bar Horizontal -->
            <div class="relative">
                <!-- Connector Line Desktop -->
                <div class="hidden md:block absolute top-4 left-6 right-6 h-1 bg-slate-100 -z-0">
                    <div class="h-full bg-blue-600 transition-all duration-500"
                         style="width: {{ $currentStep === 1 ? '0%' : (($currentStep - 1) / 5) * 100 . '%' }};"></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 relative z-10">
                    @foreach ($stepperItems as $stepNum => $step)
                        @php
                            $isPassed = $stepNum < $currentStep;
                            $isCurrent = $stepNum === $currentStep;
                            $isFuture = $stepNum > $currentStep;
                        @endphp
                        <div class="flex flex-col items-center text-center">
                            <!-- Circle Node -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-xs mb-2
                                @if($isPassed) bg-blue-600 text-white ring-4 ring-blue-50
                                @elseif($isCurrent)
                                    @if($isSelesaiDitolak && $stepNum === 6) bg-rose-600 text-white ring-4 ring-rose-100 animate-pulse
                                    @elseif($currentStep === 6 && $stepNum === 6) bg-emerald-600 text-white ring-4 ring-emerald-100
                                    @else bg-blue-600 text-white ring-4 ring-blue-200 animate-pulse @endif
                                @else bg-slate-100 text-slate-400 border border-slate-200 @endif">
                                @if ($isPassed)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif ($isCurrent && $isSelesaiDitolak && $stepNum === 6)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @elseif ($isCurrent && $currentStep === 6)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $stepNum }}
                                @endif
                            </div>

                            <!-- Label & Desc -->
                            <span class="text-xs font-bold block
                                @if($isPassed) text-slate-800
                                @elseif($isCurrent)
                                    @if($isSelesaiDitolak && $stepNum === 6) text-rose-600
                                    @elseif($currentStep === 6 && $stepNum === 6) text-emerald-600
                                    @else text-blue-600 @endif
                                @else text-slate-400 @endif">
                                {{ $step['label'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 block mt-0.5 leading-tight">
                                {{ $step['desc'] }}
                            </span>

                            @if ($isCurrent)
                                <span class="mt-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                    @if($isSelesaiDitolak && $stepNum === 6) bg-rose-100 text-rose-800
                                    @elseif($currentStep === 6 && $stepNum === 6) bg-emerald-100 text-emerald-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    Aktif
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Keterangan Tahap Aktif -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-start gap-2.5 text-xs text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    @if ($currentStep === 1)
                        <span class="font-bold text-slate-800">Tahap Verifikasi:</span>
                        Laporan pengaduan Anda telah diterima dalam sistem dan sedang diperiksa oleh administrator sebelum masuk ke penanganan substantif.
                    @elseif ($currentStep === 2)
                        <span class="font-bold text-slate-800">Tahap Proses - Identifikasi Masalah:</span>
                        Tim administrator sedang melakukan identifikasi fakta lapangan dan menganalisis pokok permasalahan aduan.
                    @elseif ($currentStep === 3)
                        <span class="font-bold text-slate-800">Tahap Proses - Klarifikasi Pihak Terkait:</span>
                        Tim sedang mengumpulkan keterangan dan klarifikasi dari pihak/unit terkait perihal masalah yang dilaporkan.
                    @elseif ($currentStep === 4)
                        <span class="font-bold text-slate-800">Tahap Proses - Rencana Aksi Solusi:</span>
                        Tim sedang merumuskan langkah penanganan konkrit dan rencana aksi untuk menuntaskan kendala tersebut.
                    @elseif ($currentStep === 5)
                        <span class="font-bold text-slate-800">Tahap Proses - Pelaksanaan Aksi:</span>
                        Tindakan dan eksekusi perbaikan sedang dilaksanakan secara langsung di lapangan/lingkungan kerja.
                    @elseif ($currentStep === 6 && $isSelesaiDitolak)
                        <span class="font-bold text-rose-800">Hasil Selesai Ditolak:</span>
                        Laporan telah melalui proses evaluasi dan diputuskan tidak dapat ditindaklanjuti. Silakan periksa catatan dan riwayat di bawah untuk rincian alasan penolakan.
                    @else
                        <span class="font-bold text-emerald-800">Hasil Selesai Ditangani:</span>
                        Laporan telah selesai ditangani dengan sukses. Segala tindakan perbaikan telah selesai dilaksanakan. Terima kasih atas partisipasi Anda.
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Rincian Aduan -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">ID Pengaduan</span>
                            <span class="font-mono text-lg font-bold text-blue-700">#{{ $pengajuan->id }}</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if(in_array($pengajuan->status, ['diajukan', 'menunggu'])) bg-amber-100 text-amber-800
                            @elseif($pengajuan->status === 'diproses') bg-blue-100 text-blue-800
                            @elseif($pengajuan->status === 'selesai' && $pengajuan->sub_status === 'selesai_ditolak') bg-rose-100 text-rose-800
                            @elseif($pengajuan->status === 'selesai') bg-emerald-100 text-emerald-800
                            @else bg-slate-100 text-slate-800 @endif">
                            Status: {{ $pengajuan->status_label }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-4">Pengaduan #{{ $pengajuan->id }}</h1>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Uraian Pengaduan</h3>
                            <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $pengajuan->deskripsi ?? $pengajuan->isi_laporan }}
                            </div>
                        </div>

                        @if ($pengajuan->saran_penyelesaian)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Saran / Rekomendasi Solusi</h3>
                                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                                    {{ $pengajuan->saran_penyelesaian }}
                                </div>
                            </div>
                        @endif

                        @if ($pengajuan->lampirans && $pengajuan->lampirans->count() > 0)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Lampiran Berkas</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($pengajuan->lampirans as $lampiran)
                                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                                            <span class="truncate font-medium text-slate-700">{{ $lampiran->nama_file }}</span>
                                            <a href="{{ asset('storage/' . ($lampiran->path_file ?? $lampiran->path)) }}" target="_blank" class="ml-2 font-semibold text-blue-600 hover:text-blue-800 shrink-0">
                                                Unduh ↗
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bagian Tanggapan Admin -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Tanggapan & Tindak Lanjut Admin
                    </h2>

                    @if ($pengajuan->tanggapans && $pengajuan->tanggapans->count() > 0)
                        <div class="space-y-4">
                            @foreach ($pengajuan->tanggapans as $tanggapan)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 text-xs sm:text-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-slate-800">{{ $tanggapan->user->name ?? 'Tim Administrator' }}</span>
                                        <span class="text-slate-400 text-[11px]">{{ $tanggapan->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="text-slate-700 whitespace-pre-line">{{ $tanggapan->isi_tanggapan ?? $tanggapan->pesan }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-slate-400 text-xs sm:text-sm">
                            Belum ada tanggapan resmi dari tim admin untuk laporan ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Metadata & Riwayat Status -->
            <div class="space-y-6">
                <!-- Metadata Box -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Informasi Laporan</h2>
                    <dl class="space-y-3 text-xs sm:text-sm">
                        <div>
                            <dt class="text-slate-400 font-medium">Klasifikasi</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->nama_kategori_lengkap }}</dd>
                            @if ($pengajuan->kategori?->keterangan)
                                <p class="text-[11px] text-slate-500 mt-1 italic leading-relaxed">
                                    {{ $pengajuan->kategori->keterangan }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Waktu Pengajuan</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->created_at->format('d F Y, H:i') }} WIB</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Mode Identitas</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">
                                {{ ($pengajuan->metode_identitas === 'anonim' || $pengajuan->is_anonim) ? 'Anonim' : 'Dengan Identitas Pegawai' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Riwayat Status Timeline -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Riwayat Penanganan</h2>
                    @if ($pengajuan->riwayatStatuses && $pengajuan->riwayatStatuses->count() > 0)
                        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @foreach ($pengajuan->riwayatStatuses as $riwayat)
                                <div class="relative">
                                    <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full
                                        @if($riwayat->status_baru === 'selesai' && $riwayat->sub_status_baru === 'selesai_ditolak') bg-rose-500
                                        @elseif($riwayat->status_baru === 'selesai') bg-emerald-500
                                        @elseif($riwayat->status_baru === 'diproses') bg-blue-500
                                        @else bg-amber-500 @endif ring-4 ring-white"></div>
                                    <div class="text-xs">
                                        <span class="font-bold text-slate-900 block">{{ $riwayat->status_lengkap_baru }}</span>
                                        @if ($riwayat->status_sebelumnya)
                                            <span class="text-[10px] text-slate-400 block">Sebelumnya: {{ $riwayat->status_lengkap_sebelumnya }}</span>
                                        @endif
                                        @if ($riwayat->catatan)
                                            <p class="text-slate-600 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $riwayat->catatan }}</p>
                                        @endif
                                        <span class="text-[10px] text-slate-400 block mt-1">{{ $riwayat->created_at->format('d M Y, H:i') }} WIB</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada pembaruan status.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Portal Pengadu KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
