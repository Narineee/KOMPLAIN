@extends('layouts.admin')

@section('title', 'Kelola Bidang Tugas')

@section('content')
    <div class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Bidang Tugas</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Master data unit kerja dan bidang penanganan pengaduan dinas.</p>
            </div>
            <div>
                <a href="{{ route('admin.unit.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Bidang Baru
                </a>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <form action="{{ route('admin.unit.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text"
                           name="q"
                           value="{{ $filter['q'] ?? '' }}"
                           placeholder="Cari nama bidang atau keterangan tugas..."
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs">
                        Cari
                    </button>
                    @if (!empty($filter['q']))
                        <a href="{{ route('admin.unit.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs sm:text-sm font-semibold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Bidang -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-5">Nama Bidang Tugas</th>
                            <th class="py-3.5 px-4">Deskripsi / Keterangan</th>
                            <th class="py-3.5 px-4 text-center">Petugas Bidang</th>
                            <th class="py-3.5 px-4 text-center">Total Pegawai</th>
                            <th class="py-3.5 px-4 text-center">Pengaduan Ditangani</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($units as $u)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 border border-indigo-100">
                                            🏢
                                        </div>
                                        <span class="font-bold text-slate-900 block">{{ $u->nama_unit }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 max-w-sm text-slate-600 text-xs leading-relaxed">
                                    {{ $u->keterangan ?: '-' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $u->petugas_count }} Petugas
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center text-xs font-semibold text-slate-600">
                                    {{ $u->users_count }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $u->pengajuans_count }} Aduan
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.unit.edit', $u) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>
                                        <span class="text-slate-200">|</span>
                                        <form action="{{ route('admin.unit.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang {{ $u->nama_unit }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Belum ada data bidang tugas yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($units->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $units->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
