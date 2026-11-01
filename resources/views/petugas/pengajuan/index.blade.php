@extends('layouts.petugas')

@section('title', 'Daftar Tugas Pengaduan')

@section('content')
    <div class="space-y-5">
        <div>
            <h1 class="text-2xl sm:text-[28px] font-bold leading-tight tracking-tight text-slate-900">
                Daftar Tugas Pengaduan
            </h1>
            <p class="mt-1 text-sm text-slate-500">Kelola dan perbarui tindak lanjut aduan bidang Anda.</p>
        </div>

        <!-- Filter & Pencarian -->
        <form method="GET" action="{{ route('petugas.pengajuan.index') }}" class="space-y-3">
            <div class="relative flex items-center">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       placeholder="Cari ID laporan atau isi aduan..."
                       class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-xs sm:text-sm text-slate-800 placeholder-slate-400 shadow-xs focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
            </div>

            <!-- Tabs Filter Status Cepat -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('petugas.pengajuan.index', ['q' => $q]) }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition {{ empty($statusAktif) ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    Semua Status
                </a>
                <a href="{{ route('petugas.pengajuan.index', ['status' => 'baru', 'q' => $q]) }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition {{ $statusAktif === 'baru' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    Baru Ditugaskan
                </a>
                <a href="{{ route('petugas.pengajuan.index', ['status' => 'diproses', 'q' => $q]) }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition {{ $statusAktif === 'diproses' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    Sedang Diproses
                </a>
                <a href="{{ route('petugas.pengajuan.index', ['status' => 'selesai', 'q' => $q]) }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition {{ $statusAktif === 'selesai' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    Selesai
                </a>
            </div>
        </form>

        <!-- List Tugas Card -->
        <div class="space-y-3.5">
            @forelse ($pengajuans as $lap)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:border-amber-300 transition">
                    <div class="flex items-start justify-between gap-3 mb-2.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                #{{ $lap->id }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700">
                                📁 {{ $lap->nama_kategori_lengkap }}
                            </span>
                        </div>

                        <div>
                            @if ($lap->status === 'selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai
                                </span>
                            @elseif ($lap->status === 'diproses')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $lap->sub_status_label ?? 'Diproses' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Ditugaskan
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-800 font-medium leading-relaxed mb-3 line-clamp-3">
                        {{ $lap->deskripsi ?? $lap->isi_laporan }}
                    </p>

                    @if ($lap->catatan_penugasan)
                        <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 mb-3">
                            <span class="font-bold block text-[11px] text-amber-700">Instruksi Admin:</span>
                            <span>{{ $lap->catatan_penugasan }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">
                            {{ $lap->created_at->locale('id')->diffForHumans() }}
                        </span>
                        <a href="{{ route('petugas.pengajuan.show', $lap) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-xs transition">
                            <span>Tindak Lanjuti</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-12 text-center text-slate-400 text-sm">
                    <p class="text-3xl mb-2">🔍</p>
                    <p class="font-medium text-slate-600">Tidak ada pengaduan yang sesuai filter.</p>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau pilih status semua.</p>
                </div>
            @endforelse
        </div>

        @if ($pengajuans->hasPages())
            <div class="pt-2">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>
@endsection
