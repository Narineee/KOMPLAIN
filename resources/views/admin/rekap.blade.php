<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekapitulasi Pengaduan - Admin KOMPLAIN</title>

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
    <!-- Navbar Admin (Disembunyikan saat dicetak) -->
    <nav class="bg-indigo-900 text-white sticky top-0 z-30 shadow-md no-print">
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
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
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
                        <a href="{{ route('admin.rekap') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-800 text-white">
                            Rekap
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-semibold text-white">{{ auth()->user()->name ?? auth()->user()->nama }}</span>
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
        <!-- Page Header (Web Screen) -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Rekapitulasi & Laporan Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Ringkasan statistik aduan, analisis per klasifikasi, filter periode, dan ekspor laporan resmi.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-800 shadow-xs flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="live-clock" class="font-mono text-xs">--:--:-- WIB</span>
                </div>
                <a href="{{ route('admin.rekap.pdf', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF 
                </a>
            </div>
        </div>

        <!-- Filter Form Box (Disembunyikan saat dicetak) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6 no-print">
            <form action="{{ route('admin.rekap') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date"
                           name="dari_tanggal"
                           value="{{ $filter['dari_tanggal'] ?? '' }}"
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date"
                           name="sampai_tanggal"
                           value="{{ $filter['sampai_tanggal'] ?? '' }}"
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <!-- Klasifikasi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Klasifikasi</label>
                    <select name="kategori_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Klasifikasi --</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ ($filter['kategori_id'] ?? '') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori ?? $kat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Pengaduan</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Status --</option>
                        <option value="belum_verifikasi" {{ ($filter['status'] ?? '') === 'belum_verifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="verifikasi" {{ in_array($filter['status'] ?? '', ['verifikasi', 'diajukan', 'menunggu']) ? 'selected' : '' }}>Verifikasi</option>
                        <option value="diproses" {{ ($filter['status'] ?? '') === 'diproses' ? 'selected' : '' }}>Sedang Diproses (Semua Tahap)</option>
                        <option value="identifikasi_klarifikasi" {{ in_array($filter['status'] ?? '', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']) ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Identifikasi / Klarifikasi</option>
                        <option value="rencana_aksi" {{ ($filter['status'] ?? '') === 'rencana_aksi' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Rencana Aksi</option>
                        <option value="aksi" {{ ($filter['status'] ?? '') === 'aksi' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Aksi / Tindakan</option>
                        <option value="selesai" {{ in_array($filter['status'] ?? '', ['selesai', 'selesai_ditangani', 'selesai_ditolak', 'ditolak']) ? 'selected' : '' }}>Selesai (Semua)</option>
                        <option value="selesai_setelah_tinjau_ulang" {{ ($filter['status'] ?? '') === 'selesai_setelah_tinjau_ulang' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Selesai setelah tinjau ulang</option>
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition text-center shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.rekap') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition text-center">
                        Reset
                    </a>
                </div>
            </form>

            @if (!empty($filter['dari_tanggal']) || !empty($filter['sampai_tanggal']) || !empty($filter['kategori_id']) || !empty($filter['status']))
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    <span class="font-medium">Filter Aktif:</span>
                    @if (!empty($filter['dari_tanggal']))
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-medium">Dari: {{ $filter['dari_tanggal'] }}</span>
                    @endif
                    @if (!empty($filter['sampai_tanggal']))
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-medium">Sampai: {{ $filter['sampai_tanggal'] }}</span>
                    @endif
                    @if (!empty($filter['kategori_id']))
                        @php
                            $katTerpilih = $kategoris->firstWhere('id', $filter['kategori_id']);
                        @endphp
                        @if ($katTerpilih)
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-medium">Klasifikasi: {{ $katTerpilih->nama_kategori ?? $katTerpilih->nama }}</span>
                        @endif
                    @endif
                    @if (!empty($filter['status']))
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-medium">Status: {{ $filter['status'] === 'belum_verifikasi' ? 'Belum Diverifikasi' : (in_array($filter['status'], ['verifikasi', 'diajukan', 'menunggu']) ? 'Verifikasi' : ucfirst(str_replace('_', ' ', $filter['status']))) }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Ringkasan Statistik Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-6">
            <!-- Total Aduan -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm card-print">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Laporan</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $ringkasan['total'] ?? 0 }}</div>
                <div class="text-[10px] text-slate-400 mt-1">Seluruh data</div>
            </div>

            <!-- Verifikasi -->
            <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-sm bg-amber-50/20 card-print">
                <div class="text-[11px] font-semibold text-amber-600 uppercase tracking-wider">Verifikasi</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-700 mt-1">{{ $ringkasan['verifikasi'] ?? $ringkasan['diajukan'] ?? 0 }}</div>
                <div class="text-[10px] text-amber-600/70 mt-1">Perlu diverifikasi</div>
            </div>

            <!-- Diproses -->
            <div class="bg-white p-4 rounded-2xl border border-blue-200/80 shadow-sm bg-blue-50/20 card-print">
                <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Diproses</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-blue-700 mt-1">{{ $ringkasan['diproses'] ?? 0 }}</div>
                <div class="text-[10px] text-blue-600/70 mt-1">Tahap tindak lanjut</div>
            </div>

            <!-- Selesai -->
            <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-sm bg-emerald-50/20 card-print">
                <div class="text-[11px] font-semibold text-emerald-600 uppercase tracking-wider">Selesai</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1">{{ $ringkasan['selesai'] ?? 0 }}</div>
                <div class="text-[10px] text-emerald-700/80 mt-1">Tuntas terselesaikan</div>
            </div>

            <!-- Anonim vs Beridentitas -->
            <div class="bg-white p-4 rounded-2xl border border-purple-200/80 shadow-sm bg-purple-50/20 card-print">
                <div class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider">Identitas</div>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-xl font-extrabold text-purple-700">{{ $ringkasan['identitas'] ?? 0 }}</span>
                    <span class="text-xs text-purple-500 font-medium">/ {{ $ringkasan['anonim'] ?? 0 }} anonim</span>
                </div>
                <div class="text-[10px] text-purple-600/70 mt-1">Terbuka vs Anonim</div>
            </div>
        </div>

        <!-- Analisis Visual & Distribusi (Disembunyikan saat cetak jika diinginkan atau dibuat rapi) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6 no-print">
            <!-- Distribusi Berdasarkan Klasifikasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Distribusi Menurut Klasifikasi</h3>
                <div class="space-y-3">
                    @forelse ($kategoris as $kat)
                        @php
                            $jumlahKat = $laporan->where('kategori_id', $kat->id)->count();
                            $persenKat = ($ringkasan['total'] ?? 0) > 0 ? round(($jumlahKat / $ringkasan['total']) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="font-medium text-slate-700">{{ $kat->nama_kategori ?? $kat->nama }}</span>
                                <span class="font-bold text-slate-900">{{ $jumlahKat }} <span class="text-slate-400 font-normal">laporan</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $persenKat }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada klasifikasi yang tercatat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Ringkasan Progres & Penyelesaian -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Status Penanganan Aduan</h3>
                    <div class="space-y-3">
                        @php
                            $totalLap = $ringkasan['total'] ?? 0;
                            $pDiajukan = $totalLap > 0 ? round((($ringkasan['diajukan'] ?? 0) / $totalLap) * 100, 1) : 0;
                            $pDiproses = $totalLap > 0 ? round((($ringkasan['diproses'] ?? 0) / $totalLap) * 100, 1) : 0;
                            $pSelesai = $totalLap > 0 ? round((($ringkasan['selesai'] ?? 0) / $totalLap) * 100, 1) : 0;
                        @endphp

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-emerald-700 font-semibold">Terselesaikan (Selesai)</span>
                                <span class="font-bold text-emerald-800">{{ $ringkasan['selesai'] ?? 0 }} laporan</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pSelesai }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-amber-700 font-semibold">Sedang Diproses</span>
                                <span class="font-bold text-amber-800">{{ $ringkasan['diproses'] ?? 0 }} laporan</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $pDiproses }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-blue-700 font-semibold">Menunggu / Diajukan</span>
                                <span class="font-bold text-blue-800">{{ $ringkasan['diajukan'] ?? 0 }} laporan</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $pDiajukan }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                    <span>Total Selesai:</span>
                    <span class="font-bold text-slate-800">{{ $ringkasan['selesai'] ?? 0 }} Laporan</span>
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Data -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden card-print">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tabel Rekapitulasi Laporan Pengaduan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Menampilkan total {{ $laporan->count() }} data sesuai parameter filter.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="tabel-rekap" class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Pelapor</th>
                            <th class="py-3 px-4">Klasifikasi</th>
                            <th class="py-3 px-4">Uraian Pengaduan</th>
                            <th class="py-3 px-4">Waktu Masuk</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Tgl Selesai</th>
                            <th class="py-3 px-4 text-right no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($laporan as $index => $lap)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4">
                                    @if ($lap->is_anonim || $lap->metode_identitas === 'anonim')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                            🔒 Anonim
                                        </span>
                                    @else
                                        <span class="font-medium text-slate-900 block">{{ $lap->user->name ?? $lap->user->nama ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $lap->user->unit->nama_unit ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $lap->kategori->nama_kategori ?? $lap->kategori->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs sm:max-w-md">
                                    <span class="text-xs text-slate-800 font-medium block">
                                        {{ Str::limit($lap->deskripsi ?? $lap->isi_laporan, 50) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-900">
                                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $lap->created_at ? $lap->created_at->locale('id')->diffForHumans() : '-' }}</span>
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5 font-mono">
                                        {{ $lap->created_at ? $lap->created_at->format('d/m/Y H:i:s') : '-' }} WIB
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
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
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    @if ($lap->status === 'selesai')
                                        {{ $lap->tanggal_selesai ? $lap->tanggal_selesai->format('d/m/Y') : $lap->updated_at->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap no-print">
                                    <a href="{{ route('admin.laporan.show', $lap) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-slate-600">Tidak ada data laporan pengaduan</p>
                                    <p class="text-xs text-slate-400 mt-1">Tidak ditemukan data pengaduan pada periode atau kriteria filter ini.</p>
                                    <a href="{{ route('admin.rekap') }}" class="inline-block mt-3 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                        Reset Filter
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer Web -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>

    <script>
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
