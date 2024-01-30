<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;
use App\Traits\ApiResponder;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->input("bulan");
            $tahun = $request->input("tahun");

            $pengunjungMasuks = PengunjungMasuk::with('user')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->latest()->get();
            if ($request->input("mode") == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('admin', function ($pengunjungMasuk) {
                        return $pengunjungMasuk->user->nama;
                    })
                    ->addColumn('tanggal', function ($pengunjungMasuk) {
                        return formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s');
                    })
                    ->addColumn('pembayaran', function ($pengunjungMasuk) {
                        return formatRupiah($pengunjungMasuk->tarif);
                    })
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        return '<span class="badge bg-primary rounded-3 fw-semibold">' . $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->rawColumns(['admin', 'tanggal', 'pembayaran', 'durasi'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->input("mode") == "single") {
                $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
                $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

                $keuanganData = PengunjungMasuk::whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get([
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(tarif) as total_tarif'),
                    ])
                    ->pluck('total_tarif', 'date');

                $dates = Carbon::parse($startDate);
                $labels = [];
                $dataKeuangan = [];

                while ($dates <= $endDate) {
                    $dateString = $dates->toDateString();
                    $labels[] = formatTanggal($dateString, 'd');
                    $dataKeuangan[] = $keuanganData[$dateString] ?? 0;
                    $dates->addDay();
                }

                return $this->successResponse([
                    'labels' => $labels,
                    'data' => $dataKeuangan,
                ], 'Data laporan keuangan ditemukan.');
            }
        }

        getPermission('laporan_keuangan');
        return view('admin.keuangan.index');
    }
}
