@if ($pengajuan->perlu_konfirmasi)
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <h2 class="text-lg font-bold text-slate-900">Konfirmasi dari Anda</h2>
            @if ($pengajuan->batas_waktu_konfirmasi)
                <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800 border border-amber-200/60">
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batas: {{ $pengajuan->batas_waktu_konfirmasi->locale('id')->translatedFormat('d M Y, H.i') }}
                </span>
            @endif
        </div>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Jika kendala teratasi, konfirmasikan selesai. Jika belum, Anda dapat mengajukan keberatan agar aduan ditinjau kembali (maksimal 1 kali). Batas waktu konfirmasi adalah <strong>3 hari</strong> sebelum sistem mengonfirmasi secara otomatis.
        </p>

        <form action="{{ route('petugas.aduan.konfirmasi', $pengajuan) }}" method="POST" class="mt-4">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Konfirmasi Selesai
            </button>
        </form>

        @if ($pengajuan->bisa_ajukan_keberatan)
            <a href="{{ route('petugas.aduan.keberatan.form', $pengajuan) }}"
               class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-amber-400 hover:text-amber-800 hover:bg-amber-50/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Ajukan Keberatan
            </a>
            <p class="mt-2 text-center text-[11px] text-slate-400">
                * Pengajuan keberatan hanya dapat dilakukan maksimal 1 kali per pengaduan.
            </p>
        @endif

        <p class="mt-4 text-xs leading-relaxed text-slate-500">
            Aduan dinyatakan tuntas setelah Anda menyetujui penyelesaian.
        </p>
    </section>
@endif
