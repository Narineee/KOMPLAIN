<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pengaduan {{ $pengajuan->nomor_tiket }} - KOMPLAIN</title>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Rincian Aduan -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Nomor Tiket</span>
                            <span class="font-mono text-lg font-bold text-blue-700">{{ $pengajuan->nomor_tiket }}</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($pengajuan->status === 'diajukan' || $pengajuan->status === 'menunggu') bg-blue-100 text-blue-800
                            @elseif($pengajuan->status === 'diproses') bg-amber-100 text-amber-800
                            @elseif($pengajuan->status === 'selesai') bg-emerald-100 text-emerald-800
                            @else bg-rose-100 text-rose-800 @endif">
                            Status: {{ ucfirst($pengajuan->status) }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-4">{{ $pengajuan->judul }}</h1>

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
                            <dt class="text-slate-400 font-medium">Kategori</dt>
                            <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->kategori->nama ?? $pengajuan->kategori->nama_kategori ?? '-' }}</dd>
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
                                    <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full bg-blue-600 ring-4 ring-white"></div>
                                    <div class="text-xs">
                                        <span class="font-bold text-slate-800 block">Status: {{ ucfirst($riwayat->status_baru) }}</span>
                                        @if ($riwayat->catatan)
                                            <p class="text-slate-600 mt-0.5">{{ $riwayat->catatan }}</p>
                                        @endif
                                        <span class="text-[10px] text-slate-400 block mt-1">{{ $riwayat->created_at->format('d M Y, H:i') }}</span>
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
