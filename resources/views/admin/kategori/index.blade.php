@extends('layouts.admin')

@section('title', 'Master Klasifikasi Pengaduan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Master Klasifikasi Pengaduan</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola klasifikasi pengaduan, pilihan sub-klasifikasi turunan, serta keterangannya.</p>
            </div>
            <div>
                <a href="{{ route('admin.kategori.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Klasifikasi Baru
                </a>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6">
            <form action="{{ route('admin.kategori.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / Keterangan</label>
                    <input type="text"
                           name="q"
                           value="{{ $filter['q'] ?? '' }}"
                           placeholder="Ketik kata kunci..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tingkat Hierarki</label>
                    <select name="tipe" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Hierarki --</option>
                        <option value="parent" {{ ($filter['tipe'] ?? '') === 'parent' ? 'selected' : '' }}>Hanya Klasifikasi Utama</option>
                        <option value="child" {{ ($filter['tipe'] ?? '') === 'child' ? 'selected' : '' }}>Hanya Sub-Klasifikasi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Status --</option>
                        <option value="aktif" {{ ($filter['status'] ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ ($filter['status'] ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.kategori.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Klasifikasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Klasifikasi & Hierarki</th>
                            <th class="py-3.5 px-4">Keterangan / Penjelasan</th>
                            <th class="py-3.5 px-4">Sub-Klasifikasi</th>
                            <th class="py-3.5 px-4">Total Aduan</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($kategoris as $kategori)
                            <tr class="hover:bg-slate-50/60 transition {{ $kategori->isChild() ? 'bg-slate-50/30' : '' }}">
                                <td class="py-3.5 px-4">
                                    @if ($kategori->isChild())
                                        <div class="flex items-center gap-2 pl-4">
                                            <span class="text-indigo-400 font-mono text-sm leading-none">↳</span>
                                            <div>
                                                <span class="font-bold text-slate-800 block">{{ $kategori->nama_kategori }}</span>
                                                <span class="text-[10px] text-slate-400 block font-medium">
                                                    Sub dari: <span class="text-indigo-600">{{ $kategori->parent->nama_kategori ?? '-' }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <span class="font-bold text-slate-900 text-sm block">{{ $kategori->nama_kategori }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 mt-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                Klasifikasi Utama
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    @if ($kategori->keterangan)
                                        <p class="text-xs text-slate-600 line-clamp-2" title="{{ $kategori->keterangan }}">
                                            {{ $kategori->keterangan }}
                                        </p>
                                    @else
                                        <span class="text-slate-400 italic text-xs">- Tidak ada keterangan -</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($kategori->isParent() && $kategori->children->count() > 0)
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach ($kategori->children as $child)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                                    {{ $child->nama_kategori }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($kategori->isParent())
                                        <span class="text-slate-400 text-xs">Langsung (tanpa sub)</span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $kategori->pengajuans_count ?? 0 }} aduan
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $kategori->aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $kategori->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.kategori.edit', $kategori) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>
                                        <span class="text-slate-300">|</span>
                                        <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan klasifikasi ini?')" class="inline">
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
                                    Belum ada klasifikasi yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kategoris->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $kategoris->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
