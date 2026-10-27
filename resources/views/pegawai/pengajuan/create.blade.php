@extends('layouts.pengadu')

@section('title', 'Pengaduan baru')
@section('back_url', route('pegawai.dashboard'))
@section('back_title', 'Pengaduan baru')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-[28px] font-bold leading-tight tracking-tight text-slate-900">Sampaikan aduan Anda</h1>
    <p class="mt-2 text-[15px] leading-relaxed text-slate-500">
        Ceritakan kendala secara jelas agar kami dapat membantu dengan tepat.
    </p>

    <!-- Info privasi -->
    <div class="mt-5 flex gap-3 rounded-2xl bg-blue-50/70 border border-blue-100 p-4">
        <svg class="w-6 h-6 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        <div>
            <p class="text-sm font-bold text-blue-900">Aduan ditangani secara internal</p>
            <p class="mt-0.5 text-[13px] leading-relaxed text-slate-700">
                Identitas dan lampiran hanya dapat diakses oleh Anda dan petugas yang menangani aduan.
            </p>
        </div>
    </div>

    <!-- Error validasi -->
    @if ($errors->any())
        <div role="alert" class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-semibold">Ada isian yang perlu diperbaiki:</p>
            <ul class="mt-1.5 list-disc list-inside space-y-0.5 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pegawai.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        <input type="hidden" name="konfirmasi" value="1">

        <!-- Mode identitas -->
        <fieldset>
            <legend class="text-[15px] font-bold text-slate-900">Pilih mode identitas pengadu <span class="text-rose-500">*</span></legend>
            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="pilih-mode block cursor-pointer">
                    <input type="radio" name="is_anonim" value="0" class="sr-only" {{ old('is_anonim', '0') === '0' ? 'checked' : '' }}>
                    <div class="pilih-kartu h-full rounded-2xl border border-slate-200 bg-white p-4 text-slate-900">
                        <div class="flex items-start justify-between">
                            <span class="pilih-ikon flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </span>
                            <span class="pilih-cek flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-300 text-transparent" aria-hidden="true">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-[15px] font-bold">Dengan identitas</p>
                        <p class="mt-1 text-xs leading-snug opacity-70">Laporan terhubung dengan profil pegawai Anda dan dapat dipantau dari dashboard.</p>
                    </div>
                </label>
                <label class="pilih-mode block cursor-pointer">
                    <input type="radio" name="is_anonim" value="1" class="sr-only" {{ old('is_anonim') === '1' ? 'checked' : '' }}>
                    <div class="pilih-kartu h-full rounded-2xl border border-slate-200 bg-white p-4 text-slate-900">
                        <div class="flex items-start justify-between">
                            <span class="pilih-ikon flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </span>
                            <span class="pilih-cek flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-300 text-transparent" aria-hidden="true">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-[15px] font-bold">Anonim</p>
                        <p class="mt-1 text-xs leading-snug opacity-70">Nama dan NIP Anda disembunyikan dari tampilan admin. Aduan tetap dapat Anda pantau dari dashboard.</p>
                    </div>
                </label>
            </div>
        </fieldset>

        <!-- Klasifikasi -->
        <div>
            <label for="parent_kategori_select" class="block text-sm font-bold text-slate-900">
                Klasifikasi <span class="text-rose-500">*</span>
            </label>
            <div class="relative mt-2 flex items-center">
                <select id="parent_kategori_select"
                        class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Pilih klasifikasi</option>
                    @foreach ($parentKategoris as $kategori)
                        <option value="{{ $kategori->id }}"
                                data-keterangan="{{ $kategori->keterangan ?? '' }}"
                                data-children="{{ json_encode($kategori->activeChildren->map(fn ($c) => ['id' => $c->id, 'nama' => $c->nama_kategori, 'keterangan' => $c->keterangan])) }}">
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <p id="kategori_keterangan_text" class="mt-2 hidden text-xs leading-relaxed text-slate-500"></p>

            <!-- Sub-klasifikasi (diisi JavaScript) -->
            <div id="subkategori_wrapper" class="mt-3 hidden rounded-2xl border border-slate-200 bg-white p-4">
                <p class="text-sm font-bold text-slate-900">Pilih jenis yang paling sesuai <span class="text-rose-500">*</span></p>
                <div id="subkategori_options_container" class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-2"></div>
            </div>

            <input type="hidden" name="kategori_id" id="kategori_id" value="{{ old('kategori_id') }}">
            @error('kategori_id')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Uraian -->
        <div>
            <label for="isi_laporan" class="block text-sm font-bold text-slate-900">
                Uraian detail aduan <span class="text-rose-500">*</span>
            </label>
            <textarea name="isi_laporan" id="isi_laporan" rows="6" required minlength="10"
                      placeholder="Contoh: AC di ruang rapat B tidak mengeluarkan udara dingin sejak Senin pagi."
                      class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('isi_laporan') }}</textarea>
            <p class="mt-1.5 text-xs text-slate-500">Jelaskan kejadian, waktu, dan dampak yang Anda alami.</p>
        </div>

        <!-- Saran -->
        <div>
            <label for="saran_penyelesaian" class="block text-sm font-bold text-slate-900">Saran / rekomendasi solusi</label>
            <textarea name="saran_penyelesaian" id="saran_penyelesaian" rows="2"
                      class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('saran_penyelesaian') }}</textarea>
        </div>

        <!-- Lampiran -->
        <div>
            <span class="block text-sm font-bold text-slate-900">Lampiran pendukung</span>
            <label for="lampiran"
                   class="mt-2 flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-slate-300 bg-white px-6 py-8 text-center transition hover:border-blue-400 focus-within:border-blue-600">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <span class="mt-2 text-sm font-semibold text-blue-600">Pilih foto atau dokumen</span>
                <span class="mt-1 text-xs text-slate-500">JPG, PNG, PDF · Maks. 5 MB per berkas</span>
            </label>
            <input id="lampiran" name="lampiran[]" type="file" multiple accept=".jpg,.jpeg,.png,.pdf" class="sr-only">
            <p id="lampiran-error" class="mt-2 hidden text-xs text-rose-600" role="alert"></p>
            <ul id="lampiran-list" class="mt-3 space-y-2"></ul>
        </div>

        <p class="text-xs leading-relaxed text-slate-500">
            <span class="text-rose-500">*</span> Wajib diisi. Pastikan informasi yang disampaikan benar dan tidak memuat data sensitif yang tidak diperlukan.
        </p>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
            Kirim pengaduan
        </button>
    </form>
</div>
@endsection

@push('head')
<style>
    .pilih-kartu, .pilih-ikon, .pilih-cek { transition: background-color .15s, border-color .15s, color .15s; }
    .pilih-mode:hover .pilih-kartu { border-color: #60a5fa; }
    .pilih-mode input:checked + .pilih-kartu { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
    .pilih-mode input:checked + .pilih-kartu .pilih-ikon { background: rgba(255,255,255,.15); color: #fff; }
    .pilih-mode input:checked + .pilih-kartu .pilih-cek { background: #3b82f6; border-color: #3b82f6; color: #fff; }
    .pilih-mode input:focus-visible + .pilih-kartu { outline: 2px solid #2563eb; outline-offset: 2px; }
</style>
@endpush

@push('scripts')
<script>
    // ---------- Lampiran: daftar berkas + hapus per berkas ----------
    (function () {
        const input = document.getElementById('lampiran');
        const list = document.getElementById('lampiran-list');
        const err = document.getElementById('lampiran-error');
        const MAKS = 5 * 1024 * 1024;
        const IZIN = ['jpg', 'jpeg', 'png', 'pdf'];
        let berkas = [];

        function ukuran(b) {
            return b >= 1048576
                ? (b / 1048576).toFixed(1).replace('.', ',') + ' MB'
                : Math.max(1, Math.round(b / 1024)) + ' KB';
        }

        function sinkron() {
            const dt = new DataTransfer();
            berkas.forEach(f => dt.items.add(f));
            input.files = dt.files;

            list.innerHTML = '';
            berkas.forEach((f, i) => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm';

                const nama = document.createElement('span');
                nama.className = 'min-w-0 flex-1 truncate font-medium text-slate-800';
                nama.textContent = f.name;

                const info = document.createElement('span');
                info.className = 'shrink-0 text-xs text-slate-500';
                info.textContent = ukuran(f.size);

                const hapus = document.createElement('button');
                hapus.type = 'button';
                hapus.className = 'shrink-0 rounded-full p-1 text-slate-500 hover:bg-slate-100 hover:text-rose-600';
                hapus.setAttribute('aria-label', 'Hapus ' + f.name);
                hapus.innerHTML = '<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                hapus.addEventListener('click', () => { berkas.splice(i, 1); sinkron(); });

                li.append(nama, info, hapus);
                list.appendChild(li);
            });
        }

        input.addEventListener('change', function () {
            const pesan = [];
            Array.from(input.files).forEach(f => {
                const ext = f.name.split('.').pop().toLowerCase();
                if (!IZIN.includes(ext)) {
                    pesan.push(f.name + ': format tidak didukung.');
                } else if (f.size > MAKS) {
                    pesan.push(f.name + ': ukuran lebih dari 5 MB.');
                } else if (!berkas.some(b => b.name === f.name && b.size === f.size)) {
                    berkas.push(f);
                }
            });
            err.textContent = pesan.join(' ');
            err.classList.toggle('hidden', pesan.length === 0);
            sinkron();
        });
    })();

    // ---------- Klasifikasi & sub-klasifikasi ----------
    document.addEventListener('DOMContentLoaded', function () {
        const parentSelect = document.getElementById('parent_kategori_select');
        const hiddenKategoriId = document.getElementById('kategori_id');
        const keteranganText = document.getElementById('kategori_keterangan_text');
        const subWrapper = document.getElementById('subkategori_wrapper');
        const subContainer = document.getElementById('subkategori_options_container');
        const initialKategoriId = "{{ old('kategori_id') }}";

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

        function updateCategoryUI() {
            const opt = parentSelect.options[parentSelect.selectedIndex];

            if (!opt || !opt.value) {
                keteranganText.classList.add('hidden');
                subWrapper.classList.add('hidden');
                subContainer.innerHTML = '';
                hiddenKategoriId.value = '';
                return;
            }

            const ket = (opt.getAttribute('data-keterangan') || '').trim();
            keteranganText.textContent = ket;
            keteranganText.classList.toggle('hidden', ket === '');

            let children = [];
            try { children = JSON.parse(opt.getAttribute('data-children') || '[]'); } catch (e) {}

            if (children.length === 0) {
                subWrapper.classList.add('hidden');
                subContainer.innerHTML = '';
                hiddenKategoriId.value = opt.value;
                return;
            }

            subWrapper.classList.remove('hidden');
            subContainer.innerHTML = '';
            if (hiddenKategoriId.value === opt.value) hiddenKategoriId.value = '';

            children.forEach(child => {
                const label = document.createElement('label');
                label.className = 'flex cursor-pointer flex-col rounded-xl border border-slate-200 bg-white p-3 transition hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-500/20';
                label.innerHTML =
                    '<div class="flex items-center justify-between gap-2">' +
                        '<span class="text-sm font-semibold text-slate-800">' + esc(child.nama) + '</span>' +
                        '<input type="radio" name="_subkategori_radio" value="' + esc(child.id) + '" class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-600"' + (hiddenKategoriId.value == child.id ? ' checked' : '') + '>' +
                    '</div>' +
                    (child.keterangan ? '<p class="mt-1 text-xs leading-snug text-slate-500">' + esc(child.keterangan) + '</p>' : '');
                label.querySelector('input').addEventListener('change', function () {
                    hiddenKategoriId.value = this.value;
                });
                subContainer.appendChild(label);
            });
        }

        parentSelect.addEventListener('change', function () {
            hiddenKategoriId.value = '';
            updateCategoryUI();
        });

        // Pulihkan pilihan setelah validasi gagal
        if (initialKategoriId) {
            for (let i = 0; i < parentSelect.options.length; i++) {
                const opt = parentSelect.options[i];
                let children = [];
                try { children = JSON.parse(opt.getAttribute('data-children') || '[]'); } catch (e) {}
                if (opt.value == initialKategoriId || children.some(c => c.id == initialKategoriId)) {
                    parentSelect.selectedIndex = i;
                    hiddenKategoriId.value = initialKategoriId;
                    updateCategoryUI();
                    break;
                }
            }
        }

        // Cegah kirim jika klasifikasi belum lengkap
        const form = hiddenKategoriId.closest('form');
        form.addEventListener('submit', function (e) {
            if (!hiddenKategoriId.value) {
                e.preventDefault();
                if (!parentSelect.value) {
                    alert('Silakan pilih klasifikasi aduan.');
                    parentSelect.focus();
                } else {
                    alert('Pilih salah satu jenis yang tersedia pada klasifikasi ini.');
                    subWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
</script>
@endpush