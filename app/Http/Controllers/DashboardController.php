<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungKeluar;
use App\Models\PengunjungMasuk;

class DashboardController extends Controller
{
    public function index()
    {
        $countDayPengunjungMasuk = PengunjungMasuk::whereDate('created_at', date('Y-m-d'))->count();
        $countDayPengunjungKeluar = PengunjungKeluar::whereDate('created_at', date('Y-m-d'))->count();
        $countMounthPengungjungMasuk = PengunjungMasuk::whereMonth('created_at', date('m'))->count();
        $countMounthPengungjungKeluar = PengunjungKeluar::whereMonth('created_at', date('m'))->count();
        return view('admin.dashboard.index', compact('countDayPengunjungMasuk', 'countDayPengunjungKeluar', 'countMounthPengungjungMasuk', 'countMounthPengungjungKeluar'));
    }
}
