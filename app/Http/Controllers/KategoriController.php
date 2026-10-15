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
     * Menampilkan daftar semua kategori dengan hierarki dan filter.
     */
    public function index(Request $request): View
    {
        $query = Kategori::with(['parent', 'children'])
            ->withCount('pengajuans');

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_kategori', 'like', "%{$keyword}%")
                    ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        if ($request->query('tipe') === 'parent') {
            $query->whereNull('parent_id');
        } elseif ($request->query('tipe') === 'child') {
            $query->whereNotNull('parent_id');
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->query('status') === 'aktif');
        }

        $kategoris = $query->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, nama_kategori')
            ->paginate(15)
            ->withQueryString();

        return view('admin.kategori.index', [
            'kategoris' => $kategoris,
            'filter' => $request->all(),
        ]);
    }

    /**
     * Menampilkan formulir tambah kategori baru.
     */
    public function create(): View
    {
        $parentKategoris = Kategori::whereNull('parent_id')
            ->where('aktif', true)
            ->orderBy('nama_kategori')
            ->get();

        return view('admin.kategori.create', [
            'parentKategoris' => $parentKategoris,
        ]);
    }

    /**
     * Menyimpan kategori baru (kategori utama atau sub-kategori).
     */
    public function store(Request $request): RedirectResponse
    {
        $parentId = $request->filled('parent_id') ? $request->input('parent_id') : null;

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama_kategori')->where(fn ($query) => $query->where('parent_id', $parentId)),
            ],
            'parent_id' => ['nullable', 'exists:kategori,id'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'aktif' => ['boolean'],
        ], [
            'nama.required' => 'Nama klasifikasi pengaduan wajib diisi.',
            'nama.unique' => 'Nama klasifikasi ini sudah terdaftar pada tingkat hierarki yang sama.',
            'parent_id.exists' => 'Klasifikasi induk yang dipilih tidak valid.',
        ]);

        Kategori::create([
            'nama_kategori' => $validated['nama'],
            'parent_id' => $validated['parent_id'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Klasifikasi pengaduan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit kategori.
     */
    public function edit(Kategori $kategori): View
    {
        $parentKategoris = Kategori::whereNull('parent_id')
            ->where('id', '!=', $kategori->id)
            ->orderBy('nama_kategori')
            ->get();

        return view('admin.kategori.edit', [
            'kategori' => $kategori,
            'parentKategoris' => $parentKategoris,
        ]);
    }

    /**
     * Memperbarui data kategori dan hierarkinya.
     */
    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $parentId = $request->filled('parent_id') ? $request->input('parent_id') : null;

        if ($parentId && (int) $parentId === (int) $kategori->id) {
            return back()->withInput()->with('error', 'Klasifikasi tidak dapat menjadi induk bagi dirinya sendiri.');
        }

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama_kategori')
                    ->where(fn ($query) => $query->where('parent_id', $parentId))
                    ->ignore($kategori->id),
            ],
            'parent_id' => ['nullable', 'exists:kategori,id'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'aktif' => ['boolean'],
        ], [
            'nama.required' => 'Nama klasifikasi pengaduan wajib diisi.',
            'nama.unique' => 'Nama klasifikasi ini sudah terdaftar pada tingkat hierarki yang sama.',
            'parent_id.exists' => 'Klasifikasi induk yang dipilih tidak valid.',
        ]);

        $kategori->update([
            'nama_kategori' => $validated['nama'],
            'parent_id' => $validated['parent_id'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Klasifikasi pengaduan berhasil diperbarui.');
    }

    /**
     * Menghapus atau menonaktifkan kategori.
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        $hasPengajuan = $kategori->pengajuans()->exists();

        $hasChildPengajuan = false;
        foreach ($kategori->children as $child) {
            if ($child->pengajuans()->exists()) {
                $hasChildPengajuan = true;
                break;
            }
        }

        if ($hasPengajuan || $hasChildPengajuan) {
            $kategori->update(['aktif' => false]);
            $kategori->children()->update(['aktif' => false]);

            return back()->with('info', 'Klasifikasi tidak dapat dihapus permanen karena memiliki data laporan terkait. Klasifikasi telah dinonaktifkan.');
        }

        $kategori->children()->delete();
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Klasifikasi pengaduan berhasil dihapus.');
    }
}
