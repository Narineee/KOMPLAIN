<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPegawaiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\TanggapanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/
// Rute Utama: Langsung ke formulir login tanpa portal terpisah
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');

// Autentikasi Pegawai & Admin
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Pegawai (Internal)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PengajuanController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengaduan', [PengajuanController::class, 'index']);
    Route::get('/pengaduan/buat', [PengajuanController::class, 'create']);
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::get('/pengajuan/{pengajuan}/pdf', [PengajuanController::class, 'pengajuanPdf'])->name('pengajuan.pdf');
    Route::post('/pengajuan/{pengajuan}/konfirmasi', [PengajuanController::class, 'konfirmasi'])->name('pengajuan.konfirmasi');
    Route::get('/pengajuan/{pengajuan}/keberatan', [PengajuanController::class, 'formKeberatan'])->name('pengajuan.keberatan.form');
    Route::post('/pengajuan/{pengajuan}/keberatan', [PengajuanController::class, 'keberatan'])->name('pengajuan.keberatan');
});

/*
|--------------------------------------------------------------------------
| Rute Administrator
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen & Detail Laporan
    Route::get('/laporan', [AdminDashboardController::class, 'laporanIndex'])->name('laporan.index');
    Route::get('/laporan/{pengajuan}', [AdminDashboardController::class, 'laporanShow'])->name('laporan.show');
    Route::get('/laporan/{pengajuan}/pdf', [AdminDashboardController::class, 'laporanPdf'])->name('laporan.pdf');
    Route::post('/laporan/{pengajuan}/buka-anonim', [AdminDashboardController::class, 'revealAnonim'])->name('laporan.reveal-anonim');
    Route::post('/laporan/{pengajuan}/kunci-anonim', [AdminDashboardController::class, 'lockAnonim'])->name('laporan.lock-anonim');
    Route::patch('/laporan/{pengajuan}/status', [AdminDashboardController::class, 'updateStatus'])->name('laporan.status');
    Route::patch('/laporan/{pengajuan}/tandai-belum-verifikasi', [AdminDashboardController::class, 'tandaiBelumVerifikasi'])->name('laporan.tandai-belum-verifikasi');
    Route::patch('/laporan/{pengajuan}/verifikasi', [AdminDashboardController::class, 'verifikasi'])->name('laporan.verifikasi');
    Route::post('/laporan/{pengajuan}/tanggapan', [TanggapanController::class, 'store'])->name('laporan.tanggapan');

    // Rekapitulasi Laporan
    Route::get('/rekap', [AdminDashboardController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/pdf', [AdminDashboardController::class, 'rekapPdf'])->name('rekap.pdf');

    // Kelola Master Kategori
    Route::resource('kategori', KategoriController::class)->except(['show']);

    // Kelola Akun Pegawai & Pengguna
    Route::resource('pegawai', AdminPegawaiController::class)->parameters(['pegawai' => 'user'])->except(['show']);
    Route::patch('/pegawai/{user}/toggle-status', [AdminPegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');

    // Alias Rute Pengguna
    Route::resource('pengguna', AdminPegawaiController::class)->parameters(['pengguna' => 'user'])->except(['show']);
    Route::patch('/pengguna/{user}/toggle-status', [AdminPegawaiController::class, 'toggleStatus'])->name('pengguna.toggle-status');
});
