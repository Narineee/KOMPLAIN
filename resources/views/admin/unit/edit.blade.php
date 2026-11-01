@extends('layouts.admin')

@section('title', 'Edit Bidang Tugas - ' . $unit->nama_unit)

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <div>
            <a href="{{ route('admin.unit.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Bidang
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-1.5">Edit Bidang Tugas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">Perbarui nama atau keterangan ruang lingkup bidang tugas.</p>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.unit.update', $unit) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama_unit" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Nama Bidang / Unit Kerja <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama_unit"
                           id="nama_unit"
                           value="{{ old('nama_unit', $unit->nama_unit) }}"
                           required
                           placeholder="Contoh: Bidang Pengelolaan Sampah & Kebersihan"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Keterangan / Ruang Lingkup Tugas <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <textarea name="keterangan"
                              id="keterangan"
                              rows="3"
                              placeholder="Jelaskan jenis penanganan atau wewenang bidang ini..."
                              class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">{{ old('keterangan', $unit->keterangan) }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.unit.index') }}" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs">
                        Perbarui Bidang
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
