@extends('layouts.admin')

@section('title', 'Edit Akun Pengguna')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.pegawai.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Pengguna
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Edit Data Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">Perbarui informasi profil akun, unit kerja, peran, atau kata sandi pengguna.</p>

            <form action="{{ route('admin.pegawai.update', $user) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $user->name ?? $user->nama) }}"
                           required
                           placeholder="Contoh: Budi Santoso, S.Kom"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-semibold text-slate-900 mb-1.5">
                            NIP Pegawai <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="nip"
                               id="nip"
                               value="{{ old('nip', $user->nip) }}"
                               required
                               placeholder="198001012005011001"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               placeholder="nama@komplain.test"
                               class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                </div>

                <!-- Password (Opsional) -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Password Baru <span class="text-xs font-normal text-slate-500">(Kosongkan jika tidak ingin mengubah password)</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           placeholder="Minimal 8 karakter baru"
                           class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-900 mb-1.5">
                            Peran (Role) <span class="text-rose-500">*</span>
                        </label>
                        <select name="role" id="role" required onchange="toggleUnitRequired(this.value)" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="pegawai" {{ old('role', $user->role) === 'pegawai' ? 'selected' : '' }}>Pegawai (Portal Pengadu)</option>
                            <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas Bidang</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Panel Admin)</option>
                        </select>
                        @if ($user->id === auth()->id())
                            <p class="text-[11px] text-amber-600 mt-1">Catatan: Anda sedang mengedit akun Anda sendiri.</p>
                        @endif
                    </div>

                    <!-- Unit Kerja -->
                    <div>
                        <label for="unit_id" class="block text-sm font-semibold text-slate-900 mb-1.5">
                            Bidang Tugas / Unit Kerja <span id="unit_required" class="text-rose-500 {{ old('role', $user->role) === 'petugas' ? '' : 'hidden' }}">*</span>
                        </label>
                        <select name="unit_id" id="unit_id" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 py-2.5 px-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                            <option value="">-- Pilih Bidang / Unit Kerja --</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id', $user->unit_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                        <p id="unit_hint" class="text-[11px] text-amber-600 mt-1 {{ old('role', $user->role) === 'petugas' ? '' : 'hidden' }}">Wajib dipilih untuk Petugas Bidang.</p>
                    </div>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="flex items-center gap-2 text-xs sm:text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', $user->is_aktif ? '1' : '0') === '1' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <span class="font-medium">Status Akun Aktif (pengguna dapat login ke sistem)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.pegawai.index') }}" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleUnitRequired(role) {
            const req = document.getElementById('unit_required');
            const hint = document.getElementById('unit_hint');
            if (role === 'petugas') {
                req.classList.remove('hidden');
                hint.classList.remove('hidden');
            } else {
                req.classList.add('hidden');
                hint.classList.add('hidden');
            }
        }
    </script>
@endsection
