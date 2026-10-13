<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semua Laporan Pengaduan - Admin KOMPLAIN</title>

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
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh aduan masuk dari pegawai beserta status tindak lanjut.</p>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6">
            <form action="{{ route('admin.laporan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Tiket / Judul</label>
                    <input type="text"
                           name="q"
                           value="{{ $filter['q'] ?? '' }}"
                           placeholder="Ketik kata kunci..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Laporan</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Status --</option>
                        <option value="menunggu" {{ ($filter['status'] ?? '') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diproses" {{ ($filter['status'] ?? '') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ ($filter['status'] ?? '') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak" {{ ($filter['status'] ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
                    <select name="kategori_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ ($filter['kategori_id'] ?? '') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama ?? $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mode Identitas & Tombol -->
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Identitas</label>
                        <select name="is_anonim" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">Semua</option>
                            <option value="0" {{ ($filter['is_anonim'] ?? '') === '0' ? 'selected' : '' }}>Identitas Terbuka</option>
                            <option value="1" {{ ($filter['is_anonim'] ?? '') === '1' ? 'selected' : '' }}>Anonim</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.laporan.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Laporan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nomor Tiket</th>
                            <th class="py-3.5 px-4">Judul Pengaduan</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Pelapor</th>
                            <th class="py-3.5 px-4">Tanggal Masuk</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($pengajuans as $lap)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-mono font-semibold text-indigo-700">
                                    {{ $lap->nomor_tiket }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-slate-900 block">{{ $lap->judul }}</span>
                                    <span class="text-xs text-slate-400 truncate max-w-xs block">{{ Str::limit($lap->deskripsi ?? $lap->isi_laporan, 50) }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $lap->kategori->nama ?? $lap->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($lap->metode_identitas === 'anonim' || $lap->is_anonim)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                            🔒 Anonim
                                        </span>
                                    @else
                                        <span class="font-medium text-slate-900 block">{{ $lap->user->name ?? $lap->user->nama ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $lap->user->unit->nama_unit ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $lap->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if($lap->status === 'menunggu' || $lap->status === 'diajukan') bg-amber-100 text-amber-800
                                        @elseif($lap->status === 'diproses') bg-blue-100 text-blue-800
                                        @elseif($lap->status === 'selesai') bg-emerald-100 text-emerald-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ ucfirst($lap->status) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.laporan.show', $lap) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        Detail & Tindak Lanjut →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Tidak ada pengaduan yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pengajuans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
