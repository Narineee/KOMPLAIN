@props(['pengajuan'])

@php $p = $pengajuan; @endphp

<a href="{{ route('pegawai.pengajuan.show', $p) }}"
   class="group block rounded-2xl border border-slate-200/90 bg-white p-4.5 sm:p-5 shadow-xs transition hover:border-blue-400 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
    <div class="flex items-center justify-between text-xs text-slate-500">
        <span>{{ $p->created_at->locale('id')->translatedFormat('d M Y') }}</span>
        <span class="font-mono text-slate-400">#{{ $p->id }}</span>
    </div>

    <div class="mt-2">
        <h3 class="text-[15px] font-semibold leading-snug text-slate-900 group-hover:text-blue-600 transition">
            {{ $p->judul ?: 'Pengaduan #'.$p->id }}
        </h3>
    </div>

    <p class="mt-1 text-xs text-slate-500">{{ $p->nama_kategori_lengkap }}</p>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <x-pengadu.status-chip :pengajuan="$p" />
        @if ($p->is_anonim)
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Anonim</span>
        @endif
    </div>
</a>