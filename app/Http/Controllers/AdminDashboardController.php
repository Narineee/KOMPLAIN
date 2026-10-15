<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama admin dengan ringkasan statistik.
     */
    public function index(): View
    {
        $now = now();
        $tahunAktif = (int) $now->year;
        $bulanAktif = (int) $now->month;
        $namaBulan = $now->translatedFormat('F');
        $namaBulanTahun = $now->translatedFormat('F Y');

        $statistik = $this->hitungStatistik();
        $statistikTahun = $this->hitungStatistik($tahunAktif);
        $statistikBulan = $this->hitungStatistik($tahunAktif, $bulanAktif);

        // Definisi laporan pengaduan terbaru: Masuk dalam kurun 1 minggu (7 hari) terakhir
        $batasWaktuTerbaru = $now->copy()->subDays(7)->startOfDay();
        $rentangTerbaru = [
            'dari' => $batasWaktuTerbaru,
            'sampai' => $now,
            'dari_teks' => $batasWaktuTerbaru->locale('id')->translatedFormat('d F Y'),
            'sampai_teks' => $now->locale('id')->translatedFormat('d F Y'),
            'hari' => 7,
        ];

        $laporanTerbaru = Pengajuan::with(['kategori.parent', 'user.unit'])
            ->where('created_at', '>=', $batasWaktuTerbaru)
            ->latest()
            ->get();

        return view('admin.dashboard', [
            'statistik' => $statistik,
            'statistikTahun' => $statistikTahun,
            'statistikBulan' => $statistikBulan,
            'tahunAktif' => $tahunAktif,
            'bulanAktif' => $bulanAktif,
            'namaBulan' => $namaBulan,
            'namaBulanTahun' => $namaBulanTahun,
            'laporanTerbaru' => $laporanTerbaru,
            'rentangTerbaru' => $rentangTerbaru,
        ]);
    }

    /**
     * Menghitung ringkasan statistik laporan berdasarkan tahun dan/atau bulan.
     *
     * @return array{total: int, diajukan: int, verifikasi: int, diproses: int, selesai: int, selesai_ditangani: int, selesai_ditolak: int, ditolak: int, proses_identifikasi: int, proses_klarifikasi: int, proses_rencana_aksi: int, proses_aksi: int}
     */
    private function hitungStatistik(?int $tahun = null, ?int $bulan = null): array
    {
        $query = Pengajuan::query();

        if ($tahun !== null) {
            $query->whereYear('created_at', $tahun);
        }

        if ($bulan !== null) {
            $query->whereMonth('created_at', $bulan);
        }

        $row = $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status IN ('diajukan', 'menunggu') THEN 1 ELSE 0 END) as diajukan,
            SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN (status = 'selesai' AND (sub_status = 'selesai_ditangani' OR sub_status IS NULL)) THEN 1 ELSE 0 END) as selesai_ditangani,
            SUM(CASE WHEN (status = 'selesai' AND sub_status = 'selesai_ditolak') OR status = 'ditolak' THEN 1 ELSE 0 END) as selesai_ditolak,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'identifikasi' THEN 1 ELSE 0 END) as proses_identifikasi,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'klarifikasi' THEN 1 ELSE 0 END) as proses_klarifikasi,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'rencana_aksi' THEN 1 ELSE 0 END) as proses_rencana_aksi,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'aksi' THEN 1 ELSE 0 END) as proses_aksi
        ")->first();

        $selesaiDitolak = (int) ($row?->selesai_ditolak ?? 0);

        return [
            'total' => (int) ($row?->total ?? 0),
            'diajukan' => (int) ($row?->diajukan ?? 0),
            'verifikasi' => (int) ($row?->diajukan ?? 0),
            'diproses' => (int) ($row?->diproses ?? 0),
            'selesai' => (int) ($row?->selesai ?? 0),
            'selesai_ditangani' => (int) ($row?->selesai_ditangani ?? 0),
            'selesai_ditolak' => $selesaiDitolak,
            'ditolak' => $selesaiDitolak,
            'proses_identifikasi' => (int) ($row?->proses_identifikasi ?? 0),
            'proses_klarifikasi' => (int) ($row?->proses_klarifikasi ?? 0),
            'proses_rencana_aksi' => (int) ($row?->proses_rencana_aksi ?? 0),
            'proses_aksi' => (int) ($row?->proses_aksi ?? 0),
        ];
    }

    /**
     * Menampilkan daftar semua laporan dengan filter dan pencarian.
     */
    public function laporanIndex(Request $request): View
    {
        $query = Pengajuan::with(['kategori.parent', 'user.unit'])->latest();

        if ($request->filled('status')) {
            $status = $request->query('status');
            if (in_array($status, ['diajukan', 'menunggu', 'verifikasi'], true)) {
                $query->whereIn('status', ['diajukan', 'menunggu']);
            } elseif ($status === 'selesai_ditangani') {
                $query->where('status', 'selesai')->where(function ($sq) {
                    $sq->where('sub_status', 'selesai_ditangani')->orWhereNull('sub_status');
                });
            } elseif ($status === 'selesai_ditolak' || $status === 'ditolak') {
                $query->where(function ($sq) {
                    $sq->where(function ($s1) {
                        $s1->where('status', 'selesai')->where('sub_status', 'selesai_ditolak');
                    })->orWhere('status', 'ditolak');
                });
            } elseif (in_array($status, ['identifikasi', 'klarifikasi', 'rencana_aksi', 'aksi'], true)) {
                $query->where('status', 'diproses')->where('sub_status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('sub_status')) {
            $query->where('sub_status', $request->query('sub_status'));
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->query('kategori_id'));
        }

        if ($request->has('is_anonim') && $request->query('is_anonim') !== '') {
            $query->where('is_anonim', (bool) $request->query('is_anonim'));
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->query('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->query('sampai_tanggal'));
        }

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $cleanId = ltrim($keyword, '#');
                if (is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }
                $q->orWhere('deskripsi', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('nip', 'like', "%{$keyword}%");
                    });
            });
        }

        $pengajuans = $query->paginate(15)->withQueryString();
        $kategoris = Kategori::with('parent')->where('aktif', true)->orderBy('nama_kategori')->get();

        return view('admin.laporan.index', [
            'pengajuans' => $pengajuans,
            'kategoris' => $kategoris,
            'filter' => $request->all(),
        ]);
    }

    /**
     * Menampilkan detail lengkap laporan beserta tanggapan dan riwayat.
     */
    public function laporanShow(Pengajuan $pengajuan): View
    {
        $pengajuan->load([
            'kategori.parent',
            'user.unit',
            'lampirans',
            'riwayatStatuses.user',
            'tanggapans.user',
        ]);

        $isAnonimUnlocked = (bool) session('unlocked_anonim_'.$pengajuan->id, false);

        return view('admin.laporan.show', [
            'pengajuan' => $pengajuan,
            'isAnonimUnlocked' => $isAnonimUnlocked,
        ]);
    }

    /**
     * Membuka kunci identitas pelapor anonim dengan verifikasi password admin / kunci otorisasi.
     */
    public function revealAnonim(Request $request, Pengajuan $pengajuan): JsonResponse|RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Password atau kunci otorisasi wajib diisi.',
        ]);

        $user = $request->user();
        $inputPassword = (string) $request->input('password');

        $masterKey = config('app.admin_anonim_key', env('ADMIN_ANONIM_KEY', 'admin123'));

        $isValid = Hash::check($inputPassword, $user->password)
            || ($masterKey !== null && $masterKey !== '' && hash_equals((string) $masterKey, $inputPassword));

        if (! $isValid) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password atau kunci otorisasi salah. Akses ditolak.',
                ], 422);
            }

            return back()->withErrors(['password' => 'Password atau kunci otorisasi salah. Akses ditolak.']);
        }

        $request->session()->put('unlocked_anonim_'.$pengajuan->id, true);

        $pengajuan->loadMissing('user.unit');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kunci identitas pelapor berhasil dibuka.',
                'data' => [
                    'nama' => $pengajuan->user?->name ?? $pengajuan->user?->nama ?? '(Akun Pengguna Tidak Ditemukan)',
                    'nip' => $pengajuan->user?->nip ?? '-',
                    'unit' => $pengajuan->user?->unit?->nama_unit ?? '-',
                    'email' => $pengajuan->user?->email ?? '-',
                ],
            ]);
        }

        return back()->with('success', 'Identitas asli pelapor anonim berhasil dibuka.');
    }

    /**
     * Mengunci kembali identitas pelapor anonim.
     */
    public function lockAnonim(Request $request, Pengajuan $pengajuan): JsonResponse|RedirectResponse
    {
        $request->session()->forget('unlocked_anonim_'.$pengajuan->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Identitas pelapor berhasil dikunci kembali.',
            ]);
        }

        return back()->with('info', 'Identitas pelapor berhasil dikunci kembali.');
    }

    /**
     * Memperbarui status penanganan laporan beserta tahapan sub-proses atau sub-selesai.
     */
    public function updateStatus(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,diajukan,verifikasi,diproses,selesai,ditolak'],
            'sub_status' => ['nullable', 'string', 'in:identifikasi,klarifikasi,rencana_aksi,aksi,selesai_ditangani,selesai_ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status baru wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'sub_status.in' => 'Tahapan proses atau hasil akhir yang dipilih tidak valid.',
        ]);

        $statusInput = $validated['status'];
        $subStatusInput = $validated['sub_status'] ?? null;

        // Normalisasi status utama dan sub_status
        if (in_array($statusInput, ['diajukan', 'verifikasi'], true)) {
            $statusBaru = 'menunggu';
            $subStatusBaru = null;
        } elseif ($statusInput === 'ditolak') {
            // Jika ada pengiriman status 'ditolak' legacy, petakan ke selesai dengan sub_status selesai_ditolak
            $statusBaru = 'selesai';
            $subStatusBaru = 'selesai_ditolak';
        } else {
            $statusBaru = $statusInput;
            if ($statusBaru === 'diproses') {
                $subStatusBaru = in_array($subStatusInput, ['identifikasi', 'klarifikasi', 'rencana_aksi', 'aksi'], true)
                    ? $subStatusInput
                    : 'identifikasi';
            } elseif ($statusBaru === 'selesai') {
                $subStatusBaru = in_array($subStatusInput, ['selesai_ditangani', 'selesai_ditolak'], true)
                    ? $subStatusInput
                    : 'selesai_ditangani';
            } else {
                $subStatusBaru = null;
            }
        }

        $statusLama = in_array($pengajuan->status, ['diajukan', 'verifikasi'], true) ? 'menunggu' : $pengajuan->status;
        $subStatusLama = $pengajuan->sub_status;
        $catatan = $validated['catatan'] ?? null;
        $admin = $request->user();

        if ($statusLama === $statusBaru && $subStatusLama === $subStatusBaru) {
            return back()->with('info', 'Status dan tahapan laporan tidak berubah.');
        }

        $labelStatus = match ($statusBaru) {
            'menunggu' => 'Verifikasi',
            'diproses' => 'Diproses ('.(Pengajuan::SUB_STATUS_PROSES[$subStatusBaru] ?? 'Identifikasi').')',
            'selesai' => ($subStatusBaru === 'selesai_ditolak' ? 'Selesai Ditolak' : 'Selesai Ditangani'),
            default => ucfirst($statusBaru),
        };

        DB::transaction(function () use ($pengajuan, $statusLama, $statusBaru, $subStatusLama, $subStatusBaru, $catatan, $admin, $labelStatus) {
            $updateData = [
                'status' => $statusBaru,
                'sub_status' => $subStatusBaru,
            ];

            if ($statusBaru === 'selesai') {
                $updateData['tanggal_selesai'] = now();
            } else {
                $updateData['tanggal_selesai'] = null;
            }

            $pengajuan->update($updateData);

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => $admin->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'sub_status_sebelumnya' => $subStatusLama,
                'sub_status_baru' => $subStatusBaru,
                'catatan' => $catatan,
            ]);

            // Kirim notifikasi jika laporan beridentitas
            if (! $pengajuan->is_anonim && $pengajuan->user_id) {
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_id' => $pengajuan->id,
                    'judul' => 'Status Laporan Diperbarui',
                    'pesan' => "Status laporan #{$pengajuan->id} telah diubah menjadi: {$labelStatus}",
                    'tipe' => 'status_update',
                ]);
            }
        });

        return back()->with('success', "Status laporan berhasil diperbarui menjadi: {$labelStatus}.");
    }

    /**
     * Menampilkan halaman rekapitulasi dan laporan statistik.
     */
    public function rekap(Request $request): View
    {
        $kategoris = Kategori::with('parent')->orderBy('nama_kategori')->get();
        $query = Pengajuan::with(['kategori.parent', 'user.unit']);

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->query('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->query('sampai_tanggal'));
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->query('kategori_id'));
        }

        if ($request->filled('status')) {
            $status = $request->query('status');
            if (in_array($status, ['diajukan', 'menunggu', 'verifikasi'], true)) {
                $query->whereIn('status', ['diajukan', 'menunggu']);
            } elseif ($status === 'selesai_ditangani') {
                $query->where('status', 'selesai')->where(function ($sq) {
                    $sq->where('sub_status', 'selesai_ditangani')->orWhereNull('sub_status');
                });
            } elseif ($status === 'selesai_ditolak' || $status === 'ditolak') {
                $query->where(function ($sq) {
                    $sq->where(function ($s1) {
                        $s1->where('status', 'selesai')->where('sub_status', 'selesai_ditolak');
                    })->orWhere('status', 'ditolak');
                });
            } elseif (in_array($status, ['identifikasi', 'klarifikasi', 'rencana_aksi', 'aksi'], true)) {
                $query->where('status', 'diproses')->where('sub_status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('sub_status')) {
            $query->where('sub_status', $request->query('sub_status'));
        }

        $laporan = $query->latest()->get();

        $selesaiDitolak = $laporan->filter(function ($l) {
            return ($l->status === 'selesai' && $l->sub_status === 'selesai_ditolak') || $l->status === 'ditolak';
        })->count();

        $selesaiDitangani = $laporan->filter(function ($l) {
            return $l->status === 'selesai' && ($l->sub_status === 'selesai_ditangani' || $l->sub_status === null);
        })->count();

        $ringkasan = [
            'total' => $laporan->count(),
            'diajukan' => $laporan->whereIn('status', ['diajukan', 'menunggu'])->count(),
            'verifikasi' => $laporan->whereIn('status', ['diajukan', 'menunggu'])->count(),
            'diproses' => $laporan->where('status', 'diproses')->count(),
            'selesai' => $laporan->where('status', 'selesai')->count(),
            'selesai_ditangani' => $selesaiDitangani,
            'selesai_ditolak' => $selesaiDitolak,
            'ditolak' => $selesaiDitolak,
            'anonim' => $laporan->where('is_anonim', true)->count(),
            'identitas' => $laporan->where('is_anonim', false)->count(),
        ];

        return view('admin.rekap', [
            'laporan' => $laporan,
            'ringkasan' => $ringkasan,
            'kategoris' => $kategoris,
            'filter' => $request->all(),
        ]);
    }
}
