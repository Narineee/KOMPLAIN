<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Administrator - KOMPLAIN</title>

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
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
    <!-- Navbar Admin -->
    <nav class="bg-indigo-900 text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-indigo-700 flex items-center justify-center font-bold text-lg text-white">
                            K
                        </div>
                        <div>
                            <span class="font-bold text-base leading-tight block">KOMPLAIN</span>
                            <span class="text-[10px] text-indigo-300 block font-medium">Panel Administrator</span>
                        </div>
                    </a>

                    <div class="hidden md:flex items-center gap-1 ml-8">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-800 text-white">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Semua Laporan
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Klasifikasi
                        </a>
                        <a href="{{ route('admin.pegawai.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Pengguna
                        </a>
                        <a href="{{ route('admin.rekap') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Rekap
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-semibold text-white">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] text-indigo-300">Administrator ({{ auth()->user()->email }})</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="py-1.5 px-3 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Header Dashboard & Indikator Waktu Realtime -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <h1 class="text-2xl font-bold text-slate-900">Ringkasan Sistem Pengaduan</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-xs">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        {{ $namaBulanTahun }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200/90 text-slate-800 shadow-xs flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="live-clock" class="font-mono text-xs">--:--:-- WIB</span>
                </div>
                <a href="{{ route('admin.laporan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition shadow-sm flex items-center gap-1.5">
                    <span>Lihat Seluruh Laporan</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Highlight Ringkasan: Tahun Ini & Realtime Bulan Ini -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <!-- Card 1: Laporan Masuk Tahun Ini -->
            <div class="bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 text-white rounded-3xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/5 rounded-full pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-indigo-100 border border-white/10 backdrop-blur-xs">
                            📅 Tahun {{ $tahunAktif }}
                        </span>
                        <span class="text-[11px] text-indigo-300 font-medium">1 Jan – 31 Des {{ $tahunAktif }}</span>
                    </div>

                    <div class="mt-5">
                        <div class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Laporan Masuk Tahun Ini</div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-white">
                                {{ number_format($statistikTahun['total']) }}
                            </span>
                            <span class="text-xs font-medium text-indigo-200">total laporan</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-indigo-800/80 relative z-10">
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-white/5 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-indigo-300 font-medium">Verifikasi</span>
                            <span class="block font-bold text-amber-300 mt-0.5">{{ $statistikTahun['diajukan'] }}</span>
                        </div>
                        <div class="bg-white/5 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-indigo-300 font-medium">Diproses</span>
                            <span class="block font-bold text-blue-300 mt-0.5">{{ $statistikTahun['diproses'] }}</span>
                        </div>
                        <div class="bg-white/5 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-indigo-300 font-medium">Selesai</span>
                            <span class="block font-bold text-emerald-300 mt-0.5">{{ $statistikTahun['selesai'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Laporan Masuk Bulan Ini (Realtime) -->
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-700 text-white rounded-3xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between ring-2 ring-blue-400/40">
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-100 border border-emerald-400/30 backdrop-blur-xs">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-300"></span>
                            </span>
                            {{ $namaBulan }}
                        </span>
                        <span class="text-[10px] text-blue-100 font-medium bg-white/10 px-2 py-0.5 rounded-full" title="Hitungan akan otomatis kembali ke 0 ketika tanggal masuk ke awal bulan berikutnya">
                            Reset tiap bulan
                        </span>
                    </div>

                    <div class="mt-5">
                        <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider">Laporan Masuk Bulan Ini</div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-white">
                                {{ number_format($statistikBulan['total']) }}
                            </span>
                            <span class="text-xs font-medium text-blue-100">laporan aktif</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-white/20 relative z-10">
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-white/10 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-blue-100 font-medium">Verifikasi</span>
                            <span class="block font-bold text-amber-200 mt-0.5">{{ $statistikBulan['diajukan'] }}</span>
                        </div>
                        <div class="bg-white/10 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-blue-100 font-medium">Diproses</span>
                            <span class="block font-bold text-sky-200 mt-0.5">{{ $statistikBulan['diproses'] }}</span>
                        </div>
                        <div class="bg-white/10 rounded-xl py-1.5 px-1">
                            <span class="block text-[10px] text-blue-100 font-medium">Selesai</span>
                            <span class="block font-bold text-emerald-200 mt-0.5">{{ $statistikBulan['selesai'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Total Arsip Keseluruhan (Semua Waktu) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                            🌐 Seluruh Waktu
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Akumulasi Semua Arsip</span>
                    </div>

                    <div class="mt-5">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Laporan di Sistem</div>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-4xl sm:text-5xl font-black tracking-tight text-slate-900">
                                {{ number_format($statistik['total']) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500">keseluruhan</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-slate-50 rounded-xl py-1.5 px-1 border border-slate-100">
                            <span class="block text-[10px] text-slate-400 font-medium">Verifikasi</span>
                            <span class="block font-bold text-amber-700 mt-0.5">{{ $statistik['diajukan'] }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl py-1.5 px-1 border border-slate-100">
                            <span class="block text-[10px] text-slate-400 font-medium">Diproses</span>
                            <span class="block font-bold text-blue-700 mt-0.5">{{ $statistik['diproses'] }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl py-1.5 px-1 border border-slate-100">
                            <span class="block text-[10px] text-slate-400 font-medium">Selesai</span>
                            <span class="block font-bold text-emerald-700 mt-0.5">{{ $statistik['selesai'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Status Penanganan per Periode -->
        @php
            $periods = [
                'bulan' => [
                    'title' => 'Bulan Ini (' . $namaBulan . ')',
                    'subtitle' => 'Data dinamis bulan berjalan ' . $namaBulanTahun . ' (reset otomatis tiap awal bulan)',
                    'stats' => $statistikBulan,
                    'badge' => '⚡ Realtime ' . $namaBulan,
                    'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                ],
                'tahun' => [
                    'title' => 'Tahun Ini (' . $tahunAktif . ')',
                    'subtitle' => 'Akumulasi seluruh laporan sepanjang tahun ' . $tahunAktif,
                    'stats' => $statistikTahun,
                    'badge' => '📅 Tahun ' . $tahunAktif,
                    'badge_class' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                ],
                'semua' => [
                    'title' => 'Semua Waktu',
                    'subtitle' => 'Keseluruhan data laporan yang tercatat dalam basis data sistem',
                    'stats' => $statistik,
                    'badge' => '🌐 Seluruh Arsip',
                    'badge_class' => 'bg-slate-100 text-slate-800 border-slate-200',
                ],
            ];
        @endphp

        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rincian Status Penanganan</h2>
                    <p class="text-xs text-slate-500">Pilih rentang waktu untuk mengamati proporsi status laporan.</p>
                </div>

                <!-- Tab Tombol Switcher Periode -->
                <div class="inline-flex p-1 rounded-xl bg-slate-200/80 text-xs font-semibold self-start sm:self-auto shadow-inner" id="period-tabs">
                    <button type="button"
                            onclick="switchPeriod('bulan')"
                            id="tab-bulan"
                            class="px-3 py-1.5 rounded-lg transition bg-white text-indigo-700 shadow-sm flex items-center gap-1.5 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Bulan Ini</span>
                    </button>
                    <button type="button"
                            onclick="switchPeriod('tahun')"
                            id="tab-tahun"
                            class="px-3 py-1.5 rounded-lg transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5 font-semibold">
                        <span>📅</span>
                        <span>Tahun Ini ({{ $tahunAktif }})</span>
                    </button>
                    <button type="button"
                            onclick="switchPeriod('semua')"
                            id="tab-semua"
                            class="px-3 py-1.5 rounded-lg transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5 font-semibold">
                        <span>🌐</span>
                        <span>Semua Waktu</span>
                    </button>
                </div>
            </div>

            <!-- Panel Rincian untuk Masing-masing Periode -->
            @foreach ($periods as $key => $p)
                @php
                    $st = $p['stats'];
                @endphp
                <div id="panel-{{ $key }}" class="space-y-3 {{ $key !== 'bulan' ? 'hidden' : '' }}">
                    <div class="flex items-center justify-between text-xs text-slate-500 px-1">
                        <span>Menampilkan data untuk: <strong class="text-slate-800">{{ $p['title'] }}</strong> &bull; {{ $p['subtitle'] }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $p['badge_class'] }}">
                            {{ $p['badge'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Periode Card -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Masuk</div>
                                <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ number_format($st['total']) }}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Total pengaduan</span>
                                <span class="font-medium text-slate-700">Laporan</span>
                            </div>
                        </div>

                        <!-- Diajukan / Menunggu Card -->
                        <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-sm bg-amber-50/20 flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider flex items-center justify-between">
                                    <span>Verifikasi</span>
                                    @if (($st['belum_verifikasi'] ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-700 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            {{ $st['belum_verifikasi'] }} Belum Dibuka
                                        </span>
                                    @endif
                                </div>
                                <div class="text-3xl font-extrabold text-amber-700 mt-2">{{ number_format($st['diajukan']) }}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-amber-100 text-[11px] text-amber-700/80 flex items-center justify-between">
                                <span>{{ $st['belum_verifikasi'] ?? 0 }} Belum Diverifikasi &bull; {{ $st['verifikasi'] ?? 0 }} Diverifikasi</span>
                                <span class="font-semibold">{{ $st['diajukan'] }} lap</span>
                            </div>
                        </div>

                        <!-- Diproses Card -->
                        <div class="bg-white p-5 rounded-2xl border border-blue-200/80 shadow-sm bg-blue-50/20 flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">
                                    <span>Diproses</span>
                                </div>
                                <div class="text-3xl font-extrabold text-blue-700 mt-2">{{ number_format($st['diproses']) }}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-100 text-[10px] text-blue-700/80">
                                <span>Tahap: {{ $st['proses_identifikasi_klarifikasi'] }} Identifikasi / Klarifikasi &bull; {{ $st['proses_rencana_aksi'] }} Rencana &bull; {{ $st['proses_aksi'] }} Aksi</span>
                            </div>
                        </div>

                        <!-- Selesai Card -->
                        <div class="bg-white p-5 rounded-2xl border border-emerald-200/80 shadow-sm bg-emerald-50/20 flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">
                                    <span>Selesai</span>
                                </div>
                                <div class="text-3xl font-extrabold text-emerald-700 mt-2">{{ number_format($st['selesai']) }}</div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-emerald-100 text-[11px] text-emerald-700/80 flex items-center justify-between">
                                <span>Tuntas ditangani</span>
                                <span class="font-semibold">{{ $st['selesai'] }} lap</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Laporan Terbaru Table (1 Minggu Terakhir) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-slate-900">Laporan Pengaduan Terbaru</h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                            🗓️ 7 Hari Terakhir
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                            {{ $laporanTerbaru->count() }} laporan
                        </span>
                    </div>

                    <!-- Keterangan & Definisi Terbaru -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1.5">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            <strong>Definisi Terbaru:</strong> Aduan yang masuk dalam kurun 1 minggu ke belakang (<strong>{{ $rentangTerbaru['dari_teks'] }}</strong> s/d <strong>{{ $rentangTerbaru['sampai_teks'] }}</strong>).
                        </span>
                    </div>
                </div>

                <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 self-start sm:self-auto px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100/80 transition">
                    <span>Lihat Seluruh Arsip Laporan</span>
                    <span>→</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Pelapor</th>
                            <th class="py-3 px-4">Klasifikasi</th>
                            <th class="py-3 px-4">Uraian Pengaduan</th>
                            <th class="py-3 px-4">Waktu Masuk</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($laporanTerbaru as $lap)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4">
                                    @if ($lap->is_anonim)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">🔒 Anonim</span>
                                    @else
                                        <span class="font-semibold text-slate-900 block">{{ $lap->user->name ?? $lap->user->nama ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $lap->user->unit->nama_unit ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $lap->nama_kategori_lengkap ?? $lap->kategori?->nama_kategori ?? $lap->kategori?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="text-xs text-slate-800 font-medium block">
                                        {{ \Illuminate\Support\Str::limit($lap->deskripsi ?? $lap->isi_laporan, 50) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-900">
                                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $lap->created_at->locale('id')->diffForHumans() }}</span>
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5 font-mono">
                                        {{ $lap->created_at->format('d/m/Y H:i:s') }} WIB
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if($lap->status === 'belum_verifikasi') bg-rose-100 text-rose-800 border border-rose-200/60
                                        @elseif(in_array($lap->status, ['diajukan', 'menunggu', 'verifikasi'])) bg-amber-100 text-amber-800
                                        @elseif($lap->status === 'diproses') bg-blue-100 text-blue-800
                                        @elseif($lap->status === 'selesai') bg-emerald-100 text-emerald-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        @if($lap->status === 'belum_verifikasi')
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse mr-1.5"></span>
                                        @endif
                                        {{ $lap->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.laporan.show', $lap) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center">
                                    <div class="max-w-sm mx-auto text-center">
                                        <span class="text-3xl block mb-2">📭</span>
                                        <p class="text-xs font-semibold text-slate-700">Tidak Ada Laporan Baru</p>
                                        <p class="text-[11px] text-slate-400 mt-1">
                                            Belum ada pengaduan yang diajukan dalam 1 minggu terakhir ({{ $rentangTerbaru['dari_teks'] }} s/d {{ $rentangTerbaru['sampai_teks'] }}).
                                        </p>
                                        <a href="{{ route('admin.laporan.index') }}" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Lihat Seluruh Arsip Laporan →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>

    <!-- Script Interaktif Switcher Periode Tab & Live Clock -->
    <script>
        function switchPeriod(period) {
            const periods = ['bulan', 'tahun', 'semua'];
            periods.forEach(function (p) {
                const panel = document.getElementById('panel-' + p);
                const tab = document.getElementById('tab-' + p);

                if (!panel || !tab) return;

                if (p === period) {
                    panel.classList.remove('hidden');
                    tab.className = 'px-3 py-1.5 rounded-lg transition bg-white text-indigo-700 shadow-sm flex items-center gap-1.5 font-semibold';
                } else {
                    panel.classList.add('hidden');
                    tab.className = 'px-3 py-1.5 rounded-lg transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5 font-semibold';
                }
            });
        }

        function updateLiveClock() {
            const el = document.getElementById('live-clock');
            if (!el) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            el.innerText = timeStr + ' WIB';
        }
        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    </script>
</body>
</html>
