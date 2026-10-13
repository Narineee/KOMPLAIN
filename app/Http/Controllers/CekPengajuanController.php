<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CekPengajuanController extends Controller
{
    /**
     * Menampilkan formulir publik untuk cek pengajuan dengan tiket dan token.
     */
    public function index(): View
    {
        return view('publik.cek_pengajuan');
    }

    /**
     * Memverifikasi nomor tiket dan token rahasia, lalu menampilkan detail pengajuan.
     */
    public function check(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'nomor_tiket' => ['required', 'string', 'max:50'],
            'token' => ['required', 'string', 'max:64'],
        ], [
            'nomor_tiket.required' => 'Nomor tiket wajib diisi.',
            'token.required' => 'Token rahasia wajib diisi.',
        ]);

        $nomorTiket = trim($validated['nomor_tiket']);
        $inputToken = trim($validated['token']);

        $pengajuan = Pengajuan::with(['tokenPengajuan'])
            ->where('nomor_tiket', $nomorTiket)
            ->first();

        if (! $pengajuan || ! $pengajuan->tokenPengajuan) {
            return back()->withInput($request->only('nomor_tiket'))->withErrors([
                'nomor_tiket' => 'Laporan dengan nomor tiket tersebut tidak ditemukan.',
            ]);
        }

        $inputHash = hash('sha256', $inputToken);

        if (! hash_equals($pengajuan->tokenPengajuan->token_hash, $inputHash)) {
            return back()->withInput($request->only('nomor_tiket'))->withErrors([
                'token' => 'Token rahasia tidak cocok dengan nomor tiket ini. Silakan periksa kembali token Anda.',
            ]);
        }

        // Catat waktu akses verifikasi token
        $pengajuan->tokenPengajuan->update([
            'terakhir_digunakan' => now(),
        ]);

        // Eager load data terkait yang diizinkan untuk dilihat oleh publik/pelapor
        $pengajuan->load([
            'kategori',
            'lampirans',
            'riwayatStatuses',
            'tanggapans' => fn ($query) => $query->where('is_internal', false)->with('user:id,name,role')->latest(),
        ]);

        return view('publik.hasil_pengajuan', [
            'pengajuan' => $pengajuan,
        ]);
    }
}
