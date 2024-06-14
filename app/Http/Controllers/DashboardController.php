<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungKeluar;
use App\Models\PengunjungMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $countDayPengunjungMasuk = PengunjungMasuk::whereDate('created_at', date('Y-m-d'))->count();
        $countDayPengunjungKeluar = PengunjungKeluar::whereDate('created_at', date('Y-m-d'))->count();
        $startDate = Carbon::now()->subDays(7)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $pengunjungMasukData = PengunjungMasuk::whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
            ])
            ->pluck('count', 'date');

        $pengunjungKeluarData = PengunjungKeluar::whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
            ])
            ->pluck('count', 'date');

        $dates = Carbon::parse($startDate);
        $labels = [];
        $dataMasuk = [];
        $dataKeluar = [];

        while ($dates <= $endDate) {
            $dateString = $dates->toDateString();
            $labels[] = formatTanggal($dateString, 'l');
            $dataMasuk[] = $pengunjungMasukData[$dateString] ?? 0;
            $dataKeluar[] = $pengunjungKeluarData[$dateString] ?? 0;
            $dates->addDay();
        }

        return view('admin.dashboard.index', compact('countDayPengunjungMasuk', 'countDayPengunjungKeluar', 'labels', 'dataMasuk', 'dataKeluar'));
    }
}