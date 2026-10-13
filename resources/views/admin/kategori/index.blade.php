<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Kategori - Admin KOMPLAIN</title>

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
                        <a href="{{ route('admin.laporan.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Semua Laporan
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-800 text-white">
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

        @if (session('info'))
            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Master Kategori Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola jenis kategori masalah yang dapat dipilih pegawai saat membuat aduan.</p>
            </div>
            <div>
                <a href="{{ route('admin.kategori.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori Baru
                </a>
            </div>
        </div>

        <!-- Table Kategori -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Kategori</th>
                            <th class="py-3.5 px-4">Total Aduan</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($kategoris as $kategori)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $kategori->nama ?? $kategori->nama_kategori }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $kategori->pengajuans_count ?? $kategori->pengajuans()->count() }} aduan
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ ($kategori->aktif ?? $kategori->is_aktif) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ($kategori->aktif ?? $kategori->is_aktif) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.kategori.edit', $kategori) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>
                                        <span class="text-slate-300">|</span>
                                        <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan kategori ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400">
                                    Belum ada kategori yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kategoris->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $kategoris->links() }}
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
