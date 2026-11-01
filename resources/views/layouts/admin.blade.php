<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Administrator') - KOMPLAIN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @stack('head')
</head>

@php
    use Illuminate\Support\Str;

    $adminUser = auth()->user();

    $inisial = Str::of($adminUser->name ?? '?')
        ->trim()
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($k) => Str::upper(Str::substr($k, 0, 1)))
        ->implode('');

    $menuUtama = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Dashboard',
            'aktif' => request()->routeIs('admin.dashboard'),
            'icon'  => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        ],
        [
            'route' => 'admin.laporan.index',
            'label' => 'Semua Laporan',
            'aktif' => request()->routeIs('admin.laporan.*'),
            'icon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        ],
    ];

    $menuMaster = [
        [
            'route' => 'admin.unit.index',
            'label' => 'Kelola Bidang',
            'aktif' => request()->routeIs('admin.unit.*', 'admin.bidang.*'),
            'icon'  => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z',
        ],
        [
            'route' => 'admin.kategori.index',
            'label' => 'Klasifikasi',
            'aktif' => request()->routeIs('admin.kategori.*'),
            'icon'  => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.386a11.968 11.968 0 003.52-3.52c.486-.827.313-1.908-.386-2.607l-9.58-9.58A2.25 2.25 0 009.568 3z M6 6h.008v.008H6V6z',
        ],
        [
            'route' => 'admin.pegawai.index',
            'label' => 'Pengguna',
            'aktif' => request()->routeIs('admin.pegawai.*', 'admin.pengguna.*'),
            'icon'  => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
    ];

    $menuLaporan = [
        [
            'route' => 'admin.rekap',
            'label' => 'Rekapitulasi',
            'aktif' => request()->routeIs('admin.rekap*'),
            'icon'  => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        ],
    ];
@endphp

<body class="min-h-full bg-slate-50 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white">

    <!-- Mobile Header & Toggle -->
    <div class="lg:hidden sticky top-0 z-40 bg-indigo-950 text-white border-b border-indigo-900 px-4 h-16 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-indigo-700 flex items-center justify-center font-bold text-base shadow-sm">
                K
            </span>
            <div>
                <span class="font-bold text-sm leading-tight block">KOMPLAIN</span>
                <span class="text-[10px] text-indigo-300 block">Panel Administrator</span>
            </div>
        </div>

        <button type="button"
                onclick="toggleMobileSidebar()"
                class="p-2 rounded-xl text-indigo-200 hover:text-white hover:bg-indigo-900 transition"
                aria-label="Buka Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Backdrop Mobile -->
    <div id="mobile-backdrop"
         onclick="toggleMobileSidebar()"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

    <!-- SIDEBAR (Fixed Desktop & Drawer Mobile) -->
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-950 text-white border-r border-indigo-900/70 flex flex-col transition-transform -translate-x-full lg:translate-x-0 shadow-xl lg:shadow-none">

        <!-- Sidebar Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-indigo-900/60">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-lg text-white shadow-sm ring-2 ring-indigo-500/40">
                    K
                </div>
                <div>
                    <span class="font-bold text-base leading-tight block tracking-tight">KOMPLAIN</span>
                    <span class="text-[10px] text-indigo-300 font-semibold block tracking-wider uppercase">Administrator</span>
                </div>
            </a>

            <button type="button"
                    onclick="toggleMobileSidebar()"
                    class="lg:hidden p-1.5 rounded-lg text-indigo-300 hover:text-white hover:bg-indigo-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Items -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">

            <!-- Bagian Utama -->
            <div>
                <span class="px-3 text-[10px] font-bold tracking-wider uppercase text-indigo-400/80 block mb-2">
                    Menu Utama
                </span>
                <nav class="space-y-1">
                    @foreach ($menuUtama as $m)
                        <a href="{{ route($m['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ $m['aktif'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-indigo-200 hover:text-white hover:bg-indigo-900/70' }}">
                            <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $m['icon'] }}" />
                            </svg>
                            <span>{{ $m['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Bagian Master Data & Organisasi -->
            <div>
                <span class="px-3 text-[10px] font-bold tracking-wider uppercase text-indigo-400/80 block mb-2">
                    Organisasi & Master
                </span>
                <nav class="space-y-1">
                    @foreach ($menuMaster as $m)
                        <a href="{{ route($m['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ $m['aktif'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-indigo-200 hover:text-white hover:bg-indigo-900/70' }}">
                            <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $m['icon'] }}" />
                            </svg>
                            <span>{{ $m['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Bagian Rekap & Laporan -->
            <div>
                <span class="px-3 text-[10px] font-bold tracking-wider uppercase text-indigo-400/80 block mb-2">
                    Analisis & Rekap
                </span>
                <nav class="space-y-1">
                    @foreach ($menuLaporan as $m)
                        <a href="{{ route($m['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition {{ $m['aktif'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-indigo-200 hover:text-white hover:bg-indigo-900/70' }}">
                            <svg class="w-5 h-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $m['icon'] }}" />
                            </svg>
                            <span>{{ $m['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Sidebar User Profile & Logout -->
        <div class="p-4 border-t border-indigo-900/60 bg-indigo-950/80">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-indigo-800 text-indigo-100 flex items-center justify-center font-bold text-xs ring-2 ring-indigo-700 shrink-0">
                    {{ $inisial }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate">{{ $adminUser->name }}</p>
                    <p class="text-[11px] text-indigo-300 truncate">{{ $adminUser->email }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-rose-600/90 hover:bg-rose-700 text-white text-xs font-semibold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT WRAPPER (Offset by sidebar width on desktop) -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

            <!-- Flash Alert Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
                    <svg class="w-5 h-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-auto">
            Panel Administrator KOMPLAIN &copy; {{ date('Y') }} &bull; Tata Kelola Pengaduan Pegawai
        </footer>
    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            const isClosed = sidebar.classList.contains('-translate-x-full');

            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
