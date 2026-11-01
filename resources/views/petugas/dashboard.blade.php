@extends('layouts.petugas')

@section('title', 'Dashboard Petugas Bidang')

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

    <div class="lg:grid lg:grid-cols-5 lg:gap-8">

        <!-- Kolom kiri (2 kolom): Sapaan, Kartu Bidang, Aksi Cepat -->
        <div class="lg:col-span-2 space-y-5">
            <div>
                <p class="text-sm text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h1 class="mt-2 text-[26px] sm:text-[28px] font-bold leading-tight tracking-tight text-slate-900">
                    Selamat {{ $salam }}, {{ $namaDepan }} <span aria-hidden="true">👋</span>
                </h1>
                <p class="mt-1 text-sm text-slate-500">Panel penanganan pengaduan internal instansi.</p>
            </div>

            <!-- Banner Tugas Bidang (Tema Amber/Emas Penanganan) -->
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-600 via-amber-700 to-orange-800 p-6 sm:p-7 text-white shadow-md">
                <div class="relative z-10">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-amber-50 backdrop-blur-xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ $unit->nama_unit ?? 'Petugas Bidang' }}
                    </span>
                    <h2 class="mt-3 text-xl sm:text-2xl font-bold leading-tight text-white">Tindak Lanjut Aduan</h2>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-amber-100">
                        Periksa dan berikan aksi nyata untuk setiap pengaduan yang ditugaskan ke bidang Anda.
                    </p>
                    <div class="mt-5 flex flex-col sm:flex-row gap-2.5">
                        <a href="{{ route('petugas.pengajuan.index') }}"
                           class="flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-xs sm:text-sm font-bold text-amber-800 shadow-sm transition hover:bg-amber-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                            </svg>
                            Buka Daftar Tugas
                        </a>
                        <a href="{{ route('petugas.aduan.create') }}"
                           class="flex items-center justify-center gap-2 rounded-xl bg-amber-800/60 border border-white/20 px-4 py-3 text-xs sm:text-sm font-semibold text-white transition hover:bg-amber-800">
                            <span>✏️ Buat Aduan</span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Statistik Cepat Grid 2x2 -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <span class="text-xs font-semibold text-slate-500 block">Total Tugas</span>
                    <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalTugas }}</span>
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Laporan ditugaskan</span>
                </div>
                <div class="bg-white rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-xs">
                    <span class="text-xs font-semibold text-amber-700 block">Baru Masuk</span>
                    <span class="text-2xl font-bold text-amber-900 mt-1 block">{{ $baruDitugaskan }}</span>
                    <span class="text-[11px] text-amber-600 mt-0.5 block">Menunggu diproses</span>
                </div>
                <div class="bg-white rounded-2xl border border-blue-200 bg-blue-50/40 p-4 shadow-xs">
                    <span class="text-xs font-semibold text-blue-700 block">Sedang Diproses</span>
                    <span class="text-2xl font-bold text-blue-900 mt-1 block">{{ $sedangDiproses }}</span>
                    <span class="text-[11px] text-blue-600 mt-0.5 block">Tahap penanganan</span>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-xs">
                    <span class="text-xs font-semibold text-emerald-700 block">Selesai</span>
                    <span class="text-2xl font-bold text-emerald-900 mt-1 block">{{ $selesai }}</span>
                    <span class="text-[11px] text-emerald-600 mt-0.5 block">Tuntas ditangani</span>
                </div>
            </div>
        </div>

        <!-- Kolom kanan (3 kolom): Daftar Tugas Pengaduan Terbaru -->
        <div class="lg:col-span-3 mt-8 lg:mt-0 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Tugas Terbaru Bidang</h2>
                <a href="{{ route('petugas.pengajuan.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900">
                    Lihat Semua &rarr;
                </a>
            </div>

            @forelse ($tugasTerbaru as $tugas)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:border-amber-300 transition">
                    <div class="flex items-start justify-between gap-3 mb-2.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                            📁 {{ $tugas->nama_kategori_lengkap }}
                        </span>
                        @if ($tugas->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Selesai
                            </span>
                        @elseif ($tugas->status === 'diproses')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $tugas->sub_status_label ?? 'Diproses' }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Ditugaskan
                            </span>
                        @endif
                    </div>

                    <p class="text-sm font-semibold text-slate-900 leading-snug line-clamp-2 mb-2">
                        {{ $tugas->deskripsi ?? $tugas->isi_laporan }}
                    </p>

                    @if ($tugas->catatan_penugasan)
                        <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-100 text-xs text-amber-900 mb-3">
                            <span class="font-bold block text-[11px] text-amber-700">Instruksi Admin:</span>
                            <span>{{ $tugas->catatan_penugasan }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">
                            {{ $tugas->created_at->locale('id')->diffForHumans() }}
                        </span>
                        <a href="{{ route('petugas.pengajuan.show', $tugas) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-xs transition">
                            <span>Tindak Lanjuti</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-sm">
                    <p class="text-2xl mb-2">📋</p>
                    <p class="font-medium text-slate-600">Belum ada pengaduan yang ditugaskan ke bidang Anda.</p>
                    <p class="text-xs text-slate-400 mt-1">Pengaduan baru dari pegawai yang diverifikasi Admin akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection
