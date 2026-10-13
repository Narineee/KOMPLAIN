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
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ringkasan Sistem Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau status laporan dan tindak lanjut penanganan pengaduan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.laporan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    Lihat Seluruh Laporan →
                </a>
            </div>
        </div>

        <!-- Statistik Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Laporan</div>
                <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $statistik['total'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-blue-200/80 shadow-sm bg-blue-50/20">
                <div class="text-xs font-medium text-blue-600 uppercase tracking-wider">Diajukan</div>
                <div class="text-3xl font-extrabold text-blue-700 mt-2">{{ $statistik['diajukan'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-sm bg-amber-50/20">
                <div class="text-xs font-medium text-amber-600 uppercase tracking-wider">Diproses</div>
                <div class="text-3xl font-extrabold text-amber-700 mt-2">{{ $statistik['diproses'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-emerald-200/80 shadow-sm bg-emerald-50/20">
                <div class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Selesai</div>
                <div class="text-3xl font-extrabold text-emerald-700 mt-2">{{ $statistik['selesai'] ?? 0 }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-rose-200/80 shadow-sm bg-rose-50/20">
                <div class="text-xs font-medium text-rose-600 uppercase tracking-wider">Ditolak</div>
                <div class="text-3xl font-extrabold text-rose-700 mt-2">{{ $statistik['ditolak'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Laporan Terbaru Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Laporan Pengaduan Terbaru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar laporan yang baru masuk ke sistem.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tiket</th>
                            <th class="py-3 px-4">Judul Laporan</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Pelapor</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($laporanTerbaru as $lap)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900">{{ $lap->nomor_tiket }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $lap->judul }}</td>
                                <td class="py-3.5 px-4">{{ $lap->kategori->nama ?? '-' }}</td>
                                <td class="py-3.5 px-4">
                                    @if ($lap->is_anonim)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">Anonim</span>
                                    @else
                                        <span>{{ $lap->user->name ?? 'Identitas Tercatat' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if($lap->status === 'diajukan') bg-blue-100 text-blue-800
                                        @elseif($lap->status === 'diproses') bg-amber-100 text-amber-800
                                        @elseif($lap->status === 'selesai') bg-emerald-100 text-emerald-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ ucfirst($lap->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('admin.laporan.show', $lap) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada laporan pengaduan yang diajukan.
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
</body>
</html>
