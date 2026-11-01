@extends('layouts.petugas')

@section('title', 'Aduan Saya')

@section('content')
    @php
        $adaFilter = $q !== '' || $status !== '' || $tahunAktif !== (string) now()->year;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-[28px] font-bold leading-tight tracking-tight text-slate-900">Aduan Saya</h1>
            <p class="mt-2 text-[15px] text-slate-500">Pantau setiap proses aduan yang Anda ajukan, dari dikirim hingga tuntas.</p>
        </div>
        <a href="{{ route('petugas.aduan.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Buat Aduan Baru</span>
        </a>
    </div>

    <!-- Catatan Ketentuan Keberatan Aduan -->
    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/70 p-4 shadow-2xs">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-600 text-white shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Ketentuan Keberatan Terhadap Hasil Pengaduan</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-700">
                    Pengajuan keberatan atas penyelesaian aduan hanya dapat dilakukan <strong>maksimal 1 (satu) kali</strong> untuk setiap pengaduan, apabila masih keberatan maka diajukan sebagai pengaduan baru. Untuk pengaduan keberatan berikutnya dilakukan melalui prosedur aduan baru.
                </p>
            </div>
        </div>
    </div>

    <!-- Pencarian & filter -->
    <form method="GET" action="{{ route('petugas.aduan.index') }}" class="mt-5 space-y-4">
        <div class="relative flex items-center">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <label for="q" class="sr-only">Cari pengaduan</label>
            <input type="text" id="q" name="q" value="{{ $q }}" placeholder="Cari nomor atau judul aduan..."
                   class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
        </div>

        <div class="grid grid-cols-2 gap-3 md:max-w-xl">
            <div>
                <label for="status" class="block text-sm font-bold text-slate-900">Status</label>
                <div class="relative mt-2 flex items-center">
                    <select id="status" name="status" onchange="this.form.submit()"
                            class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                        <option value="">Semua status</option>
                        @foreach ($opsiStatus as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($status === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="tahun" class="block text-sm font-bold text-slate-900">Periode</label>
                <div class="relative mt-2 flex items-center">
                    <select id="tahun" name="tahun" onchange="this.form.submit()"
                            class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                        @foreach ($daftarTahun as $th)
                            <option value="{{ $th }}" @selected($tahunAktif === (string) $th)>Tahun {{ $th }}</option>
                        @endforeach
                        <option value="semua" @selected($tahunAktif === 'semua')>Semua tahun</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="sr-only">Terapkan filter</button>
    </form>

    <!-- Jumlah hasil -->
    <div class="mt-4 flex items-center justify-between text-xs font-semibold text-slate-500">
        <p>{{ $pengajuans->total() }} aduan ditemukan &bull; Terbaru lebih dulu</p>
        @if ($adaFilter)
            <a href="{{ route('petugas.aduan.index') }}" class="text-amber-700 hover:text-amber-800">Atur ulang filter</a>
        @endif
    </div>

    <!-- Daftar Aduan -->
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        @forelse ($pengajuans as $p)
            <x-pengadu.kartu-aduan :pengajuan="$p" :url="route('petugas.aduan.show', $p)" />
        @empty
            <div class="md:col-span-2 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <p class="text-2xl mb-2">📬</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $adaFilter ? 'Tidak ada pengaduan yang cocok dengan filter' : 'Belum ada pengaduan yang Anda ajukan' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $adaFilter ? 'Coba ubah kata kunci pencarian, status, atau periode tahun.' : 'Pengaduan yang Anda buat akan muncul dan tercatat di sini.' }}
                </p>
                @if (! $adaFilter)
                    <a href="{{ route('petugas.aduan.create') }}"
                       class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition">
                        <span>Buat Aduan Pertama</span>
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    @if ($pengajuans->hasPages())
        <div class="mt-6">
            {{ $pengajuans->links() }}
        </div>
    @endif
@endsection
