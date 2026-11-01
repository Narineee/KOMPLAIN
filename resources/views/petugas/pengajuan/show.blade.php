@extends('layouts.petugas')

@section('title', 'Tugas #' . $pengajuan->id . ' - Detail Penanganan')
@section('back_url', route('petugas.pengajuan.index'))
@section('back_title', 'Daftar Tugas')

@section('content')
    <div class="space-y-6">

        <!-- Header Ringkasan Tugas -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                            #{{ $pengajuan->id }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                            📁 {{ $pengajuan->nama_kategori_lengkap }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">
                        Masuk pada {{ $pengajuan->created_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                <div>
                    @if ($pengajuan->status === 'selesai')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ✅ {{ $pengajuan->sub_status_label ?? 'Selesai' }}
                        </span>
                    @elseif ($pengajuan->status === 'diproses')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            ⚙️ {{ $pengajuan->sub_status_label ?? 'Sedang Diproses' }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            📌 Ditugaskan ke Bidang
                        </span>
                    @endif
                </div>
            </div>

            <!-- Kerahasiaan Identitas / Informasi Pelapor -->
            <div class="pt-4">
                @if ($pengajuan->is_anonim)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 shrink-0">
                            🔒
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Laporan Anonim</span>
                            <span class="text-[11px] text-slate-500 block">Identitas asli pelapor dirahasiakan oleh sistem untuk melindungi privasi pengadu.</span>
                        </div>
                    </div>
                @else
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Identitas Pelapor (Terbuka)</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                            <div>
                                <span class="text-slate-500">Nama:</span>
                                <span class="font-bold text-slate-800 block">{{ $pengajuan->user->name ?? $pengajuan->user->nama ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">NIP:</span>
                                <span class="font-mono font-semibold text-slate-800 block">{{ $pengajuan->user->nip ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Unit Asal:</span>
                                <span class="font-medium text-slate-800 block">{{ $pengajuan->user->unit->nama_unit ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Rincian Masalah & Instruksi Admin -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                Uraian Pengaduan
            </h2>

            <div>
                <span class="text-xs font-semibold text-slate-500 block mb-1">Deskripsi Permasalahan:</span>
                <div class="text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50/60 p-4 rounded-xl border border-slate-100">
                    {{ $pengajuan->deskripsi ?? $pengajuan->isi_laporan }}
                </div>
            </div>

            @if ($pengajuan->saran_penyelesaian)
                <div>
                    <span class="text-xs font-semibold text-slate-500 block mb-1">Harapan / Saran Penyelesaian Pelapor:</span>
                    <p class="text-xs sm:text-sm text-slate-700 italic bg-amber-50/40 p-3 rounded-xl border border-amber-100/60">
                        "{{ $pengajuan->saran_penyelesaian }}"
                    </p>
                </div>
            @endif

            @if ($pengajuan->catatan_penugasan)
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-xs font-bold text-amber-900 block mb-0.5">📌 Instruksi Khusus dari Admin:</span>
                    <p class="text-xs sm:text-sm text-amber-800">
                        {{ $pengajuan->catatan_penugasan }}
                    </p>
                </div>
            @endif

            <!-- Lampiran Berkas -->
            @if ($pengajuan->lampirans && $pengajuan->lampirans->isNotEmpty())
                <div>
                    <span class="text-xs font-semibold text-slate-500 block mb-2">Berkas / Lampiran Bukti:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($pengajuan->lampirans as $lampiran)
                            <a href="{{ asset('storage/' . $lampiran->path_file) }}" target="_blank"
                               class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-amber-400 hover:bg-amber-50/40 transition text-xs text-slate-700">
                                <span class="text-base">📎</span>
                                <span class="truncate font-medium flex-1">{{ $lampiran->nama_file ?? 'Lampiran Bukti' }}</span>
                                <span class="text-amber-700 font-semibold shrink-0">Buka &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Pembaruan Tahapan Penanganan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Perbarui Tahapan Penanganan
            </h2>

            <form action="{{ route('petugas.pengajuan.status', $pengajuan) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="select-tahap" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Tahapan / Status Tindak Lanjut <span class="text-rose-500">*</span>
                    </label>
                    <select id="select-tahap"
                            name="tahap"
                            required
                            class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 font-medium">
                        <option value="identifikasi_klarifikasi" {{ $pengajuan->sub_status === 'identifikasi_klarifikasi' ? 'selected' : '' }}>
                            1. Identifikasi / Klarifikasi Masalah (Diproses)
                        </option>
                        <option value="rencana_aksi" {{ $pengajuan->sub_status === 'rencana_aksi' ? 'selected' : '' }}>
                            2. Rencana Aksi Penyelesaian (Diproses)
                        </option>
                        <option value="aksi" {{ $pengajuan->sub_status === 'aksi' ? 'selected' : '' }}>
                            3. Pelaksanaan Aksi / Tindakan (Diproses)
                        </option>
                        <option value="selesai" {{ $pengajuan->status === 'selesai' ? 'selected' : '' }}>
                            4. Selesai Ditangani
                        </option>
                    </select>
                </div>

                <div>
                    <label for="catatan-tahap" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Catatan Perkembangan Progres <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="catatan-tahap"
                              name="catatan"
                              rows="2"
                              placeholder="Contoh: Telah dilakukan peninjauan fisik di lokasi dan bahan perbaikan sedang disiapkan..."
                              class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">{{ old('catatan') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan Tahapan</span>
                </button>
            </form>
        </div>

        <!-- Tanggapan & Catatan Tindak Lanjut -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Tanggapan & Catatan Petugas
            </h2>

            <form action="{{ route('petugas.pengajuan.tanggapan', $pengajuan) }}" method="POST" class="mb-6 space-y-3">
                @csrf
                <div>
                    <label for="pesan-tanggapan" class="sr-only">Pesan tanggapan</label>
                    <textarea id="pesan-tanggapan"
                              name="pesan"
                              rows="3"
                              required
                              placeholder="Tuliskan respon atau penjelasan resmi tindak lanjut kepada pelapor..."
                              class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">{{ old('pesan') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                        <span>Catatan internal (khusus petugas & admin, tidak dilihat pelapor)</span>
                    </label>

                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs">
                        Kirim Tanggapan
                    </button>
                </div>
            </form>

            <!-- Daftar Tanggapan -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Riwayat Komentar / Respon</span>
                @forelse ($pengajuan->tanggapans as $tanggapan)
                    <div class="p-3.5 rounded-xl border {{ $tanggapan->is_internal ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50 border-slate-200' }} text-xs sm:text-sm">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ $tanggapan->user->name ?? $tanggapan->user->nama ?? 'Petugas / Admin' }}</span>
                                @if ($tanggapan->is_internal)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-200 text-amber-900">Internal</span>
                                @endif
                            </div>
                            <span class="text-slate-400 text-[11px]">{{ $tanggapan->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $tanggapan->isi_tanggapan ?? $tanggapan->pesan }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada tanggapan yang diberikan pada tugas ini.</p>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Perubahan Status -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                Riwayat Jejak Penanganan
            </h2>
            <div class="space-y-3">
                @forelse ($pengajuan->riwayatStatuses as $riwayat)
                    <div class="flex items-start gap-3 text-xs">
                        <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                        <div class="flex-1">
                            <span class="font-bold text-slate-800 block">
                                {{ ucfirst(str_replace('_', ' ', $riwayat->status_baru ?? 'Verifikasi')) }}
                                @if ($riwayat->sub_status_baru)
                                    <span class="font-normal text-slate-500">({{ ucfirst(str_replace('_', ' ', $riwayat->sub_status_baru)) }})</span>
                                @endif
                            </span>
                            @if ($riwayat->catatan)
                                <p class="text-slate-600 mt-0.5">{{ $riwayat->catatan }}</p>
                            @endif
                            <span class="text-[10px] text-slate-400 mt-0.5 block">
                                Oleh: {{ $riwayat->user->name ?? 'Sistem' }} &bull; {{ $riwayat->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">Belum ada riwayat tercatat.</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection
