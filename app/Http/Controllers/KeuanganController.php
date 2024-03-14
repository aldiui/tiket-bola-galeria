<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;
use App\Traits\ApiResponder;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $pengunjungMasuks = PengunjungMasuk::with('user')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->latest()->get();
        if ($request->ajax()) {
            if ($request->mode == "datatable") {
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
                        return '<span class="badge bg-primary rounded-3 fw-semibold"><i class="ti ti-clock me-1"></i>' . $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->rawColumns(['admin', 'tanggal', 'pembayaran', 'durasi'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "single") {
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

        if ($request->mode == "pdf") {
            $bulanTahun = Carbon::create($tahun, $bulan, 1)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('F Y');
            $pdf = PDF::loadView('admin.keuangan.pdf', compact('pengunjungMasuks', 'bulanTahun'));

            $options = [
                'margin_top' => 20,
                'margin_right' => 20,
                'margin_bottom' => 20,
                'margin_left' => 20,
            ];

            $pdf->setOptions($options);
            $pdf->setPaper('legal', 'landscape');

            $namaFile = 'laporan_keuangan.pdf';

            ob_end_clean();
            ob_start();
            return $pdf->stream($namaFile);
        }

        if (!getPermission('laporan_keuangan')) {return redirect()->route('dashboard');}

        return view('admin.keuangan.index');
    }
}
