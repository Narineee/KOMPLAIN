@if ($pengajuan->perlu_konfirmasi)
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Konfirmasi dari Anda</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">
            Jika kendala teratasi, konfirmasikan selesai. Jika belum, ajukan keberatan agar aduan ditinjau kembali (maksimal 1 kali).
        </p>

        <form action="{{ route('pegawai.pengajuan.konfirmasi', $pengajuan) }}" method="POST" class="mt-4">
            @csrf
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-teal-700 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-teal-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Konfirmasi selesai
            </button>
        </form>

        @if ($pengajuan->bisa_ajukan_keberatan)
            <a href="{{ route('pegawai.pengajuan.keberatan.form', $pengajuan) }}"
               class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-teal-700 transition hover:border-teal-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
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
