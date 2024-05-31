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
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        $pengunjungMasuks = PengunjungMasuk::with('user')->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])->latest()->get();
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
                        return formatRupiah($pengunjungMasuk->durasi_extra ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra : $pengunjungMasuk->tarif);
                    })
                    ->addColumn('diskon', function ($pengunjungMasuk) {
                        return formatRupiah($pengunjungMasuk->diskon);
                    })
                    ->addColumn('total', function ($pengunjungMasuk) {
                        $total = $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra : $pengunjungMasuk->tarif;
                        $total = $total - $pengunjungMasuk->diskon ?? 0;
                        return formatRupiah($total);
                    })
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        return '<span class="badge bg-primary rounded-3 fw-semibold"><i class="ti ti-clock me-1"></i>' . $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->rawColumns(['admin', 'tanggal', 'pembayaran', 'durasi'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "single") {

                $keuanganData = PengunjungMasuk::whereBetween('created_at', [$tanggalMulai, $tanggalSelesai])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get([
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(tarif + tarif_extra) as total_tarif'),
                    ])
                    ->pluck('total_tarif', 'date');

                $dates = Carbon::parse($tanggalMulai);
                $labels = [];
                $dataKeuangan = [];

                while ($dates <= Carbon::parse($tanggalSelesai)) {
                    $dateString = $dates->toDateString();
                    $labels[] = formatTanggal($dateString, 'd');
                    $dataKeuangan[] = $keuanganData[$dateString] ?? 0;
                    $dates->addDay();
                }

                return $this->successResponse([
                    'labels' => $labels,
                    'data' => $dataKeuangan,
                    'pembayaran' => formatRupiah($pengunjungMasuks->sum('tarif') + $pengunjungMasuks->sum('tarif_extra')),
                    'diskon' => formatRupiah($pengunjungMasuks->sum('diskon')),
                    'total' => formatRupiah($pengunjungMasuks->sum('tarif') + $pengunjungMasuks->sum('tarif_extra') - $pengunjungMasuks->sum('diskon')),

                ], 'Data laporan keuangan ditemukan.');
            }
        }

        if ($request->mode == "pdf") {
            $pdf = PDF::loadView('admin.keuangan.pdf', compact('pengunjungMasuks', 'tanggalMulai', 'tanggalSelesai'));

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
