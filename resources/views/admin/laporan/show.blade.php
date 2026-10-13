<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pengaduan {{ $pengajuan->nomor_tiket }} - Admin KOMPLAIN</title>

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
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-800 text-white">
                            Semua Laporan
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Kategori
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
        <!-- Breadcrumb & Back -->
        <div class="mb-6">
            <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Laporan
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri (2 Kolom): Detail Laporan, Tanggapan, Riwayat -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Rincian Laporan -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Nomor Tiket</span>
                            <span class="font-mono text-xl font-bold text-indigo-700">{{ $pengajuan->nomor_tiket }}</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($pengajuan->status === 'menunggu' || $pengajuan->status === 'diajukan') bg-amber-100 text-amber-800
                            @elseif($pengajuan->status === 'diproses') bg-blue-100 text-blue-800
                            @elseif($pengajuan->status === 'selesai') bg-emerald-100 text-emerald-800
                            @else bg-rose-100 text-rose-800 @endif">
                            Status: {{ ucfirst($pengajuan->status) }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-4">{{ $pengajuan->judul }}</h1>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Uraian Masalah</h3>
                            <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $pengajuan->deskripsi ?? $pengajuan->isi_laporan }}
                            </div>
                        </div>

                        @if ($pengajuan->saran_penyelesaian)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Saran / Rekomendasi Solusi</h3>
                                <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                                    {{ $pengajuan->saran_penyelesaian }}
                                </div>
                            </div>
                        @endif

                        @if ($pengajuan->lampirans && $pengajuan->lampirans->count() > 0)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Lampiran Berkas Pendukung</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($pengajuan->lampirans as $lampiran)
                                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                                            <span class="truncate font-medium text-slate-700">{{ $lampiran->nama_file }}</span>
                                            <a href="{{ asset('storage/' . ($lampiran->path_file ?? $lampiran->path)) }}" target="_blank" class="ml-2 font-semibold text-indigo-600 hover:text-indigo-800 shrink-0">
                                                Unduh ↗
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bagian Berikan Tanggapan Resmi -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Tanggapan & Komentar Admin
                    </h2>

                    <!-- Form Tambah Tanggapan -->
                    <form action="{{ route('admin.laporan.tanggapan', $pengajuan) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="mb-3">
                            <textarea name="pesan"
                                      rows="3"
                                      required
                                      placeholder="Tuliskan respon, instruksi teknis, atau tanggapan resmi kepada pelapor..."
                                      class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">{{ old('pesan') }}</textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                <span>Catatan internal (hanya terlihat sesama admin)</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                                Kirim Tanggapan
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Tanggapan yang Sudah Ada -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        @forelse ($pengajuan->tanggapans as $tanggapan)
                            <div class="p-4 rounded-xl border {{ $tanggapan->is_internal ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50 border-slate-200' }} text-xs sm:text-sm">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $tanggapan->user->name ?? $tanggapan->user->nama ?? 'Admin' }}</span>
                                        @if ($tanggapan->is_internal)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-200 text-amber-900">Internal</span>
                                        @endif
                                    </div>
                                    <span class="text-slate-400 text-[11px]">{{ $tanggapan->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-slate-700 whitespace-pre-line">{{ $tanggapan->isi_tanggapan ?? $tanggapan->pesan }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada tanggapan yang diberikan untuk aduan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan (1 Kolom): Form Update Status & Metadata -->
            <div class="space-y-6">
                <!-- Box Update Status -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Ubah Status Penanganan
                    </h2>

                    <form action="{{ route('admin.laporan.status', $pengajuan) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Baru <span class="text-rose-500">*</span></label>
                            <select name="status" required class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                                <option value="menunggu" {{ $pengajuan->status === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="diproses" {{ $pengajuan->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses / Tindak Lanjut</option>
                                <option value="selesai" {{ $pengajuan->status === 'selesai' ? 'selected' : '' }}>Selesai Ditangani</option>
                                <option value="ditolak" {{ $pengajuan->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tindak Lanjut</label>
                            <textarea name="catatan"
                                      rows="3"
                                      placeholder="Alasan perubahan status atau catatan perbaikan..."
                                      class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                            Perbarui Status
                        </button>
                    </form>
                </div>

                <!-- Box Identitas Pelapor -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        Identitas Pelapor
                    </h2>

                    @if ($pengajuan->metode_identitas === 'anonim' || $pengajuan->is_anonim)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <span class="text-2xl block mb-1">🔒</span>
                            <span class="text-xs font-bold text-slate-700 block">Laporan Anonim</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">Identitas pelapor dirahasiakan oleh sistem.</span>
                        </div>
                    @else
                        <dl class="space-y-3 text-xs">
                            <div>
                                <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                                <dd class="text-slate-800 font-bold mt-0.5">{{ $pengajuan->user->name ?? $pengajuan->user->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">NIP</dt>
                                <dd class="text-slate-800 font-mono font-semibold mt-0.5">{{ $pengajuan->user->nip ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Unit Kerja</dt>
                                <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->unit->nama_unit ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Email Pegawai</dt>
                                <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->email ?? '-' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>

                <!-- Timeline Riwayat Perubahan Status -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        Riwayat Penanganan
                    </h2>

                    @if ($pengajuan->riwayatStatuses && $pengajuan->riwayatStatuses->count() > 0)
                        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @foreach ($pengajuan->riwayatStatuses as $riw)
                                <div class="relative text-xs">
                                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-indigo-600 ring-4 ring-white"></div>
                                    <span class="font-bold text-slate-800 block">
                                        Status: {{ ucfirst($riw->status_baru) }}
                                    </span>
                                    @if ($riw->catatan)
                                        <p class="text-slate-600 mt-0.5">{{ $riw->catatan }}</p>
                                    @endif
                                    <span class="text-[10px] text-slate-400 block mt-1">
                                        {{ $riw->created_at->format('d M Y, H:i') }} &bull; {{ $riw->user->name ?? $riw->user->nama ?? 'Sistem' }}
                                    </span>
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
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
