@extends('layouts.admin')

@section('title', 'Detail Pengaduan #' . $pengajuan->id)

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <!-- Breadcrumb & Back -->
    <div class="mb-6">
        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Laporan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri (2 Kolom): Detail Laporan, Tanggapan, Riwayat -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Rincian Laporan -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">ID Pengaduan</span>
                            <span class="font-mono text-xl font-bold text-indigo-700">#{{ $pengajuan->id }}</span>
                        </div>
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                @if($pengajuan->status === 'belum_verifikasi') bg-rose-100 text-rose-800 border border-rose-200/60
                                @elseif(in_array($pengajuan->status, ['menunggu', 'diajukan', 'verifikasi'])) bg-amber-100 text-amber-800
                                @elseif($pengajuan->status === 'diproses') bg-blue-100 text-blue-800
                                @elseif($pengajuan->status === 'selesai') bg-emerald-100 text-emerald-800
                                @else bg-slate-100 text-slate-800 @endif">
                                @if($pengajuan->status === 'belum_verifikasi')
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse mr-1.5"></span>
                                @endif
                                Status: {{ $pengajuan->status_label }}
                            </span>

                            @if ($pengajuan->status === 'verifikasi' || in_array($pengajuan->status, ['menunggu', 'diajukan']))
                                <form action="{{ route('admin.laporan.tandai-belum-verifikasi', $pengajuan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Tandai kembali sebagai Belum Diverifikasi"
                                            onclick="return confirm('Apakah Anda yakin ingin mengembalikan status laporan ini menjadi Belum Diverifikasi?');"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Tandai Belum Diverifikasi
                                    </button>
                                </form>
                            @elseif ($pengajuan->status === 'belum_verifikasi')
                                <form action="{{ route('admin.laporan.verifikasi', $pengajuan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Verifikasi Sekarang
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.laporan.pdf', $pengajuan) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Cetak PDF
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                            📁 {{ $pengajuan->nama_kategori_lengkap }}
                        </span>
                        @if ($pengajuan->kategori?->keterangan)
                            <p class="text-xs text-slate-500 mt-1 italic">
                                {{ $pengajuan->kategori->keterangan }}
                            </p>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-4">Pengaduan #{{ $pengajuan->id }}</h1>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Uraian Masalah</h3>
                            <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $pengajuan->deskripsi ?? $pengajuan->isi_laporan }}
                            </div>
                        </div>

                        @if ($pengajuan->saran_penyelesaian)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Saran / Rekomendasi Solusi</h3>
                                <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                                    {{ $pengajuan->saran_penyelesaian }}
                                </div>
                            </div>
                        @endif

                        @if ($pengajuan->lampirans && $pengajuan->lampirans->count() > 0)
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Lampiran Berkas Pendukung</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($pengajuan->lampirans as $lampiran)
                                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                                            <span class="truncate font-medium text-slate-700">{{ $lampiran->nama_file }}</span>
                                            <a href="{{ asset('storage/' . ($lampiran->path_file ?? $lampiran->path)) }}" target="_blank" class="ml-2 font-semibold text-indigo-600 hover:text-indigo-800 shrink-0">
                                                Unduh ↗
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bagian Berikan Tanggapan Resmi -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Tanggapan & Komentar Admin
                    </h2>

                    <!-- Form Tambah Tanggapan -->
                    <form action="{{ route('admin.laporan.tanggapan', $pengajuan) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="mb-3">
                            <textarea name="pesan"
                                      rows="3"
                                      required
                                      placeholder="Tuliskan respon, instruksi teknis, atau tanggapan resmi kepada pelapor..."
                                      class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">{{ old('pesan') }}</textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                <span>Catatan internal (hanya terlihat sesama admin)</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                                Kirim Tanggapan
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Tanggapan yang Sudah Ada -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        @forelse ($pengajuan->tanggapans as $tanggapan)
                            <div class="p-4 rounded-xl border {{ $tanggapan->is_internal ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50 border-slate-200' }} text-xs sm:text-sm">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $tanggapan->user->name ?? $tanggapan->user->nama ?? 'Admin' }}</span>
                                        @if ($tanggapan->is_internal)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-200 text-amber-900">Internal</span>
                                        @endif
                                    </div>
                                    <span class="text-slate-400 text-[11px]">{{ $tanggapan->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-slate-700 whitespace-pre-line">{{ $tanggapan->isi_tanggapan ?? $tanggapan->pesan }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada tanggapan yang diberikan untuk aduan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan (1 Kolom): Form Update Status & Metadata -->
            <div class="space-y-6">
                <!-- Box Update Status -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Ubah Status Penanganan
                    </h2>

                    <form action="{{ route('admin.laporan.status', $pengajuan) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        @if ($pengajuan->konfirmasi_pelapor === 'keberatan' || ($pengajuan->jumlah_keberatan ?? 0) > 0)
                            <div class="p-2.5 rounded-xl bg-purple-50 border border-purple-200 text-[11px] text-purple-900 leading-relaxed">
                                <strong>Catatan Peninjauan Ulang:</strong> Aduan ini memiliki riwayat keberatan pelapor. Jika diubah ke status <em>Selesai</em>, laporan akan otomatis berstatus <strong>Selesai setelah tinjau ulang</strong> (maksimal 1x keberatan).
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Utama <span class="text-rose-500">*</span></label>
                            <select id="select-status-utama"
                                    name="status"
                                    required
                                    onchange="handleStatusUtamaChange()"
                                    class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 font-medium">
                                <option value="belum_verifikasi" {{ $pengajuan->status === 'belum_verifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                                <option value="verifikasi" {{ in_array($pengajuan->status, ['menunggu', 'diajukan', 'verifikasi']) ? 'selected' : '' }}>Verifikasi (Tahap Awal)</option>
                                <option value="diproses" {{ $pengajuan->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses (Tindak Lanjut)</option>
                                <option value="selesai" {{ $pengajuan->status === 'selesai' ? 'selected' : '' }}>
                                    {{ ($pengajuan->konfirmasi_pelapor === 'keberatan' || ($pengajuan->jumlah_keberatan ?? 0) > 0 || $pengajuan->sub_status === 'selesai_setelah_tinjau_ulang') ? 'Selesai setelah tinjau ulang' : 'Selesai Penanganan' }}
                                </option>
                            </select>
                        </div>

                        <!-- Sub-Status Pilihan Ketika Status == Diproses -->
                        <div id="wrapper-sub-proses" class="{{ $pengajuan->status === 'diproses' ? '' : 'hidden' }} p-3 rounded-xl bg-blue-50/70 border border-blue-200">
                            <label class="block text-xs font-bold text-blue-900 mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                Tahap Sub-Proses <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-blue-700 mb-2">Tentukan tahapan penanganan saat ini agar transparan bagi pelapor:</p>
                            <select id="select-sub-proses"
                                    name="sub_status_proses"
                                    class="w-full text-xs rounded-lg border border-blue-300 p-2 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="identifikasi_klarifikasi" {{ in_array($pengajuan->sub_status ?? 'identifikasi_klarifikasi', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']) ? 'selected' : '' }}>Identifikasi / Klarifikasi</option>
                                <option value="rencana_aksi" {{ $pengajuan->sub_status === 'rencana_aksi' ? 'selected' : '' }}>Rencana Aksi</option>
                                <option value="aksi" {{ $pengajuan->sub_status === 'aksi' ? 'selected' : '' }}>Aksi</option>
                            </select>
                        </div>

                        <!-- Hidden Field yang Mengirimkan sub_status Akhir -->
                        <input type="hidden" name="sub_status" id="input-sub-status" value="{{ $pengajuan->sub_status }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tindak Lanjut / Alasan</label>
                            <textarea name="catatan"
                                      rows="3"
                                      placeholder="Jelaskan perkembangan tahap atau hasil keputusan penanganan..."
                                      class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600"></textarea>
                        </div>

                        <button type="submit"
                                onclick="prepareSubStatusSubmission()"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan Status</span>
                        </button>
                    </form>
                </div>

                <!-- Box Penugasan Petugas Bidang -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Penugasan Bidang
                        </span>
                        @if ($pengajuan->assignedUnit)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Sudah Ditugaskan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                Belum Ditugaskan
                            </span>
                        @endif
                    </h2>

                    @if ($pengajuan->assignedUnit)
                        <div class="mb-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                            <div>
                                <span class="text-slate-400 block font-medium">Bidang Berwenang:</span>
                                <span class="text-slate-800 font-bold block">{{ $pengajuan->assignedUnit->nama_unit }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-medium">Petugas Ditunjuk:</span>
                                <span class="text-slate-800 font-semibold block">{{ $pengajuan->petugas->name ?? $pengajuan->petugas->nama ?? '(Belum ada petugas spesifik / seluruh petugas bidang)' }}</span>
                            </div>
                            @if ($pengajuan->catatan_penugasan)
                                <div class="pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-400 block font-medium">Instruksi Admin:</span>
                                    <span class="text-slate-700 italic block">{{ $pengajuan->catatan_penugasan }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('admin.laporan.tugaskan', $pengajuan) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Bidang Tugas <span class="text-rose-500">*</span>
                            </label>
                            <select name="assigned_unit_id"
                                    id="select-assigned-unit"
                                    required
                                    onchange="filterPetugasByUnit(this.value)"
                                    class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 font-medium">
                                <option value="">-- Pilih Bidang / Unit Terkait --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (old('assigned_unit_id', $pengajuan->assigned_unit_id) == $unit->id) ? 'selected' : '' }}>
                                        {{ $unit->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Petugas Bidang <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <select name="petugas_id"
                                    id="select-petugas-id"
                                    class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 font-medium">
                                <option value="">-- Tugaskan ke Bidang (Semua Petugas) --</option>
                                @foreach ($petugasList as $petugas)
                                    <option value="{{ $petugas->id }}" data-unit="{{ $petugas->unit_id }}" {{ (old('petugas_id', $pengajuan->petugas_id) == $petugas->id) ? 'selected' : '' }}>
                                        {{ $petugas->name }} ({{ $petugas->unit->nama_unit ?? 'Umum' }})
                                    </option>
                                @endforeach
                            </select>
                            <p id="petugas-empty-hint" class="hidden text-[11px] text-amber-600 mt-1.5 font-medium">
                                ℹ️ Belum ada akun petugas terdaftar khusus pada bidang ini. Pengaduan akan tetap ditugaskan ke bidang tersebut.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Instruksi / Catatan Penugasan <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea name="catatan_penugasan"
                                      rows="2"
                                      placeholder="Contoh: Mohon koordinasikan dengan bagian terkait untuk pengecekan fisik..."
                                      class="w-full text-xs rounded-xl border border-slate-300 p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">{{ old('catatan_penugasan', $pengajuan->catatan_penugasan) }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>{{ $pengajuan->assignedUnit ? 'Perbarui Penugasan' : 'Tugaskan Pengaduan' }}</span>
                        </button>
                    </form>
                </div>

                <!-- Box Identitas Pelapor -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6" id="box-identitas-pelapor">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Identitas Pelapor
                        </h2>
                        @if ($pengajuan->metode_identitas === 'anonim' || $pengajuan->is_anonim)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                🔒 Mode Anonim
                            </span>
                        @endif
                    </div>

                    @if ($pengajuan->metode_identitas === 'anonim' || $pengajuan->is_anonim)
                        <!-- State 1: Terkunci (Default) -->
                        <div id="wrapper-anonim-terkunci" class="{{ $isAnonimUnlocked ? 'hidden' : '' }} space-y-3">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                <span class="text-2xl block mb-1">🔒</span>
                                <span class="text-xs font-bold text-slate-700 block">Laporan Anonim</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">Identitas pelapor dirahasiakan oleh sistem.</span>
                            </div>

                            <div class="pt-1">
                                <button type="button"
                                        id="btn-buka-kunci-anonim"
                                        onclick="openModalBukaAnonim()"
                                        class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span>Buka Kunci Identitas Asli</span>
                                </button>
                                <p class="text-[10px] text-slate-400 text-center mt-1.5">
                                    Khusus Admin &bull; Memerlukan password / kunci otorisasi
                                </p>
                            </div>
                        </div>

                        <!-- State 2: Terbuka (Kunci Berhasil Diverifikasi) -->
                        <div id="wrapper-anonim-terbuka" class="{{ $isAnonimUnlocked ? '' : 'hidden' }} space-y-3">
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">🔓</span>
                                    <div>
                                        <span class="font-bold block text-[11px]">Kunci Identitas Terbuka</span>
                                        <span class="text-[10px] text-amber-700 block">Identitas asli pelapor ditampilkan khusus admin</span>
                                    </div>
                                </div>
                                <form action="{{ route('admin.laporan.lock-anonim', $pengajuan) }}" method="POST" id="form-kunci-kembali">
                                    @csrf
                                    <button type="submit"
                                            onclick="handleKunciKembali(event)"
                                            class="px-2.5 py-1 bg-amber-200 hover:bg-amber-300 text-amber-900 rounded-lg text-[10px] font-semibold transition">
                                        Kunci Lagi
                                    </button>
                                </form>
                            </div>

                            <dl class="space-y-3 text-xs pt-1">
                                <div>
                                    <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                                    <dd id="anonim-nama" class="text-slate-800 font-bold mt-0.5 text-sm">{{ $pengajuan->user->name ?? $pengajuan->user->nama ?? '(Akun Pengguna Tidak Ditemukan)' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">NIP</dt>
                                    <dd id="anonim-nip" class="text-slate-800 font-mono font-semibold mt-0.5">{{ $pengajuan->user->nip ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Unit Kerja</dt>
                                    <dd id="anonim-unit" class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->unit->nama_unit ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Email Pegawai</dt>
                                    <dd id="anonim-email" class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->email ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @else
                        <dl class="space-y-3 text-xs">
                            <div>
                                <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                                <dd class="text-slate-800 font-bold mt-0.5">{{ $pengajuan->user->name ?? $pengajuan->user->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">NIP</dt>
                                <dd class="text-slate-800 font-mono font-semibold mt-0.5">{{ $pengajuan->user->nip ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Unit Kerja</dt>
                                <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->unit->nama_unit ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Email Pegawai</dt>
                                <dd class="text-slate-800 font-semibold mt-0.5">{{ $pengajuan->user->email ?? '-' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>

                <!-- Timeline Riwayat Perubahan Status -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        Riwayat Penanganan
                    </h2>

                    @if ($pengajuan->riwayatStatuses && $pengajuan->riwayatStatuses->count() > 0)
                        <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @foreach ($pengajuan->riwayatStatuses as $riw)
                                <div class="relative text-xs">
                                    <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full
                                        @if($riw->status_baru === 'selesai') bg-emerald-500
                                        @elseif($riw->status_baru === 'diproses') bg-blue-500
                                        @else bg-amber-500 @endif ring-4 ring-white"></div>
                                    <span class="font-bold text-slate-900 block">
                                        {{ $riw->status_lengkap_baru }}
                                    </span>
                                    @if ($riw->status_sebelumnya)
                                        <span class="text-[11px] text-slate-400 block">
                                            Sebelumnya: {{ $riw->status_lengkap_sebelumnya }}
                                        </span>
                                    @endif
                                    @if ($riw->catatan)
                                        <p class="text-slate-600 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $riw->catatan }}</p>
                                    @endif
                                    <span class="text-[10px] text-slate-400 block mt-1">
                                        {{ $riw->created_at->format('d M Y, H:i') }} &bull; {{ $riw->user->name ?? $riw->user->nama ?? 'Sistem' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada pembaruan status.</p>
                    @endif
                </div>
            </div>
        </div>

    @if ($pengajuan->metode_identitas === 'anonim' || $pengajuan->is_anonim)
    <!-- Modal Buka Kunci Identitas Anonim -->
    <div id="modal-buka-anonim" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalBukaAnonim()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200">
                <div class="bg-white p-6 sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="mx-auto flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-100 sm:mx-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="mt-0 text-left flex-1">
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                Otorisasi Identitas Anonim
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Pengaduan ini diajukan secara anonim. Masukkan password akun admin atau kunci otorisasi untuk membuka identitas asli pelapor.
                            </p>
                        </div>
                    </div>

                    <form id="form-buka-anonim" action="{{ route('admin.laporan.reveal-anonim', $pengajuan) }}" method="POST" onsubmit="handleBukaAnonimSubmit(event)" class="mt-5 space-y-4">
                        @csrf
                        <div id="modal-error-alert" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span id="modal-error-text">Password salah.</span>
                        </div>

                        <div>
                            <label for="input-kunci-password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Password Admin / Kunci Otorisasi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="password"
                                       id="input-kunci-password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Ketik password admin..."
                                       class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 pl-3 pr-10 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                <button type="button"
                                        onclick="togglePasswordVisibility()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs"
                                        title="Tampilkan / Sembunyikan Password">
                                    <span id="eye-icon">👁️</span>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Anda dapat memasukkan password login akun admin Anda saat ini atau kunci keamanan admin.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button"
                                    onclick="closeModalBukaAnonim()"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                                Batal
                            </button>
                            <button type="submit"
                                    id="btn-submit-unlock"
                                    class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold transition shadow-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                </svg>
                                <span id="btn-submit-unlock-text">Buka Kunci</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        function handleStatusUtamaChange() {
            const statusSelect = document.getElementById('select-status-utama');
            const wrapperProses = document.getElementById('wrapper-sub-proses');

            if (!statusSelect || !wrapperProses) return;

            const val = statusSelect.value;
            if (val === 'diproses') {
                wrapperProses.classList.remove('hidden');
            } else {
                wrapperProses.classList.add('hidden');
            }
        }

        function prepareSubStatusSubmission() {
            const statusSelect = document.getElementById('select-status-utama');
            const hiddenSubStatus = document.getElementById('input-sub-status');
            const selectProses = document.getElementById('select-sub-proses');

            if (!statusSelect || !hiddenSubStatus) return;

            const val = statusSelect.value;
            if (val === 'diproses') {
                hiddenSubStatus.value = selectProses ? selectProses.value : 'identifikasi_klarifikasi';
            } else {
                hiddenSubStatus.value = '';
            }
        }

        function openModalBukaAnonim() {
            const modal = document.getElementById('modal-buka-anonim');
            const input = document.getElementById('input-kunci-password');
            const alert = document.getElementById('modal-error-alert');
            if (modal) {
                if (alert) alert.classList.add('hidden');
                if (input) input.value = '';
                modal.classList.remove('hidden');
                setTimeout(() => input && input.focus(), 60);
            }
        }

        function closeModalBukaAnonim() {
            const modal = document.getElementById('modal-buka-anonim');
            if (modal) modal.classList.add('hidden');
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('input-kunci-password');
            const eye = document.getElementById('eye-icon');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eye) eye.textContent = '🙈';
                } else {
                    input.type = 'password';
                    if (eye) eye.textContent = '👁️';
                }
            }
        }

        async function handleBukaAnonimSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('form-buka-anonim');
            const input = document.getElementById('input-kunci-password');
            const errorAlert = document.getElementById('modal-error-alert');
            const errorText = document.getElementById('modal-error-text');
            const btnSubmit = document.getElementById('btn-submit-unlock');
            const btnText = document.getElementById('btn-submit-unlock-text');

            if (!input || !input.value.trim()) return;

            if (btnSubmit) btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Memverifikasi...';
            if (errorAlert) errorAlert.classList.add('hidden');

            try {
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value;

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const elNama = document.getElementById('anonim-nama');
                    const elNip = document.getElementById('anonim-nip');
                    const elUnit = document.getElementById('anonim-unit');
                    const elEmail = document.getElementById('anonim-email');

                    if (elNama) elNama.textContent = data.data.nama || '-';
                    if (elNip) elNip.textContent = data.data.nip || '-';
                    if (elUnit) elUnit.textContent = data.data.unit || '-';
                    if (elEmail) elEmail.textContent = data.data.email || '-';

                    const terkunci = document.getElementById('wrapper-anonim-terkunci');
                    const terbuka = document.getElementById('wrapper-anonim-terbuka');
                    if (terkunci) terkunci.classList.add('hidden');
                    if (terbuka) terbuka.classList.remove('hidden');

                    closeModalBukaAnonim();
                } else {
                    if (errorText) errorText.textContent = data.message || 'Password atau kunci otorisasi salah. Akses ditolak.';
                    if (errorAlert) errorAlert.classList.remove('hidden');
                }
            } catch (err) {
                form.submit();
            } finally {
                if (btnSubmit) btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Buka Kunci';
            }
        }

        async function handleKunciKembali(e) {
            e.preventDefault();
            const form = document.getElementById('form-kunci-kembali');
            if (!form) return;

            try {
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value;

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    }
                });

                if (response.ok) {
                    const terkunci = document.getElementById('wrapper-anonim-terkunci');
                    const terbuka = document.getElementById('wrapper-anonim-terbuka');
                    if (terbuka) terbuka.classList.add('hidden');
                    if (terkunci) terkunci.classList.remove('hidden');
                } else {
                    form.submit();
                }
            } catch (err) {
                form.submit();
            }
        }

        function filterPetugasByUnit(selectedUnitId, preserveCurrent = false) {
            const petugasSelect = document.getElementById('select-petugas-id');
            const emptyHint = document.getElementById('petugas-empty-hint');
            if (!petugasSelect) return;

            const options = Array.from(petugasSelect.querySelectorAll('option[data-unit]'));
            let matchCount = 0;

            options.forEach(opt => {
                const optUnit = opt.getAttribute('data-unit');
                if (!selectedUnitId || optUnit === String(selectedUnitId)) {
                    opt.hidden = false;
                    opt.disabled = false;
                    matchCount++;
                } else {
                    opt.hidden = true;
                    opt.disabled = true;
                }
            });

            if (emptyHint) {
                if (selectedUnitId && matchCount === 0) {
                    emptyHint.classList.remove('hidden');
                } else {
                    emptyHint.classList.add('hidden');
                }
            }

            // Jika option yang saat ini terpilih sekarang tidak valid untuk bidang ini, reset ke default
            const selectedOpt = petugasSelect.selectedOptions[0];
            if (selectedOpt && selectedOpt.disabled && !preserveCurrent) {
                petugasSelect.value = '';
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModalBukaAnonim();
            }
        });

        // Initialize state saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            handleStatusUtamaChange();

            const unitSelect = document.getElementById('select-assigned-unit');
            if (unitSelect && unitSelect.value) {
                filterPetugasByUnit(unitSelect.value, true);
            }
        });
    </script>
@endsection
