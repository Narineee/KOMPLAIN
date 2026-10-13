<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaduan Berhasil Dikirim - KOMPLAIN</title>

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
                </div>

                <div class="flex items-center gap-4">
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
    <main class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col justify-center">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-6 sm:p-10 text-center">
            <!-- Icon Sukses -->
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Laporan Berhasil Diajukan!</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-md mx-auto">
                Pengaduan Anda telah tercatat ke dalam sistem dan akan segera diverifikasi oleh tim administrator.
            </p>

            <!-- Card Informasi Tiket & Token -->
            <div class="mt-6 p-5 rounded-xl bg-slate-50 border border-slate-200 text-left space-y-4">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Judul Pengaduan</span>
                    <span class="block text-sm font-bold text-slate-800 mt-0.5">{{ $tiketSukses['judul'] ?? '-' }}</span>
                </div>

                <div class="pt-3 border-t border-slate-200/80">
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                    <div class="flex items-center justify-between mt-1">
                        <span class="font-mono text-base font-bold text-blue-700">{{ $tiketSukses['nomor_tiket'] ?? '-' }}</span>
                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ $tiketSukses['nomor_tiket'] }}'); alert('Nomor tiket berhasil disalin!');"
                                class="text-xs font-medium text-slate-500 hover:text-blue-600 bg-white border border-slate-200 px-2.5 py-1 rounded-md transition shadow-2xs">
                            Salin
                        </button>
                    </div>
                </div>

                @if (!empty($tiketSukses['token_rahasia']))
                    <div class="pt-3 border-t border-slate-200/80">
                        <div class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                            <span>Token Rahasia Pelacakan</span>
                            <span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Simpan Baik-baik</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="font-mono text-xs sm:text-sm font-semibold text-slate-700 break-all">{{ $tiketSukses['token_rahasia'] }}</span>
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('{{ $tiketSukses['token_rahasia'] }}'); alert('Token rahasia berhasil disalin!');"
                                    class="text-xs font-medium text-slate-500 hover:text-blue-600 bg-white border border-slate-200 px-2.5 py-1 rounded-md transition shrink-0 ml-2 shadow-2xs">
                                Salin
                            </button>
                        </div>
                        <p class="text-[11px] text-amber-700 mt-1.5 leading-relaxed bg-amber-50/70 p-2.5 rounded-lg border border-amber-100">
                            ⚠️ Token ini digunakan untuk membuka dan mengecek status laporan Anda melalui menu <strong>Cek Pengajuan</strong> tanpa login. Simpan nomor tiket dan token ini sekarang.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Tombol Navigasi -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('pegawai.dashboard') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-500/25 transition">
                    Kembali ke Dashboard Saya
                </a>
                <a href="{{ route('pegawai.pengajuan.create') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                    + Buat Aduan Lainnya
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Portal Pengadu KOMPLAIN &copy; {{ date('Y') }}
    </footer>
</body>
</html>
