@props(['pengajuan'])

@php
    $p = $pengajuan;

    if ($p->status === 'selesai' && $p->sub_status === 'selesai_setelah_tinjau_ulang') {
        $label = 'Selesai setelah tinjau ulang';
        $kelas = 'bg-teal-100 text-teal-800 border border-teal-200/60';
        $dot = 'bg-teal-500';
    } elseif ($p->konfirmasi_pelapor === 'keberatan' && $p->status !== 'selesai') {
        $label = 'Ditinjau ulang';
        $kelas = 'bg-purple-100 text-purple-800 border border-purple-200/60';
        $dot = 'bg-purple-500';
    } elseif ($p->perlu_konfirmasi) {
        $label = 'Selesai (Menunggu Konfirmasi)';
        $kelas = 'bg-amber-100 text-amber-800 border border-amber-200/60';
        $dot = 'bg-amber-500';
    } elseif ($p->status === 'belum_verifikasi') {
        $label = 'Belum Diverifikasi';
        $kelas = 'bg-rose-100 text-rose-800 border border-rose-200/60';
        $dot = 'bg-rose-500 animate-pulse';
    } elseif (in_array($p->status, ['verifikasi', 'menunggu', 'diajukan'], true)) {
        $label = 'Verifikasi';
        $kelas = 'bg-amber-100 text-amber-800 border border-amber-200/60';
        $dot = 'bg-amber-500';
    } elseif (in_array($p->status, ['diproses', 'proses'], true)) {
        $subLabel = match ($p->sub_status) {
            'identifikasi_klarifikasi', 'identifikasi', 'klarifikasi' => 'Identifikasi / Klarifikasi',
            'rencana_aksi' => 'Rencana Aksi',
            'aksi' => 'Aksi / Tindakan',
            default => \App\Models\Pengajuan::SUB_STATUS_PROSES[$p->sub_status] ?? null,
        };
        $label = $subLabel ? 'Diproses ('.$subLabel.')' : 'Diproses';
        $kelas = 'bg-blue-100 text-blue-800 border border-blue-200/60';
        $dot = 'bg-blue-500';
    } elseif ($p->status === 'selesai' || $p->status === 'ditolak') {
        $label = 'Selesai';
        $kelas = 'bg-emerald-100 text-emerald-800 border border-emerald-200/60';
        $dot = 'bg-emerald-500';
    } else {
        $label = $p->status_label;
        $kelas = 'bg-slate-100 text-slate-700 border border-slate-200/60';
        $dot = 'bg-slate-400';
    }
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-2xs', $kelas]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }} shrink-0" aria-hidden="true"></span>
    <span>{{ $label }}</span>
</span>