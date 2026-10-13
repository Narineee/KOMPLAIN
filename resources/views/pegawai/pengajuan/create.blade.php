<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Pengaduan Baru - KOMPLAIN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    <!-- Navbar Pengadu -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('pegawai.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-lg text-white">
                            K
                        </div>
                        <div>
                            <span class="font-bold text-base leading-tight block text-slate-900">KOMPLAIN</span>
                            <span class="text-[10px] text-blue-600 block font-semibold uppercase tracking-wider">Portal Pengadu</span>
                        </div>
                    </a>

                    <div class="hidden sm:flex items-center gap-2 ml-6">
                        <a href="{{ route('pegawai.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                            Daftar Aduan Saya
                        </a>
                        <a href="{{ route('pegawai.pengajuan.create') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700">
                            + Buat Pengaduan Baru
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] text-slate-500">Pengadu / Pegawai (NIP: {{ auth()->user()->nip ?? '-' }})</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="py-1.5 px-3 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb / Tombol Kembali -->
        <div class="mb-6">
            <a href="{{ route('pegawai.dashboard') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Histori Aduan
            </a>
        </div>

        <!-- Judul Halaman -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Formulir Buat Pengaduan Baru</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Sampaikan keluhan sarana, pelayanan, atau permasalahan internal dinas secara rinci untuk segera ditindaklanjuti.
            </p>
        </div>

        <!-- Alert Error Validasi -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                <div class="font-semibold flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Terdapat kesalahan pengisian formulir:
                </div>
                <ul class="list-disc list-inside space-y-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form Pengaduan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Pilihan Mode Pengajuan: Dengan Identitas vs Anonim -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                        Pilih Mode Identitas Pelapor <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-2 has-[:checked]:ring-blue-500/20">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-semibold text-sm text-slate-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Dengan Identitas
                                </span>
                                <input type="radio" name="is_anonim" value="0" {{ old('is_anonim', '0') === '0' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Laporan terhubung dengan profil pegawai Anda dan dapat dipantau langsung dari dashboard ini.
                            </p>
                        </label>

                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition hover:border-indigo-400 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-500/20">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-semibold text-sm text-slate-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Laporan Anonim
                                </span>
                                <input type="radio" name="is_anonim" value="1" {{ old('is_anonim') === '1' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Identitas nama dan NIP Anda dirahasiakan dari admin. Pengaduan tetap dapat Anda pantau langsung dari dashboard ini.
                            </p>
                        </label>
                    </div>
                </div>

                <!-- Kategori Laporan -->
                <div>
                    <label for="kategori_id" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Kategori Pengaduan <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_id" id="kategori_id" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        <option value="">-- Pilih Kategori Permasalahan --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama ?? $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Judul Laporan -->
                <div>
                    <label for="judul" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Judul Pengaduan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="judul"
                           id="judul"
                           value="{{ old('judul') }}"
                           required
                           placeholder="Contoh: AC Ruang Rapat Lt. 2 Bocor dan Tidak Dingin"
                           class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>

                <!-- Isi / Uraian Laporan -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="isi_laporan" class="block text-sm font-semibold text-slate-900">
                            Uraian Detail Laporan <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-500">Minimal 10 karakter</span>
                    </div>
                    <textarea name="isi_laporan"
                              id="isi_laporan"
                              rows="5"
                              required
                              placeholder="Deskripsikan secara detail permasalahan yang terjadi, lokasi spesifik, sejak kapan masalah terjadi, dan kendala operasional yang ditimbulkan..."
                              class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">{{ old('isi_laporan') }}</textarea>
                </div>

                <!-- Saran Penyelesaian (Opsional) -->
                <div>
                    <label for="saran_penyelesaian" class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Saran / Rekomendasi Solusi <span class="text-xs font-normal text-slate-500">(Opsional)</span>
                    </label>
                    <textarea name="saran_penyelesaian"
                              id="saran_penyelesaian"
                              rows="2"
                              placeholder="Usulan atau solusi cepat yang dapat membantu penanganan laporan..."
                              class="block w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">{{ old('saran_penyelesaian') }}</textarea>
                </div>

                <!-- Lampiran Berkas -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1.5">
                        Lampiran Berkas Pendukung <span class="text-xs font-normal text-slate-500">(Opsional)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-blue-400 transition bg-slate-50/50">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600 justify-center">
                                <label for="lampiran" class="relative cursor-pointer rounded-md font-semibold text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Pilih berkas dari perangkat</span>
                                    <input id="lampiran" name="lampiran[]" type="file" multiple class="sr-only" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                </label>
                            </div>
                            <p class="text-xs text-slate-500">
                                Format: JPG, PNG, PDF, atau DOCX (Maksimal 5MB per berkas).
                            </p>
                            <p id="file-info" class="text-xs font-medium text-emerald-600 hidden mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Konfirmasi -->
                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="konfirmasi" required checked class="mt-1 w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <span class="text-xs sm:text-sm text-slate-600 leading-snug">
                            Saya menyatakan bahwa informasi yang saya sampaikan adalah benar dan bersedia memberikan konfirmasi lebih lanjut apabila dibutuhkan oleh tim verifikator.
                        </span>
                    </label>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pegawai.dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-10">
        Portal Pengadu KOMPLAIN &copy; {{ date('Y') }}
    </footer>

    <script>
        const inputLampiran = document.getElementById('lampiran');
        const fileInfo = document.getElementById('file-info');

        if (inputLampiran && fileInfo) {
            inputLampiran.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    fileInfo.textContent = `✓ ${this.files.length} berkas dipilih`;
                    fileInfo.classList.remove('hidden');
                } else {
                    fileInfo.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
