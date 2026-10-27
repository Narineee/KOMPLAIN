@extends('layouts.pengadu')

@section('title', 'Riwayat pengaduan')

@section('content')
    @php
        $adaFilter = $q !== '' || $status !== '' || $tahunAktif !== (string) now()->year;
    @endphp

    <h1 class="text-[28px] font-bold leading-tight tracking-tight text-slate-900">Riwayat pengaduan</h1>
    <p class="mt-2 text-[15px] text-slate-500">Pantau setiap proses, dari dikirim hingga tuntas.</p>

    <!-- Pencarian & filter -->
    <form method="GET" action="{{ route('pegawai.pengajuan.index') }}" class="mt-5 space-y-4">
        <div class="relative flex items-center">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <label for="q" class="sr-only">Cari pengaduan</label>
            <input type="text" id="q" name="q" value="{{ $q }}" placeholder="Cari nomor atau judul aduan..."
                   class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 shadow-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
        </div>

        <div class="grid grid-cols-2 gap-3 md:max-w-xl">
            <div>
                <label for="status" class="block text-sm font-bold text-slate-900">Status</label>
                <div class="relative mt-2 flex items-center">
                    <select id="status" name="status" onchange="this.form.submit()"
                            class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
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
                            class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
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
        <p>{{ $pengajuans->total() }} pengaduan · Terbaru lebih dulu</p>
        @if ($adaFilter)
            <a href="{{ route('pegawai.pengajuan.index') }}" class="text-blue-600 hover:text-blue-700">Atur ulang</a>
        @endif
    </div>

    <!-- Daftar -->
    <div class="mt-3 grid gap-3 md:grid-cols-2">
        @forelse ($pengajuans as $p)
            <x-pengadu.kartu-aduan :pengajuan="$p" />
        @empty
            <div class="md:col-span-2 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <p class="text-sm font-semibold text-slate-800">
                    {{ $adaFilter ? 'Tidak ada pengaduan yang cocok' : 'Belum ada pengaduan' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $adaFilter ? 'Ubah kata kunci, status, atau periode.' : 'Aduan yang Anda kirim akan muncul di sini.' }}
                </p>
            </div>
        @endforelse
    </div>

    @if ($pengajuans->hasPages())
        <div class="mt-6">
            {{ $pengajuans->links() }}
        </div>
    @endif
@endsection