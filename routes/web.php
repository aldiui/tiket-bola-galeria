<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengunjungController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::match(['get', 'post'], 'ubah-tarif', [PengaturanController::class, 'ubahTarif'])->name('ubahTarif');
    Route::match(['get', 'post'], 'pengunjung-masuk', [PengunjungController::class, 'pengunjungMasuk'])->name('pengunjungMasuk');
    Route::get('riwayat-pengunjung-masuk', [PengunjungController::class, 'riwayatPengunjungMasuk'])->name('riwayatPengunjungMasuk');
    Route::match(['get', 'post'], 'pengunjung-keluar', [PengunjungController::class, 'pengunjungKeluar'])->name('pengunjungKeluar');
    Route::get('riwayat-pengunjung-keluar', [PengunjungController::class, 'riwayatPengunjungKeluar'])->name('riwayatPengunjungKeluar');
    Route::get('laporan-keuangan', [KeuanganController::class, 'index'])->name('laporanKeuangan');
    Route::resource('user-management', PengaturanController::class)->names('userManagement');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});
