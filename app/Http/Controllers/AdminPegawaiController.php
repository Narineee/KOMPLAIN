<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPegawaiController extends Controller
{
    /**
     * Menampilkan daftar semua akun pengguna (pegawai & admin).
     */
    public function index(Request $request): View
    {
        $query = User::with('unit')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->query('unit_id'));
        }

        if ($request->filled('status')) {
            $query->where('status_akun', $request->query('status'));
        }

        if ($request->filled('q')) {
            $keyword = trim($request->query('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nip', 'like', "%{$keyword}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.pegawai.index', [
            'users' => $users,
            'units' => $units,
            'filter' => $request->all(),
        ]);
    }

    /**
     * Menampilkan formulir pendaftaran akun pegawai baru.
     */
    public function create(): View
    {
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.pegawai.create', [
            'units' => $units,
        ]);
    }

    /**
     * Menyimpan akun pegawai baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,pegawai,petugas'],
            'unit_id' => [Rule::requiredIf(fn () => $request->input('role') === 'petugas'), 'nullable', 'exists:unit,id'],
            'is_aktif' => ['boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Peran (role) wajib dipilih.',
            'role.in' => 'Peran harus berupa admin, pegawai, atau petugas bidang.',
            'unit_id.required' => 'Bidang tugas wajib dipilih untuk Petugas Bidang.',
            'unit_id.exists' => 'Unit kerja tidak valid.',
        ]);

        User::create([
            'nip' => $validated['nip'],
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'unit_id' => $validated['unit_id'] ?? null,
            'status_akun' => $request->boolean('is_aktif', true) ? 'aktif' : 'nonaktif',
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit akun pegawai.
     */
    public function edit(User $user): View
    {
        $units = Unit::orderBy('nama_unit')->get();

        return view('admin.pegawai.edit', [
            'user' => $user,
            'units' => $units,
        ]);
    }

    /**
     * Memperbarui data akun pegawai.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($user->id)],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,pegawai,petugas'],
            'unit_id' => [Rule::requiredIf(fn () => $request->input('role') === 'petugas'), 'nullable', 'exists:unit,id'],
            'is_aktif' => ['boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Peran (role) wajib dipilih.',
            'role.in' => 'Peran harus berupa admin, pegawai, atau petugas bidang.',
            'unit_id.required' => 'Bidang tugas wajib dipilih untuk Petugas Bidang.',
            'unit_id.exists' => 'Unit kerja tidak valid.',
        ]);

        if ($user->id === auth()->id()) {
            if (! $request->boolean('is_aktif', true)) {
                return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            }
            if ($validated['role'] !== 'admin') {
                return back()->withInput()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
            }
        }

        $updateData = [
            'nip' => $validated['nip'],
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'unit_id' => $validated['unit_id'] ?? null,
            'status_akun' => $request->boolean('is_aktif', true) ? 'aktif' : 'nonaktif',
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan status akun pegawai.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $newStatus = $user->is_aktif ? 'nonaktif' : 'aktif';
        $user->update([
            'status_akun' => $newStatus,
        ]);

        $statusTeks = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status akun {$user->name} berhasil {$statusTeks}.");
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->pengajuans()->exists() || $user->tanggapans()->exists()) {
            $user->update(['status_akun' => 'nonaktif']);

            return back()->with('info', 'Akun tidak dapat dihapus permanen karena memiliki riwayat laporan atau tanggapan terkait. Status akun telah diubah menjadi nonaktif.');
        }

        $user->delete();

        return redirect()->route('admin.pegawai.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
