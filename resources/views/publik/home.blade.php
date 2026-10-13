<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Layanan Pengaduan - KOMPLAIN</title>

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
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 block leading-none">KOMPLAIN</span>
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-600">Portal Layanan Pengaduan</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cek-pengajuan.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 px-3 py-2 rounded-lg hover:bg-slate-100 transition hidden sm:inline-block">
                        Cek Status Laporan
                    </a>

                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('pegawai.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                                Dashboard Pengadu
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 text-sm text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Keluar">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login', ['role' => 'pengadu']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                            Login Pengadu
                        </a>

                        <a href="{{ route('login', ['role' => 'admin']) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                            Login Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Hero -->
    <main class="flex-1">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 text-center">
            @if (session('status'))
                <div class="max-w-md mx-auto mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 text-center shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-blue-100 text-blue-700 mb-4">
                Transparan • Responsif • Terpercaya
            </span>

            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 max-w-3xl mx-auto leading-tight">
                Layanan Pengaduan & Aspirasi Terintegrasi
            </h1>

            <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto">
                Sampaikan laporan aduan Anda dengan cepat, aman, dan dapat dipantau langsung perkembangannya. Masuk sebagai pengadu untuk memulai laporan atau sebagai admin untuk menindaklanjuti.
            </p>

            <!-- Action Cards -->
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto text-left">
                <!-- Portal Pengadu -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Portal Pengadu / Pelapor</h2>
                    <p class="text-sm text-slate-600 mb-6">
                        Buat pengaduan baru, unggah lampiran bukti, pantau tahapan proses laporan, dan terima tanggapan resmi.
                    </p>
                    <a href="{{ route('login', ['role' => 'pengadu']) }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                        Masuk sebagai Pengadu →
                    </a>
                </div>

                <!-- Portal Admin -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Portal Administrator</h2>
                    <p class="text-sm text-slate-600 mb-6">
                        Verifikasi keabsahan laporan, perbarui status penanganan, berikan tanggapan, dan unduh laporan rekapitulasi.
                    </p>
                    <a href="{{ route('login', ['role' => 'admin']) }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Masuk sebagai Administrator →
                    </a>
                </div>
            </div>

            <!-- Statistik -->
            <div class="mt-14 max-w-4xl mx-auto grid grid-cols-3 gap-4 pt-10 border-t border-slate-200">
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-100 text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-blue-600">{{ $statistik['total'] ?? 0 }}</div>
                    <div class="mt-1 text-xs sm:text-sm font-medium text-slate-500">Total Pengaduan</div>
                </div>
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-100 text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-amber-500">{{ $statistik['diproses'] ?? 0 }}</div>
                    <div class="mt-1 text-xs sm:text-sm font-medium text-slate-500">Sedang Diproses</div>
                </div>
                <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-100 text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-emerald-600">{{ $statistik['selesai'] ?? 0 }}</div>
                    <div class="mt-1 text-xs sm:text-sm font-medium text-slate-500">Selesai Ditangani</div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} KOMPLAIN — Sistem Layanan Pengaduan. Hak Cipta Dilindungi.</p>
    </footer>
</body>
</html>
