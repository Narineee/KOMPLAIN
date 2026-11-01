<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\Unit;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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
            SUM(CASE WHEN status = 'belum_verifikasi' THEN 1 ELSE 0 END) as belum_verifikasi,
            SUM(CASE WHEN status IN ('diajukan', 'menunggu', 'verifikasi') THEN 1 ELSE 0 END) as verifikasi,
            SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
            SUM(CASE WHEN status IN ('selesai', 'ditolak') THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = 'diproses' AND sub_status IN ('identifikasi_klarifikasi', 'identifikasi', 'klarifikasi') THEN 1 ELSE 0 END) as proses_identifikasi_klarifikasi,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'rencana_aksi' THEN 1 ELSE 0 END) as proses_rencana_aksi,
            SUM(CASE WHEN status = 'diproses' AND sub_status = 'aksi' THEN 1 ELSE 0 END) as proses_aksi
        ")->first();

        $selesai = (int) ($row?->selesai ?? 0);
        $prosesIdentifikasiKlarifikasi = (int) ($row?->proses_identifikasi_klarifikasi ?? 0);
        $belumVerifikasi = (int) ($row?->belum_verifikasi ?? 0);
        $verifikasi = (int) ($row?->verifikasi ?? 0);

        return [
            'total' => (int) ($row?->total ?? 0),
            'belum_verifikasi' => $belumVerifikasi,
            'diajukan' => $belumVerifikasi + $verifikasi,
            'verifikasi' => $verifikasi,
            'diproses' => (int) ($row?->diproses ?? 0),
            'selesai' => $selesai,
            'selesai_ditangani' => $selesai,
            'selesai_ditolak' => 0,
            'ditolak' => 0,
            'proses_identifikasi_klarifikasi' => $prosesIdentifikasiKlarifikasi,
            'proses_identifikasi' => $prosesIdentifikasiKlarifikasi,
            'proses_klarifikasi' => 0,
            'proses_rencana_aksi' => (int) ($row?->proses_rencana_aksi ?? 0),
            'proses_aksi' => (int) ($row?->proses_aksi ?? 0),
        ];
    }

    /**
     * Menampilkan daftar semua laporan dengan filter dan pencarian.
     */
    public function laporanIndex(Request $request): View
    {
        $query = Pengajuan::with(['kategori.parent', 'user.unit', 'assignedUnit', 'petugas'])->latest();

        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'belum_verifikasi') {
                $query->where('status', 'belum_verifikasi');
            } elseif (in_array($status, ['diajukan', 'menunggu', 'verifikasi'], true)) {
                $query->whereIn('status', ['diajukan', 'menunggu', 'verifikasi']);
            } elseif ($status === 'selesai_setelah_tinjau_ulang') {
                $query->where('status', 'selesai')->where('sub_status', 'selesai_setelah_tinjau_ulang');
            } elseif (in_array($status, ['selesai', 'selesai_ditangani', 'selesai_ditolak', 'ditolak'], true)) {
                $query->whereIn('status', ['selesai', 'ditolak']);
            } elseif (in_array($status, ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi'], true)) {
                $query->where('status', 'diproses')
                    ->whereIn('sub_status', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']);
            } elseif (in_array($status, ['rencana_aksi', 'aksi'], true)) {
                $query->where('status', 'diproses')->where('sub_status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('sub_status')) {
            $query->where('sub_status', $request->query('sub_status'));
        }

        if ($request->filled('assigned_unit_id')) {
            $query->where('assigned_unit_id', $request->query('assigned_unit_id'));
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
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.laporan.index', [
            'pengajuans' => $pengajuans,
            'kategoris' => $kategoris,
            'units' => $units,
            'filter' => $request->all(),
        ]);
    }

    /**
     * Menampilkan detail lengkap laporan beserta tanggapan dan riwayat.
     */
    public function laporanShow(Request $request, Pengajuan $pengajuan): View
    {
        $pengajuan->load([
            'kategori.parent',
            'user.unit',
            'lampirans',
            'riwayatStatuses.user',
            'tanggapans.user',
            'petugas',
            'assignedUnit',
        ]);

        $units = Unit::orderBy('nama_unit')->get();
        $petugasList = User::where('role', 'petugas')->where('status_akun', 'aktif')->orderBy('nama')->get();

        $isAnonimUnlocked = (bool) session('unlocked_anonim_'.$pengajuan->id, false);

        return view('admin.laporan.show', [
            'pengajuan' => $pengajuan,
            'isAnonimUnlocked' => $isAnonimUnlocked,
            'units' => $units,
            'petugasList' => $petugasList,
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
            'status' => ['required', 'in:belum_verifikasi,menunggu,diajukan,verifikasi,diproses,selesai,ditolak'],
            'sub_status' => ['nullable', 'string', 'in:identifikasi_klarifikasi,identifikasi,klarifikasi,rencana_aksi,aksi,selesai,selesai_ditangani,selesai_ditolak,selesai_setelah_tinjau_ulang'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status baru wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'sub_status.in' => 'Tahapan proses yang dipilih tidak valid.',
        ]);

        $statusInput = $validated['status'];
        $subStatusInput = $validated['sub_status'] ?? null;

        $isDariTinjauUlang = ($pengajuan->jumlah_keberatan ?? 0) > 0
            || $pengajuan->konfirmasi_pelapor === 'keberatan'
            || $pengajuan->sub_status === 'selesai_setelah_tinjau_ulang';

        // Normalisasi status utama dan sub_status
        if ($statusInput === 'belum_verifikasi') {
            $statusBaru = 'belum_verifikasi';
            $subStatusBaru = null;
        } elseif (in_array($statusInput, ['diajukan', 'verifikasi', 'menunggu'], true)) {
            $statusBaru = 'verifikasi';
            $subStatusBaru = null;
        } elseif (in_array($statusInput, ['selesai', 'ditolak'], true)) {
            $statusBaru = 'selesai';
            $subStatusBaru = $isDariTinjauUlang ? 'selesai_setelah_tinjau_ulang' : null;
        } else {
            $statusBaru = $statusInput;
            if ($statusBaru === 'diproses') {
                if (in_array($subStatusInput, ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi'], true)) {
                    $subStatusBaru = 'identifikasi_klarifikasi';
                } elseif (in_array($subStatusInput, ['rencana_aksi', 'aksi'], true)) {
                    $subStatusBaru = $subStatusInput;
                } else {
                    $subStatusBaru = 'identifikasi_klarifikasi';
                }
            } else {
                $subStatusBaru = null;
            }
        }

        $statusLama = $pengajuan->status;
        $subStatusLama = $pengajuan->sub_status;
        $catatan = $validated['catatan'] ?? null;
        $admin = $request->user();

        if ($statusLama === $statusBaru && $subStatusLama === $subStatusBaru) {
            return back()->with('info', 'Status dan tahapan laporan tidak berubah.');
        }

        $labelStatus = match ($statusBaru) {
            'belum_verifikasi' => 'Belum Diverifikasi',
            'verifikasi', 'menunggu' => 'Verifikasi',
            'diproses' => 'Diproses ('.(Pengajuan::SUB_STATUS_PROSES[$subStatusBaru] ?? 'Identifikasi / Klarifikasi').')',
            'selesai' => $subStatusBaru === 'selesai_setelah_tinjau_ulang' ? 'Selesai setelah tinjau ulang' : 'Selesai',
            default => ucfirst($statusBaru),
        };

        DB::transaction(function () use ($pengajuan, $statusLama, $statusBaru, $subStatusLama, $subStatusBaru, $catatan, $admin, $labelStatus) {
            $updateData = [
                'status' => $statusBaru,
                'sub_status' => $subStatusBaru,
            ];

            if ($statusBaru === 'selesai') {
                $updateData['tanggal_selesai'] = now();
                if ($subStatusBaru === 'selesai_setelah_tinjau_ulang') {
                    $updateData['konfirmasi_pelapor'] = 'selesai_tinjau_ulang';
                    $updateData['dikonfirmasi_pada'] = now();
                } else {
                    $updateData['konfirmasi_pelapor'] = null;
                    $updateData['dikonfirmasi_pada'] = null;
                }
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

        if ($statusBaru === 'belum_verifikasi') {
            return redirect()->route('admin.laporan.show', $pengajuan)
                ->with('success', "Status laporan berhasil diperbarui menjadi: {$labelStatus}.");
        }

        return back()->with('success', "Status laporan berhasil diperbarui menjadi: {$labelStatus}.");
    }

    /**
     * Menugaskan laporan ke bidang tugas dan petugas bidang berwenang.
     */
    public function tugaskan(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_unit_id' => ['required', 'exists:unit,id'],
            'petugas_id' => ['nullable', 'exists:users,id'],
            'catatan_penugasan' => ['nullable', 'string', 'max:1000'],
        ], [
            'assigned_unit_id.required' => 'Bidang tugas wajib dipilih.',
            'assigned_unit_id.exists' => 'Bidang tugas tidak valid.',
            'petugas_id.exists' => 'Petugas bidang tidak valid.',
        ]);

        $admin = $request->user();
        $unit = Unit::findOrFail($validated['assigned_unit_id']);
        $petugas = ! empty($validated['petugas_id']) ? User::find($validated['petugas_id']) : null;
        $catatan = $validated['catatan_penugasan'] ?? null;

        DB::transaction(function () use ($pengajuan, $unit, $petugas, $catatan, $admin) {
            $statusLama = $pengajuan->status;
            $subStatusLama = $pengajuan->sub_status;

            // Jika status masih belum_verifikasi atau verifikasi, otomatis jadikan diproses
            $statusBaru = in_array($statusLama, ['belum_verifikasi', 'verifikasi', 'menunggu', 'diajukan'], true) ? 'diproses' : $statusLama;
            $subStatusBaru = $statusBaru === 'diproses' && empty($subStatusLama) ? 'identifikasi_klarifikasi' : $subStatusLama;

            $pengajuan->update([
                'assigned_unit_id' => $unit->id,
                'petugas_id' => $petugas?->id,
                'catatan_penugasan' => $catatan,
                'status' => $statusBaru,
                'sub_status' => $subStatusBaru,
            ]);

            $teksPetugas = $petugas ? " ({$petugas->name})" : '';
            $teksCatatan = $catatan ? " - Catatan: {$catatan}" : '';
            $deskripsiRiwayat = "Ditugaskan ke {$unit->nama_unit}{$teksPetugas}{$teksCatatan}";

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => $admin->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'sub_status_sebelumnya' => $subStatusLama,
                'sub_status_baru' => $subStatusBaru,
                'catatan' => $deskripsiRiwayat,
            ]);

            // Notifikasi ke petugas yang ditugaskan
            if ($petugas) {
                Notifikasi::create([
                    'user_id' => $petugas->id,
                    'pengajuan_id' => $pengajuan->id,
                    'judul' => 'Tugas Pengaduan Baru',
                    'pesan' => "Anda telah ditugaskan oleh Admin untuk menangani laporan #{$pengajuan->id}.",
                    'tipe' => 'penugasan_baru',
                ]);
            }

            // Notifikasi ke pelapor jika bukan anonim
            if (! $pengajuan->is_anonim && $pengajuan->user_id) {
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_id' => $pengajuan->id,
                    'judul' => 'Laporan Diteruskan ke Bidang Terkait',
                    'pesan' => "Laporan Anda telah diteruskan ke {$unit->nama_unit} untuk ditindaklanjuti.",
                    'tipe' => 'status_update',
                ]);
            }
        });

        return back()->with('success', "Laporan berhasil ditugaskan ke {$unit->nama_unit}.");
    }

    /**
     * Menandai laporan kembali sebagai Belum Diverifikasi.
     */
    public function tandaiBelumVerifikasi(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $admin = $request->user();

        if ($pengajuan->status !== 'belum_verifikasi') {
            $statusLama = $pengajuan->status;
            $subStatusLama = $pengajuan->sub_status;

            DB::transaction(function () use ($pengajuan, $admin, $statusLama, $subStatusLama) {
                $pengajuan->update([
                    'status' => 'belum_verifikasi',
                    'sub_status' => null,
                ]);

                RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'diubah_oleh' => $admin->id,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => 'belum_verifikasi',
                    'sub_status_sebelumnya' => $subStatusLama,
                    'sub_status_baru' => null,
                    'catatan' => 'Admin menandai kembali laporan sebagai Belum Diverifikasi.',
                ]);
            });
        }

        return redirect()->route('admin.laporan.show', $pengajuan)
            ->with('success', "Status laporan #{$pengajuan->id} berhasil diubah kembali ke Belum Diverifikasi.");
    }

    /**
     * Menandai laporan sebagai Verifikasi secara manual.
     */
    public function verifikasi(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $admin = $request->user();

        if ($pengajuan->status !== 'verifikasi') {
            $statusLama = $pengajuan->status;
            $subStatusLama = $pengajuan->sub_status;

            DB::transaction(function () use ($pengajuan, $admin, $statusLama, $subStatusLama) {
                $pengajuan->update([
                    'status' => 'verifikasi',
                    'sub_status' => null,
                ]);

                RiwayatStatus::create([
                    'pengajuan_id' => $pengajuan->id,
                    'diubah_oleh' => $admin->id,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => 'verifikasi',
                    'sub_status_sebelumnya' => $subStatusLama,
                    'sub_status_baru' => null,
                    'catatan' => 'Admin memverifikasi laporan.',
                ]);

                if (! $pengajuan->is_anonim && $pengajuan->user_id) {
                    Notifikasi::create([
                        'user_id' => $pengajuan->user_id,
                        'pengajuan_id' => $pengajuan->id,
                        'judul' => 'Status Laporan Diperbarui',
                        'pesan' => "Status laporan #{$pengajuan->id} telah diubah menjadi: Verifikasi",
                        'tipe' => 'status_update',
                    ]);
                }
            });
        }

        return back()->with('success', "Status laporan #{$pengajuan->id} berhasil diubah menjadi: Verifikasi.");
    }

    /**
     * Mengambil dan memfilter data rekapitulasi pengaduan.
     *
     * @return array{laporan: Collection, ringkasan: array<string, int>, kategoris: Collection, filter: array<string, mixed>}
     */
    protected function getRekapData(Request $request): array
    {
        $kategoris = Kategori::with('parent')->orderBy('nama_kategori')->get();
        $query = Pengajuan::with(['kategori.parent', 'user.unit', 'assignedUnit', 'petugas']);

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->query('dari_tanggal'));
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->query('sampai_tanggal'));
        }

        if ($request->filled('assigned_unit_id')) {
            $query->where('assigned_unit_id', $request->query('assigned_unit_id'));
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->query('kategori_id'));
        }

        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'belum_verifikasi') {
                $query->where('status', 'belum_verifikasi');
            } elseif (in_array($status, ['diajukan', 'menunggu', 'verifikasi'], true)) {
                $query->whereIn('status', ['diajukan', 'menunggu', 'verifikasi']);
            } elseif ($status === 'selesai_setelah_tinjau_ulang') {
                $query->where('status', 'selesai')->where('sub_status', 'selesai_setelah_tinjau_ulang');
            } elseif (in_array($status, ['selesai', 'selesai_ditangani', 'selesai_ditolak', 'ditolak'], true)) {
                $query->whereIn('status', ['selesai', 'ditolak']);
            } elseif (in_array($status, ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi'], true)) {
                $query->where('status', 'diproses')
                    ->whereIn('sub_status', ['identifikasi_klarifikasi', 'identifikasi', 'klarifikasi']);
            } elseif (in_array($status, ['rencana_aksi', 'aksi'], true)) {
                $query->where('status', 'diproses')->where('sub_status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('sub_status')) {
            $query->where('sub_status', $request->query('sub_status'));
        }

        $laporan = $query->latest()->get();

        $selesaiTotal = $laporan->whereIn('status', ['selesai', 'ditolak'])->count();

        $ringkasan = [
            'total' => $laporan->count(),
            'belum_verifikasi' => $laporan->where('status', 'belum_verifikasi')->count(),
            'diajukan' => $laporan->whereIn('status', ['belum_verifikasi', 'diajukan', 'menunggu', 'verifikasi'])->count(),
            'verifikasi' => $laporan->whereIn('status', ['diajukan', 'menunggu', 'verifikasi'])->count(),
            'diproses' => $laporan->where('status', 'diproses')->count(),
            'selesai' => $selesaiTotal,
            'selesai_ditangani' => $selesaiTotal,
            'selesai_ditolak' => 0,
            'ditolak' => 0,
            'anonim' => $laporan->where('is_anonim', true)->count(),
            'identitas' => $laporan->where('is_anonim', false)->count(),
        ];

        return [
            'laporan' => $laporan,
            'ringkasan' => $ringkasan,
            'kategoris' => $kategoris,
            'filter' => $request->all(),
        ];
    }

    /**
     * Menampilkan halaman rekapitulasi dan laporan statistik.
     */
    public function rekap(Request $request): View
    {
        return view('admin.rekap', $this->getRekapData($request));
    }

    /**
     * Mencetak laporan rekapitulasi ke format PDF menggunakan mPDF.
     */
    public function rekapPdf(Request $request, PdfService $pdfService): Response
    {
        $data = $this->getRekapData($request);
        $html = view('admin.rekap_pdf', $data)->render();
        $filename = 'rekap_laporan_komplain_'.date('Ymd_His').'.pdf';

        return $pdfService->render($html, $filename, 'L', 'A4');
    }

    /**
     * Mencetak lembar detail pengaduan ke format PDF menggunakan mPDF.
     */
    public function laporanPdf(Pengajuan $pengajuan, PdfService $pdfService): Response
    {
        $pengajuan->load([
            'kategori.parent',
            'user.unit',
            'lampirans',
            'riwayatStatuses.user',
            'tanggapans.user',
        ]);

        $isAnonimUnlocked = (bool) session('unlocked_anonim_'.$pengajuan->id, false);

        $html = view('admin.laporan_pdf', [
            'pengajuan' => $pengajuan,
            'isAnonimUnlocked' => $isAnonimUnlocked,
        ])->render();

        $filename = 'lembar_pengaduan_'.$pengajuan->id.'_'.date('Ymd_His').'.pdf';

        return $pdfService->render($html, $filename, 'P', 'A4');
    }
}
