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
        return view('admin.dashboard.index', compact('countDayPengunjungMasuk', 'countDayPengunjungKeluar'));
    }
}
