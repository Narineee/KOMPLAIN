@extends('layouts.admin')

@section('title', 'Semua Laporan Pengaduan')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh aduan masuk dari pegawai beserta status tindak lanjut.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-800 shadow-xs flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="live-clock" class="font-mono text-xs">--:--:-- WIB</span>
                </div>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6">
            <form action="{{ route('admin.laporan.index') }}" method="GET" class="space-y-4">
                <!-- Baris 1: Parameter Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Laporan</label>
                        <input type="text"
                               name="q"
                               value="{{ $filter['q'] ?? '' }}"
                               placeholder="ID (#12), uraian, pelapor..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status Laporan</label>
                        <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">-- Semua Status --</option>
                            <option value="belum_verifikasi" {{ ($filter['status'] ?? '') === 'belum_verifikasi' ? 'selected' : '' }}>Belum Diverifikasi (Baru)</option>
                            <option value="verifikasi" {{ in_array($filter['status'] ?? '', ['verifikasi', 'menunggu', 'diajukan']) ? 'selected' : '' }}>Verifikasi (Tahap Awal)</option>
                            <option value="diproses" {{ ($filter['status'] ?? '') === 'diproses' ? 'selected' : '' }}>Sedang Diproses (Semua Tahap)</option>
                            <option value="identifikasi_klarifikasi" {{ in_array($filter['status'] ?? '', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']) ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Identifikasi / Klarifikasi</option>
                            <option value="rencana_aksi" {{ ($filter['status'] ?? '') === 'rencana_aksi' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Rencana Aksi</option>
                            <option value="aksi" {{ ($filter['status'] ?? '') === 'aksi' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Diproses: Aksi / Tindakan</option>
                            <option value="selesai" {{ in_array($filter['status'] ?? '', ['selesai', 'selesai_ditangani', 'selesai_ditolak', 'ditolak']) ? 'selected' : '' }}>Selesai (Semua)</option>
                            <option value="selesai_setelah_tinjau_ulang" {{ ($filter['status'] ?? '') === 'selesai_setelah_tinjau_ulang' ? 'selected' : '' }}>&nbsp;&nbsp;↳ Selesai setelah tinjau ulang</option>
                        </select>
                    </div>

                    <!-- Klasifikasi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Klasifikasi</label>
                        <select name="kategori_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">-- Semua Klasifikasi --</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ ($filter['kategori_id'] ?? '') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bidang Tugas -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Bidang Tugas</label>
                        <select name="assigned_unit_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">-- Semua Bidang --</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ ($filter['assigned_unit_id'] ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode Identitas -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Identitas Pelapor</label>
                        <select name="is_anonim" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">-- Semua Metode --</option>
                            <option value="0" {{ ($filter['is_anonim'] ?? '') === '0' ? 'selected' : '' }}>Identitas Terbuka</option>
                            <option value="1" {{ ($filter['is_anonim'] ?? '') === '1' ? 'selected' : '' }}>Anonim</option>
                        </select>
                    </div>
                </div>

                <!-- Baris 2: Filter Rentang Tanggal (Dari s/d Sampai Tanggal) & Tombol -->
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1 max-w-xl">
                        <!-- Dari Tanggal -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Dari Tanggal
                            </label>
                            <input type="date"
                                   name="dari_tanggal"
                                   value="{{ $filter['dari_tanggal'] ?? '' }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        </div>

                        <!-- Sampai Tanggal -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Sampai Tanggal
                            </label>
                            <input type="date"
                                   name="sampai_tanggal"
                                   value="{{ $filter['sampai_tanggal'] ?? '' }}"
                                   class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Terapkan Filter
                        </button>
                        <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if (!empty($filter['dari_tanggal']) || !empty($filter['sampai_tanggal']))
            <div class="mb-5 p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-200/80 flex flex-wrap items-center justify-between gap-2 text-xs text-indigo-900">
                <div class="flex items-center gap-2">
                    <span class="font-bold flex items-center gap-1 text-indigo-800">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Filter Rentang Tanggal:
                    </span>
                    <span>
                        @if (!empty($filter['dari_tanggal']) && !empty($filter['sampai_tanggal']))
                            <strong>{{ \Carbon\Carbon::parse($filter['dari_tanggal'])->locale('id')->translatedFormat('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($filter['sampai_tanggal'])->locale('id')->translatedFormat('d M Y') }}</strong>
                        @elseif (!empty($filter['dari_tanggal']))
                            Mulai <strong>{{ \Carbon\Carbon::parse($filter['dari_tanggal'])->locale('id')->translatedFormat('d M Y') }}</strong>
                        @else
                            Hingga <strong>{{ \Carbon\Carbon::parse($filter['sampai_tanggal'])->locale('id')->translatedFormat('d M Y') }}</strong>
                        @endif
                    </span>
                    <span class="text-indigo-600 font-semibold">({{ $pengajuans->total() }} laporan ditemukan)</span>
                </div>
                <a href="{{ route('admin.laporan.index', \Illuminate\Support\Arr::except($filter, ['dari_tanggal', 'sampai_tanggal'])) }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800 flex items-center gap-1 transition">
                    <span>Hapus Filter Tanggal</span>
                    <span>×</span>
                </a>
            </div>
        @endif

        <!-- Table Laporan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Pelapor</th>
                            <th class="py-3.5 px-4">Klasifikasi</th>
                            <th class="py-3.5 px-4">Uraian Pengaduan</th>
                            <th class="py-3.5 px-4">Waktu Masuk</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($pengajuans as $lap)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    @if (($lap->metode_identitas === 'anonim' || $lap->is_anonim) && session('unlocked_anonim_' . $lap->id))
                                        <span class="font-medium text-slate-900 block">{{ $lap->user->name ?? $lap->user->nama ?? '-' }}</span>
                                        <span class="text-[10px] text-amber-700 font-semibold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 inline-block">🔓 Anonim (Terbuka)</span>
                                    @elseif ($lap->metode_identitas === 'anonim' || $lap->is_anonim)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">
                                            🔒 Anonim
                                        </span>
                                    @else
                                        <span class="font-medium text-slate-900 block">{{ $lap->user->name ?? $lap->user->nama ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $lap->user->unit->nama_unit ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 block w-fit">
                                        {{ $lap->nama_kategori_lengkap }}
                                    </span>
                                    @if ($lap->assignedUnit)
                                        <span class="mt-1 text-[10px] font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200/60 block w-fit">
                                            🏢 {{ $lap->assignedUnit->nama_unit }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="text-xs text-slate-800 font-medium block">
                                        {{ Str::limit($lap->deskripsi ?? $lap->isi_laporan, 50) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-900">
                                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $lap->created_at->locale('id')->diffForHumans() }}</span>
                                    </span>
                                    <span class="text-[11px] text-slate-400 block mt-0.5 font-mono">
                                        {{ $lap->created_at->format('d/m/Y H:i:s') }} WIB
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if($lap->status === 'belum_verifikasi') bg-rose-100 text-rose-800 border border-rose-200/60
                                        @elseif($lap->konfirmasi_pelapor === 'keberatan' && $lap->status !== 'selesai') bg-purple-100 text-purple-800
                                        @elseif($lap->status === 'selesai' && $lap->sub_status === 'selesai_setelah_tinjau_ulang') bg-teal-100 text-teal-800
                                        @elseif(in_array($lap->status, ['menunggu', 'diajukan', 'verifikasi'])) bg-amber-100 text-amber-800
                                        @elseif($lap->status === 'diproses') bg-blue-100 text-blue-800
                                        @elseif($lap->status === 'selesai') bg-emerald-100 text-emerald-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        @if($lap->status === 'belum_verifikasi')
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse mr-1.5"></span>
                                        @endif
                                        {{ $lap->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.laporan.show', $lap) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        Detail & Tindak Lanjut →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Tidak ada pengaduan yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pengajuans->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>
    </div>

@push('scripts')
    <script>
        function updateLiveClock() {
            const el = document.getElementById('live-clock');
            if (!el) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            el.innerText = timeStr + ' WIB';
        }
        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    </script>
@endpush
@endsection
