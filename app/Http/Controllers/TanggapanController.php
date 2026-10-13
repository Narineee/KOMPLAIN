<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\Tanggapan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TanggapanController extends Controller
{
    /**
     * Menyimpan tanggapan atau catatan tindak lanjut dari admin.
     */
    public function store(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'pesan' => ['required', 'string', 'min:3'],
            'is_internal' => ['boolean'],
        ], [
            'pesan.required' => 'Pesan tanggapan wajib diisi.',
            'pesan.min' => 'Pesan tanggapan minimal 3 karakter.',
        ]);

        $isInternal = $request->boolean('is_internal');
        $admin = $request->user();

        $tanggapan = Tanggapan::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $admin->id,
            'pesan' => $validated['pesan'],
            'is_internal' => $isInternal,
        ]);

        // Kirim notifikasi jika bukan catatan internal dan laporan beridentitas
        if (! $isInternal && ! $pengajuan->is_anonim && $pengajuan->user_id) {
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'pengajuan_id' => $pengajuan->id,
                'judul' => 'Tanggapan Baru untuk Laporan Anda',
                'pesan' => "Administrator telah memberikan tanggapan baru pada laporan {$pengajuan->nomor_tiket}.",
                'tipe' => 'tanggapan_baru',
            ]);
        }

        $pesanSukses = $isInternal
            ? 'Catatan internal admin berhasil disimpan.'
            : 'Tanggapan publik untuk laporan berhasil dikirim.';

        return back()->with('success', $pesanSukses);
    }
}
