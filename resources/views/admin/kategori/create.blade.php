<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Kategori Baru - Admin KOMPLAIN</title>

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
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.kategori.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Kategori
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Tambah Kategori Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">Tambahkan kategori pengaduan baru yang dapat dipilih oleh pegawai.</p>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="nama" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama') }}"
                           required
                           placeholder="Contoh: Sarana & Prasarana Kantor"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div>
                    <label class="flex items-center gap-2 text-xs sm:text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', '1') === '1' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="font-medium">Status Aktif (dapat langsung dipilih saat pembuatan aduan)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
