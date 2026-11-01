@php
    use Illuminate\Support\Str;

    $tz = $p->created_at->format('T');

    // Kode tiket: KMP-YYYY-0001
    $kodeTiket = 'KMP-'.$p->created_at->format('Y').'-'.str_pad($p->id, 4, '0', STR_PAD_LEFT);

    // Waktu & Alasan Keberatan
    $riwayatKeberatan = $riwayat->last(
        fn ($r) => str_contains($r->catatan ?? '', 'Keberatan dari pelapor:')
    );

    $alasanKeberatan = null;
    if ($riwayatKeberatan) {
        $alasanKeberatan = trim(Str::after($riwayatKeberatan->catatan, 'Keberatan dari pelapor:'));
    }
    if (! $alasanKeberatan) {
        $tanggapanKeberatan = $p->tanggapans->first(
            fn ($t) => ($t->user?->role ?? '') === 'pegawai' || $t->admin_id === $p->user_id
        );
        $alasanKeberatan = $tanggapanKeberatan?->isi_tanggapan ?? 'Penyelesaian masalah belum sesuai dengan kondisi lapangan.';
    }

    $waktuKeberatan = $p->dikonfirmasi_pada
        ?? $riwayatKeberatan?->created_at
        ?? $p->updated_at;

    // Pemisahan lampiran: Lampiran keberatan vs Lampiran awal
    if ($waktuKeberatan) {
        $lampiranKeberatan = $p->lampirans->filter(
            fn ($l) => $l->created_at && $l->created_at->greaterThanOrEqualTo($waktuKeberatan->copy()->subMinutes(2))
        );
        $lampiranAwal = $p->lampirans->diff($lampiranKeberatan);
    } else {
        $lampiranKeberatan = collect();
        $lampiranAwal = $p->lampirans;
    }

    $isSelesaiSetelahTinjauUlang = ($p->status === 'selesai' && $p->sub_status === 'selesai_setelah_tinjau_ulang');

    // Langkah progres pengaduan
    if ($isSelesaiSetelahTinjauUlang) {
        $daftarLangkahKeberatan = [
            1 => 'Verifikasi',
            2 => 'Identifikasi / Klarifikasi (Analisis masalah)',
            3 => 'Rencana aksi (Penyusunan solusi)',
            4 => 'Aksi (Tindakan lapangan)',
            5 => 'Selesai',
            6 => 'Tinjau Ulang',
            7 => 'Selesai setelah tinjau ulang',
        ];
    } else {
        $daftarLangkahKeberatan = [
            1 => 'Verifikasi',
            2 => 'Identifikasi / Klarifikasi (Analisis masalah)',
            3 => 'Rencana aksi (Penyusunan solusi)',
            4 => 'Aksi (Tindakan lapangan)',
            5 => 'Selesai',
            6 => 'Tinjau Ulang',
        ];
    }

    // Riwayat langkah waktu terdekat
    $waktuPerLangkah = [];
    $waktuPerLangkah[1] = $waktuLangkah[1] ?? $p->created_at;
    $waktuPerLangkah[2] = $waktuLangkah[2] ?? $waktuPerLangkah[1];
    $waktuPerLangkah[3] = $waktuLangkah[3] ?? $waktuPerLangkah[2];
    $waktuPerLangkah[4] = $waktuLangkah[4] ?? $waktuPerLangkah[3];
    $waktuPerLangkah[5] = $waktuLangkah[5] ?? ($riwayat->first(fn ($r) => $r->status_baru === 'selesai')?->created_at ?? $waktuPerLangkah[4]);
    $waktuPerLangkah[6] = $waktuKeberatan;
    if ($isSelesaiSetelahTinjauUlang) {
        $waktuPerLangkah[7] = $p->tanggal_selesai ?? now();
    }

    // Kumpulan riwayat tanggapan (gabungan tanggapans + tanggapan keberatan pelapor)
    $listTanggapan = collect($p->tanggapans);
    $adaTanggapanPelapor = $listTanggapan->contains(
        fn ($t) => ($t->user?->role ?? '') === 'pegawai' || $t->admin_id === $p->user_id
    );
    if (! $adaTanggapanPelapor && $alasanKeberatan) {
        $listTanggapan->push((object) [
            'id' => 'keberatan-'.$p->id,
            'role' => 'pegawai',
            'nama' => $p->user?->nama ?? $p->user?->name ?? 'Pengadu',
            'unit' => $p->user?->unit?->nama_unit ?? 'Bagian Umum',
            'isi_tanggapan' => $alasanKeberatan,
            'created_at' => $waktuKeberatan,
        ]);
    }

    // Catatan selesai admin jika ada di riwayat dan belum ada di tanggapans
    if ($hasil && ! $listTanggapan->contains(fn ($t) => ($t->user?->role ?? '') === 'admin' && $t->created_at < $waktuKeberatan)) {
        $adminSelesai = $riwayat->first(fn ($r) => $r->status_baru === 'selesai' && filled($r->catatan));
        $waktuSelesai = $adminSelesai?->created_at ?? $waktuPerLangkah[6];
        $listTanggapan->push((object) [
            'id' => 'selesai-'.$p->id,
            'role' => 'admin',
            'nama' => $adminSelesai?->user?->nama ?? $adminSelesai?->user?->name ?? 'Admin',
            'unit' => $adminSelesai?->user?->unit?->nama_unit ?? 'Bagian Umum',
            'isi_tanggapan' => $hasil,
            'created_at' => $waktuSelesai,
        ]);
    }

    $listTanggapan = $listTanggapan->sortByDesc(fn ($t) => $t->created_at);
@endphp

<div class="max-w-2xl mx-auto space-y-4 pb-8">

    <!-- Header Informasi Aduan -->
    <div>
        <div>
            <p class="text-xs sm:text-sm font-semibold tracking-wide text-blue-600">
                {{ $kodeTiket }}
            </p>
            <h1 class="mt-1 text-2xl font-bold leading-tight text-slate-900">
                {{ $p->nama_kategori_lengkap }}
            </h1>
            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                <x-pengadu.status-chip :pengajuan="$p" />
                @if ($p->is_anonim)
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Anonim</span>
                @endif
            </div>
        </div>
        <p class="mt-2.5 text-xs sm:text-sm text-slate-400">
            Diajukan {{ $p->created_at->locale('id')->translatedFormat('d F Y · H.i') }} {{ $tz }}
        </p>
    </div>

    @if ($isSelesaiSetelahTinjauUlang)
        <!-- Banner Selesai Setelah Tinjau Ulang (Hijau/Emerald) -->
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3">
            <div class="mt-0.5 flex-shrink-0 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-bold text-emerald-900 text-sm">Selesai setelah tinjau ulang</p>
                <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                    Admin telah menyelesaikan tindak lanjut peninjauan ulang atas aduan ini pada {{ $p->tanggal_selesai ? $p->tanggal_selesai->locale('id')->translatedFormat('d F Y, H.i') : 'tanggal yang tercatat' }} {{ $tz }}.
                </p>
                <p class="text-xs text-emerald-700 mt-1 leading-relaxed">
                    Sesuai ketentuan, pengajuan keberatan hanya dapat dilakukan maksimal 1 kali. Apabila masih terdapat kendala, silakan ajukan pengaduan baru.
                </p>
                <div class="mt-3">
                    <a href="{{ route('pegawai.pengajuan.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Ajukan Pengaduan Baru</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Banner Keberatan Berhasil Dikirim (Ungu) -->
        <div class="rounded-2xl bg-[#f4effc] border border-[#ede5f8] p-4 flex items-start gap-3">
            <div class="mt-0.5 flex-shrink-0 text-[#7c3aed]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="font-bold text-[#6b21a8] text-sm">Keberatan berhasil dikirim</p>
                <p class="text-xs text-[#581c87] mt-1 leading-relaxed">
                    Terkirim {{ $waktuKeberatan->locale('id')->translatedFormat('d F Y, H.i') }} {{ $tz }}. Penyelesaian belum Anda terima; aduan kembali ditinjau oleh admin.
                </p>
            </div>
        </div>
    @endif

    <!-- Card 1: Alasan keberatan -->
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-slate-900">Alasan keberatan</h2>
        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $alasanKeberatan }}</p>

        @if ($lampiranKeberatan->isNotEmpty())
            <div class="pt-1">
                <p class="text-xs text-slate-400 font-medium">Lampiran keberatan</p>
                <div class="mt-1.5 space-y-2">
                    @foreach ($lampiranKeberatan as $lk)
                        @php
                            $urlLk = asset('storage/'.$lk->path);
                            $extLk = strtolower($lk->ekstensi ?: pathinfo($lk->nama_file, PATHINFO_EXTENSION));
                            $gambarLk = in_array($extLk, ['jpg', 'jpeg', 'png'], true);
                        @endphp
                        <div>
                            @if ($gambarLk)
                                <a href="{{ $urlLk }}" target="_blank" rel="noopener" class="block mb-2">
                                    <img src="{{ $urlLk }}" alt="{{ $lk->nama_file }}" loading="lazy" class="max-h-48 rounded-xl object-cover border border-slate-100">
                                </a>
                            @endif
                            <div class="flex items-center gap-1.5 text-sm">
                                <a href="{{ $urlLk }}" target="_blank" rel="noopener" class="font-semibold text-slate-900 hover:text-blue-600 hover:underline transition">
                                    {{ $lk->nama_file }}
                                </a>
                                @if ($lk->ukuran_file)
                                    <span class="text-xs text-slate-400">· {{ $ukuran($lk->ukuran_file) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <p class="text-xs text-slate-400 pt-1">
            Dikirim oleh {{ $p->user?->nama ?? $p->user?->name ?? 'Pengadu' }} · {{ $waktuKeberatan->locale('id')->translatedFormat('d M Y, H.i') }} {{ $tz }}
        </p>
    </section>

    <!-- Card 2: Rincian aduan awal -->
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm space-y-3">
        <h2 class="text-base font-bold text-slate-900">Rincian aduan awal</h2>
        <div>
            <p class="text-xs text-slate-400 font-medium">Klasifikasi</p>
            <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $p->nama_kategori_lengkap }}</p>
        </div>
        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $p->deskripsi ?? $p->isi_laporan }}</p>

        @if ($lampiranAwal->isNotEmpty())
            <div class="pt-1">
                <p class="text-xs text-slate-400 font-medium">Lampiran awal</p>
                <div class="mt-1.5 space-y-2">
                    @foreach ($lampiranAwal as $la)
                        @php
                            $urlLa = asset('storage/'.$la->path);
                            $extLa = strtolower($la->ekstensi ?: pathinfo($la->nama_file, PATHINFO_EXTENSION));
                            $gambarLa = in_array($extLa, ['jpg', 'jpeg', 'png'], true);
                        @endphp
                        <div>
                            @if ($gambarLa)
                                <a href="{{ $urlLa }}" target="_blank" rel="noopener" class="block mb-2">
                                    <img src="{{ $urlLa }}" alt="{{ $la->nama_file }}" loading="lazy" class="max-h-48 rounded-xl object-cover border border-slate-100">
                                </a>
                            @endif
                            <div class="flex items-center gap-1.5 text-sm">
                                <a href="{{ $urlLa }}" target="_blank" rel="noopener" class="font-semibold text-slate-900 hover:text-blue-600 hover:underline transition">
                                    {{ $la->nama_file }}
                                </a>
                                @if ($la->ukuran_file)
                                    <span class="text-xs text-slate-400">· {{ $ukuran($la->ukuran_file) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <!-- Card 3: Progres pengaduan -->
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm space-y-4">
        <h2 class="text-base font-bold text-slate-900">Progres pengaduan</h2>
        <ol class="space-y-4">
            @foreach ($daftarLangkahKeberatan as $no => $namaLangkah)
                @php
                    $waktuLangkahIni = $waktuPerLangkah[$no] ?? $p->created_at;
                @endphp
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 border border-blue-200" aria-hidden="true">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $namaLangkah }}</p>
                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ $waktuLangkahIni->locale('id')->translatedFormat('d M · H.i') }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    <!-- Card 4: Riwayat tanggapan -->
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm space-y-4">
        <h2 class="text-base font-bold text-slate-900">Riwayat tanggapan</h2>
        <div class="space-y-3">
            @forelse ($listTanggapan as $item)
                @php
                    $userItem = $item->user ?? null;
                    $isAdmin = ($item->role ?? ($userItem?->role ?? 'pegawai')) === 'admin';
                    $namaPengirim = $item->nama ?? ($userItem?->nama ?? $userItem?->name ?? 'Pengguna');
                    $unitPengirim = $item->unit ?? ($userItem?->unit?->nama_unit ?? 'Bagian Umum');
                    $roleLabel = $isAdmin ? 'Admin' : 'Pengadu';
                    $pesan = $item->isi_tanggapan ?? $item->pesan;
                    $waktu = $item->created_at ? $item->created_at->locale('id')->translatedFormat('d M Y · H.i') . ' ' . $tz : '-';
                @endphp
                <div @class([
                    'rounded-xl p-4',
                    'bg-blue-50/50 border border-blue-100' => $isAdmin,
                    'bg-[#f8f9fa] border border-gray-100' => ! $isAdmin,
                ])>
                    <div class="flex items-center gap-3">
                        <span @class([
                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                            'bg-blue-600 text-white' => $isAdmin,
                            'bg-[#e2e8f0] text-gray-700' => ! $isAdmin,
                        ]) aria-hidden="true">
                            {{ $inisial($namaPengirim) }}
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-slate-900">{{ $namaPengirim }}</p>
                            <p class="text-[11px] text-slate-500">{{ $roleLabel }} · {{ $unitPengirim }}</p>
                        </div>
                    </div>
                    <p class="mt-2.5 whitespace-pre-line text-xs leading-relaxed text-slate-800">{{ $pesan }}</p>
                    <p class="mt-2.5 text-[11px] text-slate-400">{{ $waktu }}</p>
                </div>
            @empty
                <p class="text-xs text-slate-500">Belum ada tanggapan.</p>
            @endforelse
        </div>
    </section>

    <!-- Footer Note -->
    <p class="pt-2 pb-6 text-center text-xs text-slate-400">
        Anda akan menerima notifikasi tindak lanjut berikutnya.
    </p>

</div>
