<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\RiwayatStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama admin dengan ringkasan statistik.
     */
    public function index(): View
    {
        $statistik = [
            'total' => Pengajuan::count(),
            'diajukan' => Pengajuan::where('status', 'diajukan')->count(),
            'diproses' => Pengajuan::where('status', 'diproses')->count(),
            'selesai' => Pengajuan::where('status', 'selesai')->count(),
            'ditolak' => Pengajuan::where('status', 'ditolak')->count(),
        ];

        $laporanTerbaru = Pengajuan::with(['kategori', 'user.unit'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'statistik' => $statistik,
            'laporanTerbaru' => $laporanTerbaru,
        ]);
    }

    /**
     * Menampilkan daftar semua laporan dengan filter dan pencarian.
     */
    public function laporanIndex(Request $request): View
    {
        $query = Pengajuan::with(['kategori', 'user.unit'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->query('kategori_id'));
        }

        if ($request->has('is_anonim') && $request->query('is_anonim') !== '') {
            $query->where('is_anonim', (bool) $request->query('is_anonim'));
        }

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor_tiket', 'like', "%{$keyword}%")
                    ->orWhere('judul', 'like', "%{$keyword}%");
            });
        }

        $pengajuans = $query->paginate(15)->withQueryString();
        $kategoris = Kategori::where('aktif', true)->orderBy('nama_kategori')->get();

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
            'kategori',
            'user.unit',
            'lampirans',
            'riwayatStatuses.user',
            'tanggapans.user',
        ]);

        return view('admin.laporan.show', [
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Memperbarui status penanganan laporan.
     */
    public function updateStatus(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,diproses,selesai,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status baru wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $statusLama = $pengajuan->status;
        $statusBaru = $validated['status'];
        $catatan = $validated['catatan'] ?? null;
        $admin = $request->user();

        if ($statusLama === $statusBaru) {
            return back()->with('info', 'Status laporan tidak berubah.');
        }

        DB::transaction(function () use ($pengajuan, $statusLama, $statusBaru, $catatan, $admin) {
            $updateData = ['status' => $statusBaru];

            if ($statusBaru === 'selesai') {
                $updateData['tanggal_selesai'] = now();
            }

            $pengajuan->update($updateData);

            RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => $admin->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
            ]);

            // Kirim notifikasi jika laporan beridentitas
            if (! $pengajuan->is_anonim && $pengajuan->user_id) {
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_id' => $pengajuan->id,
                    'judul' => 'Status Laporan Diperbarui',
                    'pesan' => "Status laporan {$pengajuan->nomor_tiket} telah diubah menjadi: ".ucfirst($statusBaru),
                    'tipe' => 'status_update',
                ]);
            }
        });

        return back()->with('success', "Status laporan berhasil diperbarui menjadi: {$statusBaru}.");
    }

    /**
     * Menampilkan halaman rekapitulasi dan laporan statistik.
     */
    public function rekap(Request $request): View
    {
        $kategoris = Kategori::all();
        $query = Pengajuan::with(['kategori', 'user.unit']);

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
            $query->where('status', $request->query('status'));
        }

        $laporan = $query->latest()->get();

        $ringkasan = [
            'total' => $laporan->count(),
            'diajukan' => $laporan->where('status', 'diajukan')->count(),
            'diproses' => $laporan->where('status', 'diproses')->count(),
            'selesai' => $laporan->where('status', 'selesai')->count(),
            'ditolak' => $laporan->where('status', 'ditolak')->count(),
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
