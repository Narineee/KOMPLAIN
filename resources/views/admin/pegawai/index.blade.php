<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengguna - Admin KOMPLAIN</title>

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
                        <a href="{{ route('admin.kategori.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-200 hover:text-white hover:bg-indigo-800/60 transition">
                            Kategori
                        </a>
                        <a href="{{ route('admin.pegawai.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-800 text-white">
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

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Pengguna</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun administrator dan pegawai internal dinas.</p>
            </div>
            <div>
                <a href="{{ route('admin.pegawai.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Akun Baru
                </a>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6">
            <form action="{{ route('admin.pegawai.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / NIP / Email</label>
                    <input type="text"
                           name="q"
                           value="{{ $filter['q'] ?? '' }}"
                           placeholder="Ketik kata kunci..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Peran (Role)</label>
                    <select name="role" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" {{ ($filter['role'] ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="pegawai" {{ ($filter['role'] ?? '') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Kerja</label>
                    <select name="unit_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Unit --</option>
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ ($filter['unit_id'] ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.pegawai.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Pengguna -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Lengkap & NIP</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Role</th>
                            <th class="py-3.5 px-4">Unit Kerja</th>
                            <th class="py-3.5 px-4">Status Akun</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $user->name ?? $user->nama }}</span>
                                    <span class="text-[11px] font-mono text-slate-400 block">NIP: {{ $user->nip ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $user->unit->nama_unit ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->is_aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.pegawai.edit', $user) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.pegawai.toggle-status', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold {{ $user->is_aktif ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                    {{ $user->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.pegawai.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Tidak ada pengguna yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Panel Administrator KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
