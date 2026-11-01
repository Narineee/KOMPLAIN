<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Menampilkan daftar semua bidang tugas / unit kerja.
     */
    public function index(Request $request): View
    {
        $query = Unit::withCount(['users', 'petugas', 'pengajuans'])->latest();

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_unit', 'like', "%{$keyword}%")
                    ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        $units = $query->paginate(15)->withQueryString();

        return view('admin.unit.index', [
            'units' => $units,
            'filter' => $request->all(),
        ]);
    }

    /**
     * Menampilkan formulir penambahan bidang baru.
     */
    public function create(): View
    {
        return view('admin.unit.create');
    }

    /**
     * Menyimpan data bidang baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:150', 'unique:unit,nama_unit'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_unit.required' => 'Nama bidang tugas wajib diisi.',
            'nama_unit.max' => 'Nama bidang tugas maksimal 150 karakter.',
            'nama_unit.unique' => 'Nama bidang tugas ini sudah terdaftar.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ]);

        Unit::create([
            'nama_unit' => trim($validated['nama_unit']),
            'keterangan' => ! empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('admin.unit.index')->with('success', "Bidang '{$validated['nama_unit']}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan formulir edit bidang.
     */
    public function edit(Unit $unit): View
    {
        return view('admin.unit.edit', [
            'unit' => $unit,
        ]);
    }

    /**
     * Memperbarui data bidang di database.
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:150', Rule::unique('unit', 'nama_unit')->ignore($unit->id)],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_unit.required' => 'Nama bidang tugas wajib diisi.',
            'nama_unit.max' => 'Nama bidang tugas maksimal 150 karakter.',
            'nama_unit.unique' => 'Nama bidang tugas ini sudah digunakan.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ]);

        $unit->update([
            'nama_unit' => trim($validated['nama_unit']),
            'keterangan' => ! empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
        ]);

        return redirect()->route('admin.unit.index')->with('success', "Data bidang '{$unit->nama_unit}' berhasil diperbarui.");
    }

    /**
     * Menghapus bidang kerja jika tidak memiliki relasi pegawai atau aduan.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        $totalUser = $unit->users()->count();
        $totalPengaduan = $unit->pengajuans()->count();

        if ($totalUser > 0 || $totalPengaduan > 0) {
            $pesan = "Bidang '{$unit->nama_unit}' tidak dapat dihapus karena masih terkait dengan ";
            $parts = [];
            if ($totalUser > 0) {
                $parts[] = "{$totalUser} akun pegawai/petugas";
            }
            if ($totalPengaduan > 0) {
                $parts[] = "{$totalPengaduan} pengaduan";
            }
            $pesan .= implode(' dan ', $parts).'. Silakan alihkan data terlebih dahulu.';

            return redirect()->route('admin.unit.index')->with('error', $pesan);
        }

        $namaUnit = $unit->nama_unit;
        $unit->delete();

        return redirect()->route('admin.unit.index')->with('success', "Bidang '{$namaUnit}' berhasil dihapus.");
    }
}
