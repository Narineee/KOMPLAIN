<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanRequest;
use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\Tanggapan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * [BARU] Dashboard pengadu: sapaan, pengingat konfirmasi, dan aduan terbaru.
     */
    public function dashboard(Request $request): View
    {
        // Proses aduan selesai yang melewati batas waktu 3 hari tanpa konfirmasi
        Pengajuan::prosesAutoKonfirmasiKadaluarsa();

        $userId = $request->user()->id;

        $menungguKonfirmasi = Pengajuan::with('kategori.parent')
            ->where('user_id', $userId)
            ->perluKonfirmasi()
            ->latest('tanggal_selesai')
            ->get();

        $terbaru = Pengajuan::with('kategori.parent')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('pegawai.dashboard', [
            'menungguKonfirmasi' => $menungguKonfirmasi,
            'terbaru' => $terbaru,
        ]);
    }

    /**
     * Pilihan filter status pada halaman riwayat.
     */
    private const OPSI_STATUS = [
        'belum_verifikasi' => 'Belum Diverifikasi',
        'verifikasi' => 'Verifikasi',
        'identifikasi_klarifikasi' => 'Diproses (Identifikasi / Klarifikasi)',
        'rencana_aksi' => 'Diproses (Rencana Aksi)',
        'aksi' => 'Diproses (Aksi / Tindakan)',
        'ditinjau_ulang' => 'Ditinjau Ulang',
        'menunggu_konfirmasi' => 'Selesai · Menunggu konfirmasi',
        'selesai' => 'Selesai',
        'selesai_setelah_tinjau_ulang' => 'Selesai setelah tinjau ulang',
    ];

    /**
     * [DIUBAH] Riwayat laporan milik pengadu: pencarian, filter status, dan periode.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $tahunIni = (int) now()->year;

        $q = trim((string) $request->query('q', ''));

        $status = (string) $request->query('status', '');
        if (! array_key_exists($status, self::OPSI_STATUS)) {
            $status = '';
        }

        $tahunInput = (string) $request->query('tahun', (string) $tahunIni);
        $tahun = $tahunInput === 'semua' ? null : (int) $tahunInput;
        if ($tahun !== null && ($tahun < 2000 || $tahun > 2100)) {
            $tahun = $tahunIni;
        }

        $query = Pengajuan::with('kategori.parent')
            ->where('user_id', $userId)
            ->latest();

        // Cari berdasarkan nomor (#12 atau 12) atau isi uraian
        if ($q !== '') {
            $nomor = ltrim($q, '#');
            $query->where(function ($w) use ($q, $nomor) {
                if (ctype_digit($nomor)) {
                    $w->orWhere('id', (int) $nomor);
                }
                $w->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($status === 'belum_verifikasi') {
            $query->where('status', 'belum_verifikasi');
        } elseif ($status === 'verifikasi') {
            $query->whereIn('status', ['verifikasi', 'menunggu', 'diajukan']);
        } elseif ($status === 'ditinjau_ulang') {
            $query->whereIn('status', ['diproses', 'proses'])->where('konfirmasi_pelapor', 'keberatan');
        } elseif ($status === 'identifikasi_klarifikasi' || $status === 'identifikasi' || $status === 'klarifikasi') {
            $query->whereIn('status', ['diproses', 'proses'])
                ->whereIn('sub_status', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']);
        } elseif (in_array($status, ['rencana_aksi', 'aksi'], true)) {
            $query->whereIn('status', ['diproses', 'proses'])->where('sub_status', $status);
        } elseif ($status === 'menunggu_konfirmasi') {
            $query->perluKonfirmasi();
        } elseif ($status === 'selesai_setelah_tinjau_ulang') {
            $query->whereIn('status', ['selesai', 'ditolak'])->where('sub_status', 'selesai_setelah_tinjau_ulang');
        } elseif ($status === 'selesai' || $status === 'selesai_ditangani' || $status === 'selesai_ditolak') {
            $query->whereIn('status', ['selesai', 'ditolak']);
        }

        if ($tahun !== null) {
            $query->whereBetween('created_at', [
                Carbon::create($tahun, 1, 1)->startOfYear(),
                Carbon::create($tahun, 1, 1)->endOfYear(),
            ]);
        }

        // Daftar tahun untuk pilihan Periode: dari tahun laporan pertama sampai tahun ini
        $pertama = Pengajuan::where('user_id', $userId)->min('created_at');
        $tahunAwal = $pertama ? min((int) Carbon::parse($pertama)->year, $tahunIni) : $tahunIni;

        return view('pegawai.pengajuan.index', [
            'pengajuans' => $query->paginate(10)->withQueryString(),
            'q' => $q,
            'status' => $status,
            'tahunAktif' => $tahun === null ? 'semua' : (string) $tahun,
            'daftarTahun' => range($tahunIni, $tahunAwal),
            'opsiStatus' => self::OPSI_STATUS,
        ]);
    }

    /**
     * Menampilkan formulir buat pengaduan laporan.
     */
    public function create(): View
    {
        $parentKategoris = Kategori::whereNull('parent_id')
            ->where('aktif', true)
            ->with(['activeChildren'])
            ->orderBy('nama_kategori')
            ->get();

        return view('pegawai.pengajuan.create', [
            'parentKategoris' => $parentKategoris,
            'kategoris' => $parentKategoris,
        ]);
    }

    /**
     * Menyimpan laporan baru ke database. (Tidak diubah.)
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isAnonim = $request->boolean('is_anonim', false);
        $user = $request->user();

        $pengajuan = DB::transaction(function () use ($validated, $isAnonim, $user, $request) {
            $pengajuan = Pengajuan::create([
                'user_id' => $user->id,
                'kategori_id' => $validated['kategori_id'],
                'deskripsi' => $validated['isi_laporan'],
                'isi_laporan' => $validated['isi_laporan'],
                'saran_penyelesaian' => $validated['saran_penyelesaian'] ?? null,
                'metode_identitas' => $isAnonim ? 'anonim' : 'identitas',
                'status' => 'belum_verifikasi',
                'pernyataan_disetujui' => true,
                'waktu_persetujuan' => now(),
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => $user->id,
                'status_sebelumnya' => null,
                'status_baru' => 'belum_verifikasi',
                'catatan' => 'Laporan berhasil dibuat dan diajukan ke sistem.',
            ]);

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

            Notifikasi::create([
                'user_id' => $user->id,
                'pengajuan_id' => $pengajuan->id,
                'judul' => 'Laporan Berhasil Diajukan',
                'pesan' => "Laporan #{$pengajuan->id} telah berhasil diajukan dan sedang dalam proses verifikasi admin.",
            ]);

            return $pengajuan;
        });

        return redirect()->route('pegawai.dashboard')->with('status', "Pengaduan Anda (#{$pengajuan->id}) berhasil diajukan!");
    }

    /**
     * [DIUBAH] Detail laporan milik pengadu.
     * Catatan internal admin sekarang disaring agar tidak terlihat pelapor.
     */
    public function show(Pengajuan $pengajuan): View
    {
        if ($pengajuan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak melihat rincian laporan ini.');
        }

        $pengajuan->load([
            'kategori.parent',
            'user.unit',
            'assignedUnit',
            'lampirans',
            'riwayatStatuses',
            'tanggapans' => fn ($query) => $query->where('is_internal', false)->with('user.unit')->latest(),
        ]);

        return view('pegawai.pengajuan.show', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * [BARU] Pelapor mengonfirmasi bahwa laporan benar-benar sudah selesai.
     */
    public function konfirmasi(Pengajuan $pengajuan): RedirectResponse
    {
        abort_unless($pengajuan->user_id === Auth::id(), 403);

        if ($pengajuan->apakah_kadaluarsa_konfirmasi) {
            Pengajuan::prosesAutoKonfirmasiKadaluarsa();

            return back()->with('info', 'Batas waktu konfirmasi 3 hari telah berakhir. Laporan telah diselesaikan secara otomatis oleh sistem.');
        }

        if (! $pengajuan->perlu_konfirmasi) {
            return back()->with('info', 'Laporan ini tidak sedang menunggu konfirmasi.');
        }

        DB::transaction(function () use ($pengajuan) {
            $pengajuan->update([
                'konfirmasi_pelapor' => 'dikonfirmasi',
                'dikonfirmasi_pada' => now(),
            ]);

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => Auth::id(),
                'status_sebelumnya' => 'selesai',
                'status_baru' => 'selesai',
                'sub_status_sebelumnya' => $pengajuan->sub_status,
                'sub_status_baru' => $pengajuan->sub_status,
                'catatan' => 'Pelapor mengonfirmasi bahwa laporan telah selesai ditangani.',
            ]);
        });

        return back()->with('success', 'Terima kasih, konfirmasi Anda sudah kami catat.');
    }

    /**
     * [BARU] Menampilkan halaman formulir pengaduan keberatan.
     */
    public function formKeberatan(Pengajuan $pengajuan): View|RedirectResponse
    {
        abort_unless($pengajuan->user_id === Auth::id(), 403);

        if ($pengajuan->apakah_kadaluarsa_konfirmasi) {
            Pengajuan::prosesAutoKonfirmasiKadaluarsa();

            return redirect()->route('pegawai.pengajuan.show', $pengajuan)
                ->with('error', 'Batas waktu 3 hari untuk konfirmasi atau pengajuan keberatan telah berakhir. Laporan telah diselesaikan secara otomatis.');
        }

        if (! $pengajuan->bisa_ajukan_keberatan) {
            $pesan = ($pengajuan->jumlah_keberatan ?? 0) >= 1
                ? 'Pengajuan keberatan hanya dapat dilakukan maksimal 1 (satu) kali per pengaduan. Jika kendala masih berlanjut, silakan ajukan pengaduan baru.'
                : 'Laporan ini tidak sedang menunggu konfirmasi.';

            return redirect()->route('pegawai.pengajuan.show', $pengajuan)
                ->with('error', $pesan);
        }

        $pengajuan->load(['kategori.parent', 'riwayatStatuses']);

        return view('pegawai.pengajuan.keberatan', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * [BARU] Pelapor mengajukan keberatan; laporan dibuka kembali ke tahap klarifikasi.
     */
    public function keberatan(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        abort_unless($pengajuan->user_id === Auth::id(), 403);

        if ($pengajuan->apakah_kadaluarsa_konfirmasi) {
            Pengajuan::prosesAutoKonfirmasiKadaluarsa();

            return redirect()->route('pegawai.pengajuan.show', $pengajuan)
                ->with('error', 'Batas waktu 3 hari untuk konfirmasi atau pengajuan keberatan telah berakhir. Laporan telah diselesaikan secara otomatis.');
        }

        if (! $pengajuan->bisa_ajukan_keberatan) {
            $pesan = ($pengajuan->jumlah_keberatan ?? 0) >= 1
                ? 'Pengajuan keberatan hanya dapat dilakukan maksimal 1 (satu) kali per pengaduan. Silakan ajukan aduan baru jika kendala masih berlanjut.'
                : 'Laporan ini tidak sedang menunggu konfirmasi.';

            return redirect()->route('pegawai.pengajuan.show', $pengajuan)
                ->with('error', $pesan);
        }

        $validated = $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'alasan.required' => 'Jelaskan apa yang belum teratasi.',
            'alasan.min' => 'Alasan keberatan minimal 10 karakter.',
            'lampiran.*.mimes' => 'Format berkas lampiran harus berupa JPG, PNG, atau PDF.',
            'lampiran.*.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
        ]);

        DB::transaction(function () use ($pengajuan, $request, $validated) {
            $subLama = $pengajuan->sub_status ?? 'selesai';

            $pengajuan->update([
                'status' => 'diproses',
                'sub_status' => 'identifikasi_klarifikasi',
                'tanggal_selesai' => null,
                'konfirmasi_pelapor' => 'keberatan',
                'dikonfirmasi_pada' => now(),
                'jumlah_keberatan' => ($pengajuan->jumlah_keberatan ?? 0) + 1,
            ]);

            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    if ($file && $file->isValid()) {
                        Lampiran::create([
                            'pengajuan_id' => $pengajuan->id,
                            'nama_file' => $file->getClientOriginalName(),
                            'path_file' => $file->store('lampiran', 'public'),
                            'mime_type' => $file->getClientMimeType(),
                            'ukuran_bytes' => $file->getSize(),
                        ]);
                    }
                }
            }

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => Auth::id(),
                'status_sebelumnya' => 'selesai',
                'status_baru' => 'diproses',
                'sub_status_sebelumnya' => $subLama,
                'sub_status_baru' => 'identifikasi_klarifikasi',
                'catatan' => 'Keberatan dari pelapor: '.$validated['alasan'],
            ]);

            Tanggapan::create([
                'pengajuan_id' => $pengajuan->id,
                'admin_id' => Auth::id(),
                'isi_tanggapan' => $validated['alasan'],
                'is_internal' => false,
            ]);
        });

        return redirect()->route('pegawai.pengajuan.show', $pengajuan)
            ->with('success', 'Keberatan Anda terkirim. Laporan dibuka kembali untuk klarifikasi.');
    }
}
