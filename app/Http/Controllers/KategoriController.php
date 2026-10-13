<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index(): View
    {
        $kategoris = Kategori::withCount('pengajuans')->orderBy('nama_kategori')->paginate(10);

        return view('admin.kategori.index', [
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Menampilkan formulir tambah kategori baru.
     */
    public function create(): View
    {
        return view('admin.kategori.create');
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150', 'unique:kategori,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'aktif' => ['boolean'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        Kategori::create([
            'nama' => $validated['nama'],
            'nama_kategori' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit kategori.
     */
    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.edit', [
            'kategori' => $kategori,
        ]);
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150', Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id)],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'aktif' => ['boolean'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        $kategori->update([
            'nama' => $validated['nama'],
            'nama_kategori' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus atau menonaktifkan kategori.
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->pengajuans()->exists()) {
            // Jika sudah ada laporan terhubung, nonaktifkan saja untuk integritas data
            $kategori->update(['aktif' => false]);

            return back()->with('info', 'Kategori tidak dapat dihapus karena sudah memiliki data laporan terkait. Kategori telah dinonaktifkan.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
