@extends('layouts.pengadu')

@section('title', 'Ajukan keberatan')
@section('back_url', route('pegawai.pengajuan.show', $pengajuan))
@section('back_title', 'Ajukan keberatan')

@section('content')
@php
    $p = $pengajuan;
    $riwayatSelesai = $p->riwayatStatuses->where('status_baru', 'selesai')->last();
    $tglSelesai = $p->tanggal_selesai ?? $riwayatSelesai?->created_at;
    $tz = $p->created_at->format('T') ?: 'WIB';
@endphp

<div class="max-w-2xl mx-auto space-y-5">

    <!-- Heading -->
    <div>
        <h1 class="text-[28px] font-bold leading-tight tracking-tight text-slate-900">Mari tinjau kembali</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Jelaskan bagian yang belum terselesaikan. Masukan Anda membantu kami memperbaiki tindak lanjut.
        </p>
    </div>

    <!-- Card: Aduan yang ditinjau -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Aduan yang ditinjau</p>
        <h2 class="text-lg font-bold leading-snug text-slate-900">
            {{ $p->deskripsi ?? $p->isi_laporan }}
        </h2>
        <div class="flex items-center gap-2 pt-0.5">
            <x-pengadu.status-chip :pengajuan="$p" />
        </div>
        <p class="text-xs text-slate-500 pt-0.5">
            Ditandai selesai oleh admin · {{ $tglSelesai ? $tglSelesai->locale('id')->translatedFormat('d M Y, H.i') . ' ' . $tz : 'Selesai' }}
        </p>
    </div>

    <!-- Peringatan Ketentuan 1 Kali Keberatan -->
    <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <div class="text-xs text-amber-900 leading-relaxed">
            <span class="font-bold">Ketentuan:</span> Pengajuan keberatan hanya dapat dilakukan <strong>maksimal 1 (satu) kali</strong> untuk setiap aduan. Jika setelah proses peninjauan ulang selesai kendala masih berlanjut, Anda dapat mengajukan aduan baru.
        </div>
    </div>

    <!-- Form Pengaduan Keberatan -->
    <form action="{{ route('pegawai.pengajuan.keberatan', $p) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Alasan keberatan -->
        <div>
            <label for="alasan" class="block text-sm font-bold text-slate-900">
                Alasan keberatan <span class="text-rose-500">*</span>
            </label>
            <textarea id="alasan" name="alasan" rows="5" required minlength="10" maxlength="1000"
                      placeholder="AC kembali tidak dingin setelah digunakan sekitar 30 menit pada rapat pagi ini. Mohon dilakukan pemeriksaan lanjutan, termasuk pengecekan kebocoran refrigeran."
                      class="mt-2 block w-full rounded-2xl border border-slate-300 p-4 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 leading-relaxed">{{ old('alasan') }}</textarea>
            <p class="mt-1.5 text-xs text-slate-500">Sampaikan kondisi terkini dan tindak lanjut yang diharapkan.</p>
            @error('alasan')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Lampiran baru (opsional) -->
        <div>
            <label class="block text-sm font-bold text-slate-900">
                Lampiran baru (opsional)
            </label>

            <div id="dropzone"
                 class="mt-2 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-blue-200/80 bg-blue-50/20 p-6 text-center cursor-pointer transition hover:border-blue-400 hover:bg-blue-50/40"
                 onclick="document.getElementById('lampiran-input').click()">
                <input type="file" id="lampiran-input" name="lampiran[]" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFileSelect(event)">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <p class="mt-2 text-sm font-bold text-blue-700">Tambah lampiran pendukung</p>
                <p class="mt-0.5 text-xs text-slate-500">JPG, PNG, PDF · Maks. 5 MB per berkas</p>
            </div>

            <!-- List Preview Berkas Terpilih -->
            <div id="preview-list" class="mt-3 space-y-2"></div>

            @error('lampiran')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
            @error('lampiran.*')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Callout box status info -->
        <div class="flex items-start gap-3.5 rounded-2xl bg-[#f4effc] border border-purple-100 p-4">
            <svg class="h-5 w-5 shrink-0 text-purple-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <div>
                <p class="text-sm font-bold text-purple-900">Aduan akan berstatus Ditinjau ulang</p>
                <p class="mt-1 text-xs leading-relaxed text-purple-900/80">
                    Penyelesaian belum diterima. Admin akan meninjau alasan dan lampiran Anda, lalu memberikan tanggapan lanjutan.
                </p>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="space-y-3 pt-2">
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
                Kirim keberatan
            </button>

            <a href="{{ route('pegawai.pengajuan.show', $p) }}"
               class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300">
                Batalkan
            </a>
        </div>

        <p class="pt-1 text-center text-xs text-slate-500">
            Keberatan dan lampiran disimpan dalam riwayat aduan yang sama.
        </p>
    </form>
</div>

@push('scripts')
<script>
    let dt = new DataTransfer();

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        const val = (bytes / Math.pow(k, i)).toFixed(i > 1 ? 1 : 0);
        return val.replace('.', ',') + ' ' + sizes[i];
    }

    function handleFileSelect(event) {
        const input = event.target;
        if (!input.files || input.files.length === 0) return;

        for (let i = 0; i < input.files.length; i++) {
            dt.items.add(input.files[i]);
        }
        input.files = dt.files;
        renderPreview();
    }

    function removeFile(index) {
        dt = new DataTransfer();
        const input = document.getElementById('lampiran-input');
        const files = input.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                dt.items.add(files[i]);
            }
        }
        input.files = dt.files;
        renderPreview();
    }

    function renderPreview() {
        const previewList = document.getElementById('preview-list');
        previewList.innerHTML = '';
        const input = document.getElementById('lampiran-input');
        const files = input.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-700 shadow-2xs';
            div.innerHTML = `
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="h-5 w-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span class="truncate font-medium text-slate-900">${file.name}</span>
                    <span class="text-slate-400 shrink-0">· ${formatBytes(file.size)}</span>
                </div>
                <button type="button" onclick="removeFile(${i})" class="text-slate-400 hover:text-rose-600 transition p-1" aria-label="Hapus berkas">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            previewList.appendChild(div);
        }
    }
</script>
@endpush
@endsection
