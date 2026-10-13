<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPegawaiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekPengajuanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\TanggapanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Cek Pengajuan (Tiket & Token tanpa login)
Route::get('/cek-pengajuan', [CekPengajuanController::class, 'index'])->name('cek-pengajuan.index');
Route::post('/cek-pengajuan', [CekPengajuanController::class, 'check'])->name('cek-pengajuan.check');

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
    Route::get('/dashboard', [PengajuanController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/sukses', [PengajuanController::class, 'sukses'])->name('pengajuan.sukses');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
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
    Route::patch('/laporan/{pengajuan}/status', [AdminDashboardController::class, 'updateStatus'])->name('laporan.status');
    Route::post('/laporan/{pengajuan}/tanggapan', [TanggapanController::class, 'store'])->name('laporan.tanggapan');

    // Rekapitulasi Laporan
    Route::get('/rekap', [AdminDashboardController::class, 'rekap'])->name('rekap');

    // Kelola Master Kategori
    Route::resource('kategori', KategoriController::class)->except(['show']);

    // Kelola Akun Pegawai
    Route::resource('pegawai', AdminPegawaiController::class)->parameters(['pegawai' => 'user'])->except(['show']);
    Route::patch('/pegawai/{user}/toggle-status', [AdminPegawaiController::class, 'toggleStatus'])->name('pegawai.toggle-status');
});
