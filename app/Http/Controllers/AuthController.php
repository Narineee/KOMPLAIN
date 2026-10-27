<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan formulir login untuk Administrator dan Pengadu.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('pegawai.dashboard');
        }

        $defaultRole = $request->query('role', 'pengadu');
        if (! in_array($defaultRole, ['admin', 'pengadu'], true)) {
            $defaultRole = 'pengadu';
        }

        return view('auth.login', [
            'defaultRole' => $defaultRole,
        ]);
    }

    /**
     * Memproses permintaan autentikasi pengguna (Satu Pintu Login Terpadu).
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role_target' => ['nullable', 'string'],
        ], [
            'login.required' => 'Email atau NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($validated['login']);
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';

        $user = User::where($fieldType, $login)->first();

        if (! $user) {
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Akun dengan Email atau NIP tersebut tidak ditemukan.',
            ]);
        }

        if (! $user->is_aktif) {
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        if (! Auth::attempt([$fieldType => $login, 'password' => $validated['password']], $request->boolean('remember'))) {
            return back()->withInput($request->only('login'))->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', "Selamat datang kembali, {$user->name} (Administrator)!");
        }

        return redirect()->intended(route('pegawai.dashboard'))
            ->with('status', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
