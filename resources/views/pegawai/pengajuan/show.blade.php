@extends('layouts.pengadu')

@section('title', 'Detail pengaduan')
@section('back_url', route('pegawai.pengajuan.index'))
@section('back_title', 'Detail pengaduan')

@section('content')
@php
    use Illuminate\Support\Str;

    $p = $pengajuan;
    $kartu = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm';
    $judulKartu = 'text-lg font-bold text-slate-900';
    $tz = $p->created_at->format('T');

    $selesai = in_array($p->status, ['selesai', 'ditolak'], true);

    // Langkah aktif pada daftar progres (1-5)
    if (in_array($p->status, ['menunggu', 'diajukan', 'verifikasi'], true)) {
        $langkah = 1;
    } elseif (in_array($p->status, ['diproses', 'proses'], true)) {
        $langkah = match ($p->sub_status) {
            'rencana_aksi' => 3,
            'aksi' => 4,
            default => 2,
        };
    } elseif ($selesai) {
        $langkah = 5;
    } else {
        $langkah = 1;
    }

    $daftarLangkah = [
        1 => 'Verifikasi',
        2 => 'Identifikasi / Klarifikasi (Analisis masalah)',
        3 => 'Rencana aksi (Penyusunan solusi)',
        4 => 'Aksi (Tindakan lapangan)',
        5 => 'Selesai',
    ];

    // Waktu tiap langkah diambil dari riwayat status
    $nomorLangkah = function (?string $st, ?string $sub): ?int {
        if (in_array($st, ['menunggu', 'diajukan', 'verifikasi'], true)) return 1;
        if (in_array($st, ['diproses', 'proses'], true)) {
            return match ($sub) { 'rencana_aksi' => 3, 'aksi' => 4, default => 2 };
        }
        if (in_array($st, ['selesai', 'ditolak'], true)) return 5;
        return null;
    };

    $riwayat = $p->riwayatStatuses->sortBy('created_at');
    $waktuLangkah = [];
    foreach ($riwayat as $r) {
        if ($r->status_sebelumnya === 'selesai' && $r->status_baru === 'selesai') continue; // konfirmasi pelapor
        $n = $nomorLangkah($r->status_baru, $r->sub_status_baru);
        if ($n) $waktuLangkah[$n] = $r->created_at;
    }

    // Catatan admin saat menandai selesai
    $hasil = $riwayat->sortByDesc('created_at')->first(
        fn ($r) => $r->status_baru === 'selesai' && $r->status_sebelumnya !== 'selesai' && filled($r->catatan)
    )?->catatan;

    // Banner status (kelas ditulis utuh agar tidak terbuang oleh purge Tailwind)
    $tglSelesai = $p->tanggal_selesai?->locale('id')->translatedFormat('d F Y');
    if ($p->perlu_konfirmasi) {
        $banner = ['bg-amber-50 border-amber-200', 'text-amber-700', 'text-amber-800', 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z',
            'Sudah ditangani, apakah sudah sesuai?',
            'Admin menandai aduan selesai pada '.($tglSelesai ?? 'tanggal yang tercatat').'. Penyelesaian belum Anda konfirmasi. Periksa hasilnya sebelum memilih tindakan.'];
    } elseif ($selesai) {
        $banner = ['bg-emerald-50 border-emerald-200', 'text-emerald-700', 'text-emerald-800', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            $p->sub_status === 'selesai_setelah_tinjau_ulang' ? 'Selesai setelah tinjau ulang' : 'Aduan sudah selesai ditangani',
            $p->sub_status === 'selesai_setelah_tinjau_ulang'
                ? 'Admin telah menyelesaikan penanganan setelah proses tinjau ulang.'
                : ($p->konfirmasi_pelapor === 'dikonfirmasi' && $p->dikonfirmasi_pada
                    ? 'Anda mengonfirmasi penyelesaian pada '.$p->dikonfirmasi_pada->locale('id')->translatedFormat('d F Y').'.'
                    : ($p->konfirmasi_pelapor === 'otomatis' && $p->dikonfirmasi_pada
                        ? 'Dikonfirmasi selesai secara otomatis oleh sistem pada '.$p->dikonfirmasi_pada->locale('id')->translatedFormat('d F Y').' (melewati batas waktu 3 hari).'
                        : null))];
    } elseif ($p->status === 'belum_verifikasi') {
        $banner = ['bg-rose-50 border-rose-200', 'text-rose-700', 'text-rose-800', 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
            'Menunggu Verifikasi Admin', 'Aduan Anda telah diajukan dan belum dibuka/diverifikasi oleh tim admin.'];
    } elseif ($langkah === 1) {
        $banner = ['bg-amber-50 border-amber-200', 'text-amber-700', 'text-amber-800', 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
            'Aduan Anda sedang diverifikasi', 'Admin telah membuka dan sedang memeriksa kelengkapan aduan sebelum diproses.'];
    } else {
        $banner = ['bg-blue-50 border-blue-100', 'text-blue-700', 'text-blue-800', 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
            'Aduan Anda sedang ditangani', null];
    }

    $ukuran = fn ($b) => $b >= 1048576
        ? number_format($b / 1048576, 1, ',', '.').' MB'
        : max(1, round($b / 1024)).' KB';

    $inisial = fn ($nama) => Str::of($nama ?? 'A')->trim()->explode(' ')->filter()->take(2)
        ->map(fn ($k) => Str::upper(Str::substr($k, 0, 1)))->implode('');

    $isDitinjauUlang = ($p->konfirmasi_pelapor === 'keberatan')
        || ($p->sub_status === 'selesai_setelah_tinjau_ulang')
        || (($p->jumlah_keberatan ?? 0) > 0);
@endphp

@if ($isDitinjauUlang)
    @include('pegawai.pengajuan.partials.detail-ditinjau-ulang')
@else
<div class="max-w-2xl mx-auto space-y-4">

    <div>
        <div>
            <h1 class="text-2xl font-bold leading-snug text-slate-900">{{ $p->kategori?->nama_kategori ?? 'Pengaduan #'.$p->id }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <x-pengadu.status-chip :pengajuan="$p" />
                @if ($p->is_anonim)
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Anonim</span>
                @endif
            </div>
        </div>
        <p class="mt-3 text-sm text-slate-500">
            Diajukan {{ $p->created_at->locale('id')->translatedFormat('d F Y · H.i') }} {{ $tz }} · #{{ $p->id }}
        </p>
    </div>

    <!-- Banner status -->
    <div class="flex gap-3 rounded-2xl border p-4 {{ $banner[0] }}">
        <svg class="h-6 w-6 shrink-0 {{ $banner[1] }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $banner[3] }}" />
        </svg>
        <div>
            <p class="text-sm font-bold {{ $banner[2] }}">{{ $banner[4] }}</p>
            @if ($banner[5])
                <p class="mt-1 text-[13px] leading-relaxed text-slate-700">{{ $banner[5] }}</p>
            @endif
        </div>
    </div>

    <!-- Hasil tindak lanjut -->
    @if ($selesai && $hasil)
        <section class="{{ $kartu }}">
            <h2 class="{{ $judulKartu }}">Hasil tindak lanjut</h2>
            <p class="mt-3 whitespace-pre-line text-[15px] leading-relaxed text-slate-800">{{ $hasil }}</p>
        </section>
    @endif

    <!-- Konfirmasi dari pelapor -->
    @include('pegawai.pengajuan.partials.konfirmasi')

    <!-- Rincian -->
    <section class="{{ $kartu }}">
        <h2 class="{{ $judulKartu }}">Rincian aduan</h2>
        <dl class="mt-4 space-y-4">
            <div>
                <dt class="text-xs text-slate-500">Klasifikasi</dt>
                <dd class="mt-0.5 text-[15px] font-semibold text-slate-900">{{ $p->nama_kategori_lengkap }}</dd>
            </div>
            @if ($p->assignedUnit)
                <div>
                    <dt class="text-xs text-slate-500">Bidang Penanganan</dt>
                    <dd class="mt-0.5 text-[15px] font-semibold text-slate-900">
                        🏢 {{ $p->assignedUnit->nama_unit }}
                    </dd>
                </div>
            @endif
            <div>
                <dt class="text-xs text-slate-500">Pengadu</dt>
                <dd class="mt-0.5 text-[15px] font-semibold text-slate-900">
                    {{ $p->user?->name ?? '-' }}{{ $p->user?->unit ? ' · '.$p->user->unit->nama_unit : '' }}
                </dd>
                @if ($p->is_anonim)
                    <dd class="mt-1 text-xs text-slate-500">Dikirim sebagai anonim. Nama Anda disembunyikan dari tampilan admin.</dd>
                @endif
            </div>
            <div>
                <dt class="sr-only">Uraian</dt>
                <dd class="whitespace-pre-line text-[15px] leading-relaxed text-slate-800">{{ $p->deskripsi ?? $p->isi_laporan }}</dd>
            </div>
            @if ($p->saran_penyelesaian)
                <div>
                    <dt class="text-xs text-slate-500">Saran / rekomendasi solusi</dt>
                    <dd class="mt-0.5 whitespace-pre-line text-[15px] font-semibold text-slate-900">{{ $p->saran_penyelesaian }}</dd>
                </div>
            @endif
        </dl>
    </section>

    <!-- Lampiran -->
    @if ($p->lampirans->isNotEmpty())
        <section class="{{ $kartu }}">
            <h2 class="{{ $judulKartu }}">Lampiran · {{ $p->lampirans->count() }} berkas</h2>
            <div class="mt-4 space-y-4">
                @foreach ($p->lampirans as $l)
                    @php
                        $url = asset('storage/'.$l->path);
                        $ext = strtolower($l->ekstensi ?: pathinfo($l->nama_file, PATHINFO_EXTENSION));
                        $gambar = in_array($ext, ['jpg', 'jpeg', 'png'], true);
                    @endphp
                    <div>
                        @if ($gambar)
                            <a href="{{ $url }}" target="_blank" rel="noopener">
                                <img src="{{ $url }}" alt="{{ $l->nama_file }}" loading="lazy" class="h-48 w-full rounded-xl object-cover">
                            </a>
                        @endif
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="mt-2 flex items-center gap-2.5 text-sm hover:text-blue-600">
                            <svg class="h-5 w-5 shrink-0 text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                @if ($gambar)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                @endif
                            </svg>
                            <span class="min-w-0 flex-1 truncate font-medium text-slate-800">{{ $l->nama_file }}</span>
                            @if ($l->ukuran_file)
                                <span class="shrink-0 text-xs text-slate-500">{{ $ukuran($l->ukuran_file) }}</span>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Progres -->
    <section class="{{ $kartu }}">
        <h2 class="{{ $judulKartu }}">Progres pengaduan</h2>
        <ol class="mt-4 space-y-4">
            @foreach ($daftarLangkah as $no => $nama)
                @php $tercapai = $no <= $langkah; @endphp
                <li class="flex items-start gap-3">
                    <span @class([
                        'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full',
                        'bg-blue-50 text-blue-700 border border-blue-200' => $tercapai,
                        'border-2 border-slate-300 bg-white' => ! $tercapai,
                    ])>
                        @if ($tercapai)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @endif
                    </span>
                    <div>
                        <p @class(['text-sm font-bold', 'text-slate-900' => $tercapai, 'text-slate-500' => ! $tercapai])>{{ $nama }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">
                            @if ($tercapai)
                                {{ isset($waktuLangkah[$no]) ? $waktuLangkah[$no]->locale('id')->translatedFormat('d M · H.i') : '-' }}
                            @else
                                Menunggu tindak lanjut
                            @endif
                        </p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    <!-- Tanggapan admin -->
    <section class="{{ $kartu }}">
        <h2 class="{{ $judulKartu }}">Tanggapan admin</h2>

        @forelse ($p->tanggapans as $t)
            @php $namaAdmin = $t->user?->name ?? 'Tim Administrator'; @endphp
            <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200/80 p-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white" aria-hidden="true">{{ $inisial($namaAdmin) }}</span>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $namaAdmin }}</p>
                        <p class="text-xs text-slate-500">{{ ($t->user && $t->user->isPetugas()) ? 'Petugas Bidang' : 'Admin' }}{{ $t->user?->unit ? ' · '.$t->user->unit->nama_unit : '' }}</p>
                    </div>
                </div>
                <p class="mt-3 whitespace-pre-line text-[15px] leading-relaxed text-slate-800">{{ $t->isi_tanggapan ?? $t->pesan }}</p>
                <p class="mt-3 text-xs text-slate-500">{{ $t->created_at->locale('id')->translatedFormat('d M Y · H.i') }} {{ $tz }}</p>
            </div>
        @empty
            <p class="mt-3 text-sm text-slate-500">Belum ada tanggapan dari admin.</p>
        @endforelse

        <p class="mt-4 text-xs leading-relaxed text-slate-500">Notifikasi dikirim saat ada perubahan status atau tanggapan baru.</p>
    </section>

    <p class="pt-1 text-center text-xs text-slate-500">
        Terakhir diperbarui {{ $p->updated_at->locale('id')->translatedFormat('d F Y · H.i') }} {{ $tz }}
    </p>
</div>
@endif
@endsection