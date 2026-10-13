<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar laporan milik pegawai yang sedang login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $pengajuans = Pengajuan::with(['kategori'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('pegawai.pengajuan.index', [
            'pengajuans' => $pengajuans,
        ]);
    }

    /**
     * Menampilkan formulir buat pengajuan laporan.
     */
    public function create(): View
    {
        $kategoris = Kategori::where('aktif', true)->orderBy('nama_kategori')->get();

        return view('pegawai.pengajuan.create', [
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Menyimpan laporan baru ke database.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isAnonim = $request->boolean('is_anonim', false);
        $user = $request->user();

        // Generate nomor tiket unik
        $nomorTiket = $this->generateNomorTiket();

        $pengajuan = DB::transaction(function () use ($validated, $isAnonim, $user, $nomorTiket, $request) {
            $pengajuan = Pengajuan::create([
                'nomor_tiket' => $nomorTiket,
                'user_id' => $user->id,
                'kategori_id' => $validated['kategori_id'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['isi_laporan'],
                'isi_laporan' => $validated['isi_laporan'],
                'saran_penyelesaian' => $validated['saran_penyelesaian'] ?? null,
                'metode_identitas' => $isAnonim ? 'anonim' : 'identitas',
                'status' => 'menunggu',
                'pernyataan_disetujui' => true,
                'waktu_persetujuan' => now(),
            ]);

            // Catat riwayat status awal
            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => $user->id,
                'status_sebelumnya' => null,
                'status_baru' => 'menunggu',
                'catatan' => 'Laporan berhasil dibuat dan diajukan ke sistem.',
            ]);

            // Simpan lampiran jika ada
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('lampirans', 'public');

                        Lampiran::create([
                            'pengajuan_id' => $pengajuan->id,
                            'nama_file' => $file->getClientOriginalName(),
                            'path_file' => $path,
                            'path' => $path,
                            'ekstensi' => $file->getClientOriginalExtension(),
                            'ukuran_bytes' => $file->getSize(),
                            'ukuran_file' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                    }
                }
            }

            // Notifikasi untuk pengguna
            Notifikasi::create([
                'user_id' => $user->id,
                'pengajuan_id' => $pengajuan->id,
                'judul' => 'Laporan Berhasil Diajukan',
                'pesan' => "Laporan dengan tiket {$nomorTiket} telah berhasil diajukan dan sedang menunggu tindak lanjut admin.",
            ]);

            return $pengajuan;
        });

        return redirect()->route('pegawai.dashboard')->with('status', "Pengaduan Anda ({$nomorTiket}) berhasil diajukan!");
    }

    /**
     * Menampilkan halaman sukses setelah membuat pengajuan.
     */
    public function sukses(): View|RedirectResponse
    {
        $tiketSukses = session('tiket_sukses');

        if (! $tiketSukses) {
            return redirect()->route('pegawai.dashboard');
        }

        return view('pegawai.pengajuan.sukses', [
            'tiketSukses' => $tiketSukses,
        ]);
    }

    /**
     * Menampilkan detail laporan milik pegawai (non-anonim).
     */
    public function show(Pengajuan $pengajuan): View
    {
        // Otorisasi: hanya pemilik laporan yang dapat mengakses
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat rincian laporan ini.');
        }

        $pengajuan->load([
            'kategori',
            'lampirans',
            'riwayatStatuses.user',
            'tanggapans' => fn ($query) => $query->with('user')->latest(),
        ]);

        return view('pegawai.pengajuan.show', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Menghasilkan nomor tiket unik format TKT-YYYYMMDD-XXXXX.
     */
    private function generateNomorTiket(): string
    {
        do {
            $nomorTiket = 'TKT-'.date('Ymd').'-'.strtoupper(Str::random(5));
        } while (Pengajuan::where('nomor_tiket', $nomorTiket)->exists());

        return $nomorTiket;
    }
}
