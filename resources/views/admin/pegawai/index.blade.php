@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Pengguna</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun administrator dan pegawai internal dinas.</p>
            </div>
            <div>
                <a href="{{ route('admin.pegawai.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Akun Baru
                </a>
            </div>
        </div>

        <!-- Filter Box -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm mb-6">
            <form action="{{ route('admin.pegawai.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama / NIP / Email</label>
                    <input type="text"
                           name="q"
                           value="{{ $filter['q'] ?? '' }}"
                           placeholder="Ketik kata kunci..."
                           class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Peran (Role)</label>
                    <select name="role" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" {{ ($filter['role'] ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="petugas" {{ ($filter['role'] ?? '') === 'petugas' ? 'selected' : '' }}>Petugas Bidang</option>
                        <option value="pegawai" {{ ($filter['role'] ?? '') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Unit Kerja</label>
                    <select name="unit_id" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Semua Unit --</option>
                        @foreach ($units as $u)
                            <option value="{{ $u->id }}" {{ ($filter['unit_id'] ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Akun</label>
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
                    <a href="{{ route('admin.pegawai.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Pengguna -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-semibold text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Nama Lengkap & NIP</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Role</th>
                            <th class="py-3.5 px-4">Unit Kerja</th>
                            <th class="py-3.5 px-4">Status Akun</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $user->name ?? $user->nama }}</span>
                                    <span class="text-[11px] font-mono text-slate-400 block">NIP: {{ $user->nip ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($user->role === 'admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                            Administrator
                                        </span>
                                    @elseif ($user->role === 'petugas')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Petugas Bidang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Pegawai
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $user->unit->nama_unit ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->is_aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.pegawai.edit', $user) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.pegawai.toggle-status', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold {{ $user->is_aktif ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                    {{ $user->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.pegawai.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Tidak ada pengguna yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
