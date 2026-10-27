@if ($pengajuan->perlu_konfirmasi)
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Konfirmasi dari Anda</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Jika kendala teratasi, konfirmasikan selesai. Jika belum, Anda dapat mengajukan keberatan agar aduan ditinjau kembali (maksimal 1 kali).
        </p>

        <form action="{{ route('pegawai.pengajuan.konfirmasi', $pengajuan) }}" method="POST" class="mt-4">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Konfirmasi selesai
            </button>
        </form>

        @if ($pengajuan->bisa_ajukan_keberatan)
            <a href="{{ route('pegawai.pengajuan.keberatan.form', $pengajuan) }}"
               class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Ajukan keberatan
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
