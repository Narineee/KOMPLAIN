<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use App\Models\Tanggapan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasPengajuanController extends Controller
{
    /**
     * Memastikan user adalah petugas yang berhak mengakses pengaduan terkait.
     */
    private function pastikanAksesPetugas(User $user, Pengajuan $pengajuan): void
    {
        $berhak = ($pengajuan->petugas_id === $user->id)
            || ($user->unit_id && $pengajuan->assigned_unit_id === $user->unit_id);

        if (! $berhak) {
            abort(403, 'Anda tidak memiliki hak akses untuk menangani laporan pengaduan ini.');
        }
    }

    /**
     * Query dasar untuk pengaduan yang ditugaskan ke petugas ini atau bidangnya.
     */
    private function tugasQuery(User $user)
    {
        return Pengajuan::query()->where(function ($q) use ($user) {
            $q->where('petugas_id', $user->id);
            if ($user->unit_id) {
                $q->orWhere('assigned_unit_id', $user->unit_id);
            }
        });
    }

    /**
     * Menampilkan dashboard tugas pengaduan petugas bidang.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $query = $this->tugasQuery($user);

        $totalTugas = (clone $query)->count();
        $sedangDiproses = (clone $query)->where('status', 'diproses')->count();
        $selesai = (clone $query)->where('status', 'selesai')->count();
        $baruDitugaskan = (clone $query)->whereIn('status', ['verifikasi', 'menunggu', 'belum_verifikasi'])->count();

        $tugasTerbaru = (clone $query)
            ->with(['kategori.parent', 'assignedUnit'])
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', [
            'totalTugas' => $totalTugas,
            'sedangDiproses' => $sedangDiproses,
            'selesai' => $selesai,
            'baruDitugaskan' => $baruDitugaskan,
            'tugasTerbaru' => $tugasTerbaru,
            'unit' => $user->unit,
        ]);
    }

    /**
     * Menampilkan daftar semua pengaduan yang ditugaskan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->tugasQuery($user)->with(['kategori.parent', 'assignedUnit'])->latest();

        $status = $request->query('status');
        if ($status === 'diproses') {
            $query->where('status', 'diproses');
        } elseif ($status === 'selesai') {
            $query->where('status', 'selesai');
        } elseif ($status === 'baru') {
            $query->whereIn('status', ['verifikasi', 'menunggu', 'belum_verifikasi']);
        }

        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sub) use ($q) {
                if (is_numeric($q)) {
                    $sub->where('id', $q);
                }
                $sub->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $pengajuans = $query->paginate(10)->withQueryString();

        return view('petugas.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'statusAktif' => $status,
            'q' => $request->query('q', ''),
        ]);
    }

    /**
     * Menampilkan detail pengaduan yang ditugaskan.
     * Kerahasiaan identitas: jika anonim, relasi user pengadu tidak dimuat/ditampilkan.
     */
    public function show(Request $request, Pengajuan $pengajuan): View
    {
        $user = $request->user();
        $this->pastikanAksesPetugas($user, $pengajuan);

        $withRelations = [
            'kategori.parent',
            'lampirans',
            'tanggapans.user',
            'riwayatStatuses.user',
            'assignedUnit',
            'petugas',
        ];

        // Hanya load data pelapor jika BUKAN anonim
        if (! $pengajuan->is_anonim) {
            $withRelations[] = 'user.unit';
        }

        $pengajuan->load($withRelations);

        return view('petugas.pengajuan.show', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Memperbarui tahapan status penanganan komplain oleh petugas bidang.
     */
    public function updateStatus(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $user = $request->user();
        $this->pastikanAksesPetugas($user, $pengajuan);

        $validated = $request->validate([
            'tahap' => ['required', 'in:identifikasi_klarifikasi,rencana_aksi,aksi,selesai'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'tahap.required' => 'Tahapan penanganan wajib dipilih.',
            'tahap.in' => 'Tahapan penanganan tidak valid.',
        ]);

        $tahap = $validated['tahap'];
        $catatan = $validated['catatan'] ?? null;

        $statusLama = $pengajuan->status;
        $subStatusLama = $pengajuan->sub_status;

        $isDariTinjauUlang = ($pengajuan->jumlah_keberatan ?? 0) > 0
            || $pengajuan->konfirmasi_pelapor === 'keberatan'
            || $pengajuan->sub_status === 'selesai_setelah_tinjau_ulang';

        if ($tahap === 'selesai') {
            $statusBaru = 'selesai';
            $subStatusBaru = $isDariTinjauUlang ? 'selesai_setelah_tinjau_ulang' : null;
        } else {
            $statusBaru = 'diproses';
            $subStatusBaru = $tahap;
        }

        DB::transaction(function () use ($pengajuan, $user, $statusLama, $statusBaru, $subStatusLama, $subStatusBaru, $catatan) {
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

            $labelSub = match ($subStatusBaru) {
                'identifikasi_klarifikasi' => 'Identifikasi / Klarifikasi',
                'rencana_aksi' => 'Rencana Aksi',
                'aksi' => 'Aksi / Tindakan',
                'selesai_setelah_tinjau_ulang' => 'Selesai setelah tinjau ulang',
                default => 'Selesai',
            };

            $labelStatus = $statusBaru === 'selesai' ? $labelSub : "Diproses ({$labelSub})";

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'diubah_oleh' => $user->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'sub_status_sebelumnya' => $subStatusLama,
                'sub_status_baru' => $subStatusBaru,
                'catatan' => $catatan ?: "Penanganan diperbarui oleh Petugas Bidang: {$labelStatus}",
            ]);

            // Notifikasi ke pelapor jika bukan anonim
            if (! $pengajuan->is_anonim && $pengajuan->user_id) {
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_id' => $pengajuan->id,
                    'judul' => 'Perkembangan Penanganan Aduan',
                    'pesan' => "Petugas Bidang telah memperbarui penanganan laporan #{$pengajuan->id} menjadi: {$labelStatus}",
                    'tipe' => 'status_update',
                ]);
            }
        });

        return back()->with('success', 'Status dan tahapan penanganan berhasil diperbarui.');
    }

    /**
     * Menyimpan tanggapan tindak lanjut dari Petugas Bidang.
     */
    public function storeTanggapan(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $user = $request->user();
        $this->pastikanAksesPetugas($user, $pengajuan);

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'min:3'],
            'is_internal' => ['nullable', 'boolean'],
        ], [
            'pesan.required' => 'Pesan tanggapan wajib diisi.',
            'pesan.min' => 'Pesan tanggapan minimal 3 karakter.',
        ]);

        $isInternal = $request->boolean('is_internal');

        Tanggapan::create([
            'pengajuan_id' => $pengajuan->id,
            'admin_id' => $user->id,
            'isi_tanggapan' => $validated['pesan'],
            'is_internal' => $isInternal,
        ]);

        if (! $isInternal && ! $pengajuan->is_anonim && $pengajuan->user_id) {
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'pengajuan_id' => $pengajuan->id,
                'judul' => 'Tanggapan Baru dari Petugas Bidang',
                'pesan' => "Petugas Bidang telah memberikan tanggapan tindak lanjut pada laporan #{$pengajuan->id}.",
                'tipe' => 'tanggapan_baru',
            ]);
        }

        $pesanSukses = $isInternal
            ? 'Catatan internal petugas berhasil disimpan.'
            : 'Tanggapan tindak lanjut berhasil dikirim.';

        return back()->with('success', $pesanSukses);
    }
}
