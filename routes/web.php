<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\TiketController;
use Illuminate\Support\Facades\Artisan;
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
Route::get('e-tiket', [TiketController::class, 'index'])->name('eTiket.index');
Route::get('e-tiket/{uuid}', [TiketController::class, 'show'])->name('eTiket.show');
Route::get('get-tiket-now', [TiketController::class, 'getTiketNow'])->name('eTiket.getTiketNow');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::match(['get', 'post'], 'ubah-tarif', [PengaturanController::class, 'ubahTarif'])->name('ubahTarif');
    Route::match(['get', 'post'], 'toleransi-waktu', [PengaturanController::class, 'toleransiWaktu'])->name('toleransiWaktu');
    Route::match(['get', 'post'], 'pengunjung-masuk', [PengunjungController::class, 'pengunjungMasuk'])->name('pengunjungMasuk');
    Route::match(['get', 'post'], 'pengunjung-murid', [PengunjungController::class, 'pengunjungMurid'])->name('pengunjungMurid');
    Route::post('konfirmasi-pengunjung/{id}', [PengunjungController::class, 'konfirmasiPengunjung'])->name('konfirmasiPengunjung');
    Route::get('pengunjung-masuk/{id}', [PengunjungController::class, 'getPengunjungMasuk'])->name('getPengunjungMasuk');
    Route::get('extra-time/{uuid}', [PengunjungController::class, 'extraTime'])->name('extraTime');
    Route::post('extra-time/{uuid}', [PengunjungController::class, 'extraTimeUpdate'])->name('extraTimeUpdate');
    Route::get('riwayat-pengunjung-masuk', [PengunjungController::class, 'riwayatPengunjungMasuk'])->name('riwayatPengunjungMasuk');
    Route::match(['get', 'post'], 'pengunjung-keluar', [PengunjungController::class, 'pengunjungKeluar'])->name('pengunjungKeluar');
    Route::get('riwayat-pengunjung-keluar', [PengunjungController::class, 'riwayatPengunjungKeluar'])->name('riwayatPengunjungKeluar');
    Route::get('laporan-keuangan', [KeuanganController::class, 'index'])->name('laporanKeuangan');
    Route::resource('user-management', PengaturanController::class)->names('userManagement');
    Route::resource('daftar-bank', PembayaranController::class)->names('daftarBank');
    Route::resource('murid', MuridController::class)->names('murid');
    Route::post('cek-murid', [MuridController::class, 'checkMurid'])->name('checkMurid');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');

    return 'Storage link created!';
});