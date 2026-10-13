<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Pengadu - KOMPLAIN</title>

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
                        <a href="{{ route('pegawai.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
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
                <h1 class="text-2xl font-bold text-slate-900">Histori Pengaduan Saya</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar laporan yang Anda ajukan dengan identitas terdaftar.</p>
            </div>
            <div>
                <a href="{{ route('pegawai.pengajuan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Aduan Baru
                </a>
            </div>
        </div>

        <!-- Table Complaints -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Nomor Tiket</th>
                            <th class="py-3 px-4">Judul Pengaduan</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Tanggal Pengajuan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($pengajuans as $lap)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900">{{ $lap->nomor_tiket }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $lap->judul }}</td>
                                <td class="py-3.5 px-4">{{ $lap->kategori->nama ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $lap->created_at->format('d M Y, H:i') }}</td>
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
                                    <a href="{{ route('pegawai.pengajuan.show', $lap) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Anda belum pernah mengajukan pengaduan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengajuans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Portal Pengadu KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
