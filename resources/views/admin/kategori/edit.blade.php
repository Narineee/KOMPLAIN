@extends('layouts.admin')

@section('title', 'Edit Klasifikasi')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.kategori.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Klasifikasi
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Edit Klasifikasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">Perbarui nama, hierarki klasifikasi induk, penjelasan/keterangan, atau status aktif klasifikasi.</p>

            <form action="{{ route('admin.kategori.update', $kategori) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Klasifikasi Induk (Parent) -->
                <div>
                    <label for="parent_id" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Klasifikasi Induk <span class="text-xs font-normal text-slate-500">(Opsional)</span>
                    </label>
                    <select name="parent_id" id="parent_id" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        <option value="">-- Jadikan Sebagai Klasifikasi Utama (Tanpa Induk) --</option>
                        @foreach ($parentKategoris as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $kategori->parent_id) == $parent->id ? 'selected' : '' }}>
                                Sub dari: {{ $parent->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Pilih klasifikasi induk jika ini merupakan sub-pilihan (contoh: R2/R3/R4 untuk klasifikasi Kendaraan).</p>
                </div>

                <!-- Nama Klasifikasi -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Nama Klasifikasi / Sub-Klasifikasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama', $kategori->nama ?? $kategori->nama_kategori) }}"
                           required
                           placeholder="Contoh: Kendaraan atau Roda 2 (R2)"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <!-- Keterangan / Penjelasan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Keterangan / Penjelasan Klasifikasi <span class="text-xs font-normal text-slate-500">(Disarankan)</span>
                    </label>
                    <textarea name="keterangan"
                              id="keterangan"
                              rows="3"
                              placeholder="Jelaskan mengenai klasifikasi ini, apa saja masalah yang dicakup, sehingga pelapor memahami maksud klasifikasi..."
                              class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">{{ old('keterangan', $kategori->keterangan) }}</textarea>
                    <p class="text-[11px] text-slate-500 mt-1">Keterangan ini akan ditampilkan kepada pegawai saat memilih klasifikasi pada formulir pengaduan.</p>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="flex items-center gap-2 text-xs sm:text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', $kategori->aktif ? '1' : '0') === '1' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="font-medium">Status Aktif (dapat langsung dipilih saat pembuatan aduan)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
