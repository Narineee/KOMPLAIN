@extends('layouts.petugas')

@section('title', 'Buat Aduan')
@section('back_url', route('petugas.aduan.index'))
@section('back_title', 'Aduan Saya')

@section('content')
<div class="max-w-2xl mx-auto">

    <h1 class="text-[28px] font-bold leading-tight tracking-tight text-slate-900">Sampaikan aduan Anda</h1>
    <p class="mt-2 text-[15px] leading-relaxed text-slate-500">
        Ceritakan kendala secara jelas agar kami dapat membantu dengan tepat.
    </p>

    <!-- Info privasi -->
    <div class="mt-5 flex gap-3 rounded-2xl bg-amber-50/80 border border-amber-200 p-4">
        <svg class="w-6 h-6 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        <div>
            <p class="text-sm font-bold text-amber-900">Aduan ditangani secara internal</p>
            <p class="mt-0.5 text-[13px] leading-relaxed text-slate-700">
                Identitas dan lampiran hanya dapat diakses oleh Anda dan pihak yang berwenang menangani aduan.
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

    <form action="{{ route('petugas.aduan.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
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
                        <p class="mt-1 text-xs leading-snug opacity-70">Laporan terhubung dengan profil akun Anda dan dapat dipantau dari panel Aduan Saya.</p>
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
                        <p class="mt-1 text-xs leading-snug opacity-70">Nama dan identitas Anda disembunyikan. Aduan tetap dapat Anda pantau dari panel Aduan Saya.</p>
                    </div>
                </label>
            </div>
        </fieldset>

        @php
            $oldKategoriId = old('kategori_id');
            $selectedParentId = null;
            if ($oldKategoriId) {
                $selectedKat = \App\Models\Kategori::find($oldKategoriId);
                $selectedParentId = $selectedKat?->parent_id ?? $selectedKat?->id;
            }
        @endphp

        <!-- Klasifikasi -->
        <div>
            <label for="parent_kategori_select" class="block text-sm font-bold text-slate-900">
                Klasifikasi <span class="text-rose-500">*</span>
            </label>
            <div class="relative mt-2 flex items-center">
                <select id="parent_kategori_select"
                        class="block w-full appearance-none rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-10 text-sm text-slate-800 shadow-xs focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                    <option value="">Pilih klasifikasi</option>
                    @foreach ($parentKategoris as $kategori)
                        <option value="{{ $kategori->id }}"
                                @selected($selectedParentId == $kategori->id)
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
            <div id="subkategori_wrapper" class="mt-3 hidden rounded-2xl border border-amber-200 bg-amber-50/40 p-4">
                <p class="text-sm font-bold text-amber-950 flex items-center gap-1.5">
                    <span>📌</span>
                    <span>Pilih jenis spesifik yang paling sesuai: <span class="text-rose-500">*</span></span>
                </p>
                <div id="subkategori_options_container" class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-2"></div>
            </div>

            <p id="klasifikasi_error_text" class="mt-1.5 hidden text-xs font-semibold text-rose-600"></p>

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
                      class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-slate-800 placeholder-slate-400 focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">{{ old('isi_laporan') }}</textarea>
            <p class="mt-1.5 text-xs text-slate-500">Jelaskan kejadian, waktu, dan dampak yang dialami.</p>
        </div>

        <!-- Saran -->
        <div>
            <label for="saran_penyelesaian" class="block text-sm font-bold text-slate-900">Saran / rekomendasi solusi</label>
            <textarea name="saran_penyelesaian" id="saran_penyelesaian" rows="2"
                      class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20">{{ old('saran_penyelesaian') }}</textarea>
        </div>

        <!-- Lampiran -->
        <div>
            <span class="block text-sm font-bold text-slate-900">Lampiran pendukung</span>
            <label for="lampiran"
                   class="mt-2 flex cursor-pointer flex-col items-center rounded-2xl border-2 border-dashed border-slate-300 bg-white px-6 py-8 text-center transition hover:border-amber-400 focus-within:border-amber-600">
                <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <span class="mt-2 text-sm font-semibold text-amber-700">Pilih foto atau dokumen</span>
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
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
            </svg>
            Kirim Pengaduan
        </button>
    </form>
</div>
@endsection

@push('head')
<style>
    .pilih-kartu, .pilih-ikon, .pilih-cek { transition: background-color .15s, border-color .15s, color .15s; }
    .pilih-mode:hover .pilih-kartu { border-color: #f59e0b; }
    .pilih-mode input:checked + .pilih-kartu { background: #78350f; border-color: #78350f; color: #fff; }
    .pilih-mode input:checked + .pilih-kartu .pilih-ikon { background: rgba(255,255,255,.15); color: #fff; }
    .pilih-mode input:checked + .pilih-kartu .pilih-cek { background: #d97706; border-color: #d97706; color: #fff; }
    .pilih-mode input:focus-visible + .pilih-kartu { outline: 2px solid #d97706; outline-offset: 2px; }
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
    (function () {
        function initKlasifikasi() {
            const parentSelect = document.getElementById('parent_kategori_select');
            const hiddenKategoriId = document.getElementById('kategori_id');
            const keteranganText = document.getElementById('kategori_keterangan_text');
            const subWrapper = document.getElementById('subkategori_wrapper');
            const subContainer = document.getElementById('subkategori_options_container');
            const errorText = document.getElementById('klasifikasi_error_text');
            const initialKategoriId = "{{ old('kategori_id') }}";

            if (!parentSelect || !hiddenKategoriId) return;

            const form = hiddenKategoriId.closest('form');

            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

            function clearError() {
                if (errorText) {
                    errorText.classList.add('hidden');
                    errorText.textContent = '';
                }
                parentSelect.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/20');
                if (subWrapper) {
                    subWrapper.classList.remove('border-rose-400', 'ring-2', 'ring-rose-500/20');
                }
            }

            function updateCategoryUI(targetChildId = null) {
                clearError();
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
                try {
                    children = JSON.parse(opt.getAttribute('data-children') || '[]');
                } catch (e) {
                    children = [];
                }

                // Jika kategori ini tidak memiliki anak/sub-klasifikasi (misal: Kebersihan, Keamanan, Kode Etik Pegawai)
                if (children.length === 0) {
                    subWrapper.classList.add('hidden');
                    subContainer.innerHTML = '';
                    hiddenKategoriId.value = opt.value;
                    return;
                }

                // Jika memiliki anak/sub-klasifikasi (misal: Kendaraan, Sarana/Pra Sarana)
                subWrapper.classList.remove('hidden');
                subContainer.innerHTML = '';

                // Tentukan child id yang terpilih jika ada
                const activeId = targetChildId || (hiddenKategoriId.value !== opt.value ? hiddenKategoriId.value : null);
                if (!activeId) {
                    hiddenKategoriId.value = '';
                }

                children.forEach(child => {
                    const isChecked = String(activeId) === String(child.id);
                    const label = document.createElement('label');
                    label.className = 'sub-card flex cursor-pointer items-center justify-between rounded-xl border p-3.5 transition ' +
                        (isChecked
                            ? 'border-amber-600 bg-amber-50 text-amber-900 ring-2 ring-amber-500/20'
                            : 'border-slate-200 bg-white text-slate-800 hover:border-amber-400');

                    label.innerHTML =
                        '<div class="min-w-0 flex-1 pr-2">' +
                            '<span class="text-sm font-bold block">' + esc(child.nama) + '</span>' +
                            (child.keterangan ? '<p class="mt-0.5 text-xs text-slate-500 leading-snug">' + esc(child.keterangan) + '</p>' : '') +
                        '</div>' +
                        '<input type="radio" name="_subkategori_radio" value="' + esc(child.id) + '" class="h-4 w-4 border-slate-300 text-amber-600 focus:ring-amber-600 shrink-0"' + (isChecked ? ' checked' : '') + '>';

                    const radioInput = label.querySelector('input');

                    function selectThisChild() {
                        clearError();
                        hiddenKategoriId.value = String(child.id);
                        radioInput.checked = true;

                        // Perbarui styling visual semua kartu
                        subContainer.querySelectorAll('.sub-card').forEach(c => {
                            c.classList.remove('border-amber-600', 'bg-amber-50', 'text-amber-900', 'ring-2', 'ring-amber-500/20');
                            c.classList.add('border-slate-200', 'bg-white', 'text-slate-800');
                        });
                        label.classList.remove('border-slate-200', 'bg-white', 'text-slate-800');
                        label.classList.add('border-amber-600', 'bg-amber-50', 'text-amber-900', 'ring-2', 'ring-amber-500/20');
                    }

                    label.addEventListener('click', function (e) {
                        selectThisChild();
                    });

                    radioInput.addEventListener('change', function (e) {
                        selectThisChild();
                    });

                    subContainer.appendChild(label);
                });
            }

            parentSelect.addEventListener('change', function () {
                hiddenKategoriId.value = '';
                updateCategoryUI();
            });

            // Pulihkan pilihan awal jika ada (setelah validasi gagal)
            if (initialKategoriId) {
                for (let i = 0; i < parentSelect.options.length; i++) {
                    const opt = parentSelect.options[i];
                    if (!opt.value) continue;

                    let children = [];
                    try { children = JSON.parse(opt.getAttribute('data-children') || '[]'); } catch (e) {}

                    if (String(opt.value) === String(initialKategoriId)) {
                        parentSelect.selectedIndex = i;
                        hiddenKategoriId.value = initialKategoriId;
                        updateCategoryUI();
                        break;
                    } else if (children.some(c => String(c.id) === String(initialKategoriId))) {
                        parentSelect.selectedIndex = i;
                        hiddenKategoriId.value = initialKategoriId;
                        updateCategoryUI(initialKategoriId);
                        break;
                    }
                }
            } else if (parentSelect.value) {
                updateCategoryUI();
            }

            // Cegah pengiriman formulir jika klasifikasi belum dipilih
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!hiddenKategoriId.value) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (!parentSelect.value) {
                            if (errorText) {
                                errorText.textContent = 'Silakan pilih klasifikasi aduan terlebih dahulu.';
                                errorText.classList.remove('hidden');
                            }
                            parentSelect.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/20');
                            parentSelect.focus();
                        } else {
                            if (errorText) {
                                errorText.textContent = 'Klasifikasi ini memerlukan pilihan jenis spesifik. Silakan klik salah satu opsi jenis di bawah.';
                                errorText.classList.remove('hidden');
                            }
                            if (subWrapper) {
                                subWrapper.classList.add('border-rose-400', 'ring-2', 'ring-rose-500/20');
                                subWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }

                        return false;
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initKlasifikasi);
        } else {
            initKlasifikasi();
        }
    })();
</script>
@endpush
