@extends('layouts.pengadu')

@section('title', 'Dashboard')

@section('content')
    @php
        $user = auth()->user();
        $jam = now()->hour;
        $salam = match (true) {
            $jam < 11 => 'pagi',
            $jam < 15 => 'siang',
            $jam < 18 => 'sore',
            default => 'malam',
        };
        $namaDepan = \Illuminate\Support\Str::before(trim($user->name), ' ');
    @endphp

    <div class="lg:grid lg:grid-cols-5 lg:gap-10">

        <!-- Kolom kiri: sapaan, ajakan, pengingat -->
        <div class="lg:col-span-2 space-y-5">
            <div>
                <p class="text-sm text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-2 text-[28px] font-bold leading-tight tracking-tight text-slate-900">
                    Selamat {{ $salam }}, {{ $namaDepan }} <span aria-hidden="true">👋</span>
                </h1>
                <p class="mt-2 text-[15px] text-slate-500">Suara Anda, perbaikan kita bersama.</p>
            </div>

            <!-- Ajakan membuat pengaduan (Tema Biru Senada Login, Kontras Tinggi & Tidak Monoton) -->
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-6 sm:p-7 text-white shadow-md">
                <div class="relative z-10">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-blue-100 backdrop-blur-xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Layanan Aduan Internal
                    </span>
                    <h2 class="mt-3 text-2xl font-bold leading-tight text-white">Ada kendala pelayanan?</h2>
                    <p class="mt-2 text-sm leading-relaxed text-blue-100">
                        Sampaikan aduan dengan mudah. Kami bantu tindak lanjuti secara cepat, transparan, dan terkoordinasi.
                    </p>
                    <a href="{{ route('pegawai.pengajuan.create') }}"
                       class="mt-5 flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3.5 text-sm font-bold text-blue-700 shadow-sm transition hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Buat pengaduan baru
                    </a>
                </div>
            </section>

            <!-- Pengingat konfirmasi -->
            @if ($menungguKonfirmasi->isNotEmpty())
                @php
                    $pertama = $menungguKonfirmasi->first();
                    $sisa = $menungguKonfirmasi->count() - 1;
                @endphp
                <a href="{{ route('pegawai.pengajuan.show', $pertama) }}"
                   class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 transition hover:bg-amber-100/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                    <svg class="w-6 h-6 shrink-0 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-amber-800">
                            {{ $menungguKonfirmasi->count() }} aduan menunggu konfirmasi Anda
                        </p>
                        <p class="mt-1 text-[13px] leading-relaxed text-slate-700">
                            {{ $pertama->judul }} telah ditandai selesai. Periksa hasilnya, lalu konfirmasi atau ajukan keberatan.
                            @if ($sisa > 0)
                                <span class="font-semibold">(+{{ $sisa }} aduan lainnya)</span>
                            @endif
                        </p>
                    </div>
                </a>
            @endif

            <!-- Catatan Ketentuan Keberatan Aduan -->
            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4.5 shadow-2xs">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Ketentuan Keberatan Terhadap Hasil Pengaduan</h3>
                        <p class="mt-1 text-xs leading-relaxed text-slate-700">
                            Pengajuan keberatan atas penyelesaian aduan hanya dapat dilakukan <strong>maksimal 1 (satu) kali</strong> untuk setiap pengaduan, apabila masih keberatan maka diajukan sebagai pengaduan baru. Untuk pengaduan keberatan berikutnya dilakukan melalui prosedur aduan baru.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom kanan: aduan terbaru -->
        <section class="mt-8 lg:mt-0 lg:col-span-3" aria-labelledby="judul-terbaru">
            <div class="flex items-center justify-between">
                <h2 id="judul-terbaru" class="text-lg font-bold text-slate-900">Aduan terbaru</h2>
                @if ($terbaru->isNotEmpty())
                    <a href="{{ route('pegawai.pengajuan.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                        Lihat semua
                    </a>
                @endif
            </div>

            <div class="mt-3 space-y-3">
                @forelse ($terbaru as $p)
                    <x-pengadu.kartu-aduan :pengajuan="$p" />
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-800">Belum ada aduan</p>
                        <p class="mt-1 text-sm text-slate-500">Aduan yang Anda kirim akan muncul di sini.</p>
                        <a href="{{ route('pegawai.pengajuan.create') }}"
                           class="mt-4 inline-flex rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            Buat pengaduan pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
