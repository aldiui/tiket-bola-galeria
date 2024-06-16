<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
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
        $pembayaranId = $request->pembayaran_id;

        $pengunjungMasuks = [];

        if ($pembayaranId == "Semua") {
            $pengunjungMasuks = PengunjungMasuk::with('user', 'pembayaran')
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->latest()
                ->get();
        } else if ($pembayaranId == "Cash") {
            $pengunjungMasuks = PengunjungMasuk::with('user', 'pembayaran')
                ->whereNull('pembayaran_id')
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->latest()
                ->get();
        } else {
            $pengunjungMasuks = PengunjungMasuk::with('user', 'pembayaran')
                ->where('pembayaran_id', $pembayaranId)
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->latest()
                ->get();
        }

        if ($request->ajax()) {
            if ($request->mode == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('admin', function ($pengunjungMasuk) {
                        return $pengunjungMasuk->user->nama;
                    })
                    ->addColumn('tanggal', function ($pengunjungMasuk) {
                        return formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s');
                    })
                    ->addColumn('metode_pembayaran', function ($pengunjungMasuk) {
                        return $pengunjungMasuk->pembayaran_id ? $pengunjungMasuk->pembayaran->nama_bank . ' - ' . $pengunjungMasuk->pembayaran->nama_akun : 'Cash';
                    })
                    ->addColumn('pembayaran', function ($pengunjungMasuk) {
                        $total = $pengunjungMasuk->durasi_extra
                        ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra
                        : $pengunjungMasuk->tarif;
                        $totalSemua =
                        $total +
                        $pengunjungMasuk->denda +
                        $pengunjungMasuk->biaya_mengantar +
                        $pengunjungMasuk->biaya_kaos_kaki;
                        return formatRupiah($totalSemua);
                    })
                    ->addColumn('diskon', function ($pengunjungMasuk) {
                        return formatRupiah($pengunjungMasuk->nominal_diskon);
                    })
                    ->addColumn('total', function ($pengunjungMasuk) {
                        $total = $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra : $pengunjungMasuk->tarif;
                        $totalAkhir = $total - $pengunjungMasuk->nominal_diskon + $pengunjungMasuk->denda + $pengunjungMasuk->biaya_mengantar + $pengunjungMasuk->biaya_kaos_kaki;
                        return formatRupiah($totalAkhir);
                    })
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        return '<span class="badge bg-primary rounded-3 fw-semibold"><i class="ti ti-clock me-1"></i>' . $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->rawColumns(['admin', 'tanggal', 'pembayaran', 'durasi', 'metode_pembayaran', 'diskon', 'total'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "single") {

                $query = PengunjungMasuk::whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai]);

                if ($pembayaranId == "Cash") {
                    $query->whereNull('pembayaran_id');
                } elseif ($pembayaranId != "Semua") {
                    $query->where('pembayaran_id', $pembayaranId);
                }

                $keuanganData = $query->select([
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(tarif) + SUM(tarif_extra) - SUM(nominal_diskon) + SUM(denda) + SUM(biaya_mengantar) + SUM(biaya_kaos_kaki) as total_tarif'),
                ])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->pluck('total_tarif', 'date');

                $labels = [];
                $dataKeuangan = [];
                $dates = Carbon::parse($tanggalMulai);

                while ($dates <= Carbon::parse($tanggalSelesai)) {
                    $dateString = $dates->toDateString();
                    $labels[] = formatTanggal($dateString, 'd');
                    $dataKeuangan[] = $keuanganData[$dateString] ?? 0;
                    $dates->addDay();
                }

                $pembayaranSum = $pengunjungMasuks->sum('tarif') +
                $pengunjungMasuks->sum('tarif_extra') +
                $pengunjungMasuks->sum('denda') +
                $pengunjungMasuks->sum('biaya_mengantar') +
                $pengunjungMasuks->sum('biaya_kaos_kaki');

                $diskonSum = $pengunjungMasuks->sum('nominal_diskon');

                $totalSum = $pengunjungMasuks->sum('tarif') +
                $pengunjungMasuks->sum('tarif_extra') -
                $diskonSum +
                $pengunjungMasuks->sum('denda') +
                $pengunjungMasuks->sum('biaya_mengantar') +
                $pengunjungMasuks->sum('biaya_kaos_kaki');

                return $this->successResponse([
                    'labels' => $labels,
                    'data' => $dataKeuangan,
                    'pembayaran' => formatRupiah($pembayaranSum),
                    'diskon' => formatRupiah($diskonSum),
                    'total' => formatRupiah($totalSum),
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

        $pembayaran = Pembayaran::all();
        return view('admin.keuangan.index', compact('pembayaran'));
    }
}
