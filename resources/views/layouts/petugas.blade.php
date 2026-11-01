<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Panel Petugas Bidang') - KOMPLAIN</title>

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

    $authUser = auth()->user();

    // Inisial avatar
    $inisial = Str::of($authUser->name ?? '?')
        ->trim()
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($kata) => Str::upper(Str::substr($kata, 0, 1)))
        ->implode('');

    // Menu: dipakai untuk bottom nav (mobile) dan navbar (desktop)
    $menu = [
        [
            'route' => 'petugas.dashboard',
            'label' => 'Dashboard',
            'aktif' => request()->routeIs('petugas.dashboard'),
            'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        ],
        [
            'route' => 'petugas.pengajuan.index',
            'label' => 'Tugas Bidang',
            'aktif' => request()->routeIs('petugas.pengajuan.index', 'petugas.pengajuan.show'),
            'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        ],
        [
            'route' => 'petugas.aduan.create',
            'label' => 'Buat Aduan',
            'aktif' => request()->routeIs('petugas.aduan.create'),
            'icon' => 'M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'route' => 'petugas.aduan.index',
            'label' => 'Aduan Saya',
            'aktif' => request()->routeIs('petugas.aduan.*') && ! request()->routeIs('petugas.aduan.create'),
            'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];

    $kelasFlash = [
        'status'  => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'info'    => 'bg-sky-50 border-sky-200 text-sky-800',
        'error'   => 'bg-rose-50 border-rose-200 text-rose-800',
    ];
@endphp

<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-amber-600 selection:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4 sm:gap-6">
                @hasSection('back_url')
                    <a href="@yield('back_url')" class="inline-flex items-center gap-2 py-2 px-2.5 sm:px-3 -ml-2 rounded-xl text-slate-700 hover:text-amber-600 hover:bg-amber-50/60 transition group text-sm font-medium" aria-label="Kembali">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-amber-600 transition shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span class="font-bold text-slate-900 group-hover:text-amber-600">@yield('back_title', 'Kembali')</span>
                    </a>
                @else
                    <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-2.5">
                        <span class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            P
                        </span>
                        <div>
                            <span class="text-base font-bold tracking-tight text-slate-900 block leading-tight">KOMPLAIN</span>
                            <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200 block w-fit">
                                Petugas: {{ $authUser->unit->nama_unit ?? 'Bidang' }}
                            </span>
                        </div>
                    </a>
                @endif

                <!-- Menu desktop -->
                <nav class="hidden md:flex items-center gap-1 @hasSection('back_url') ml-2 border-l border-slate-200 pl-4 @endif" aria-label="Menu utama">
                    @foreach ($menu as $m)
                        <a href="{{ route($m['route']) }}"
                           @if ($m['aktif']) aria-current="page" @endif
                           @class([
                               'px-3.5 py-2 rounded-xl text-sm font-semibold transition',
                               'bg-amber-50 text-amber-800 border border-amber-200/60' => $m['aktif'],
                               'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! $m['aktif'],
                           ])>
                            {{ $m['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="flex items-center gap-2">
                <!-- Avatar + menu akun -->
                <details class="relative">
                    <summary class="list-none cursor-pointer w-10 h-10 rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200 text-xs font-bold flex items-center justify-center [&::-webkit-details-marker]:hidden"
                             aria-label="Menu akun">
                        {{ $inisial }}
                    </summary>
                    <div class="absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-slate-200 shadow-lg p-4 z-40">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $authUser->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">NIP {{ $authUser->nip ?? '-' }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                            {{ $authUser->unit->nama_unit ?? 'Petugas Bidang' }}
                        </span>

                        <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                            <a href="{{ route('petugas.aduan.index') }}" class="flex items-center gap-2 text-xs font-semibold text-amber-700 hover:text-amber-800">
                                <span>📄</span>
                                <span>Aduan Saya</span>
                            </a>
                            <a href="{{ route('petugas.aduan.create') }}" class="flex items-center gap-2 text-xs font-semibold text-amber-700 hover:text-amber-800">
                                <span>➕</span>
                                <span>Buat Aduan Baru</span>
                            </a>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="mt-4 pt-3 border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full text-left text-sm font-semibold text-rose-600 hover:text-rose-700">
                                Keluar
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </header>

    <!-- Konten -->
    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-28 md:pt-8 md:pb-12">
        @foreach ($kelasFlash as $kunci => $kelas)
            @if (session($kunci))
                <div role="status" class="mb-5 rounded-2xl border px-4 py-3 text-sm {{ $kelas }}">
                    {{ session($kunci) }}
                </div>
            @endif
        @endforeach

        @yield('content')
    </main>

    <footer class="hidden md:block bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
        KOMPLAIN &bull; Panel Petugas Bidang &copy; {{ date('Y') }}
    </footer>

    <!-- Bottom nav (khusus mobile) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-200 pb-[env(safe-area-inset-bottom)]" aria-label="Menu utama">
        <div class="grid grid-cols-4 gap-1 px-1.5 py-1.5">
            @foreach ($menu as $m)
                <a href="{{ route($m['route']) }}"
                   @if ($m['aktif']) aria-current="page" @endif
                   @class([
                       'flex flex-col items-center gap-1 rounded-xl py-2 text-[11px] font-semibold transition',
                       'bg-amber-50 text-amber-800 font-bold' => $m['aktif'],
                       'text-slate-500 active:bg-slate-100' => ! $m['aktif'],
                   ])>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $m['icon'] }}" />
                    </svg>
                    {{ $m['label'] }}
                </a>
            @endforeach
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
