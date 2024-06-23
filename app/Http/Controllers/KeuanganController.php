<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\PengunjungMasuk;
use App\Models\TransaksiMembership;
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
        $transaksiMember = [];

        if ($pembayaranId == "Semua") {
            $pengunjungMasuks = PengunjungMasuk::with('user', 'pembayaran')
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->latest()
                ->get();
            $transaksiMember = TransaksiMembership::with('membership', 'pembayaran', 'paketMembership', 'user')
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->get();
        } else {
            $pengunjungMasuks = PengunjungMasuk::with('user', 'pembayaran')
                ->where('pembayaran_id', $pembayaranId)
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->latest()
                ->get();
            $transaksiMember = TransaksiMembership::with('membership', 'pembayaran', 'paketMembership', 'user')
                ->where('pembayaran_id', $pembayaranId)
                ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                ->get();
        }

        $mergedResults = $pengunjungMasuks->merge($transaksiMember);
        $sortedResults = $mergedResults->sortByDesc('created_at');
        $finalResults = $sortedResults->values()->all();

        if ($request->ajax()) {
            if ($request->mode == "datatable") {
                return DataTables::of($finalResults)
                    ->addColumn('nama_anak', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            return $pengunjungMasuk->nama_anak;
                        } else {
                            return $pengunjungMasuk->membership->nama_anak;
                        }
                    })
                    ->addColumn('nama_orang_tua', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            return $pengunjungMasuk->nama_orang_tua;
                        } else {
                            return $pengunjungMasuk->membership->nama_orang_tua;
                        }
                    })
                    ->addColumn('admin', function ($pengunjungMasuk) {
                        return $pengunjungMasuk->user->nama;
                    })
                    ->addColumn('tanggal', function ($pengunjungMasuk) {
                        return formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s');
                    })
                    ->addColumn('metode_pembayaran', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->pembayaran_id) {
                            return $pengunjungMasuk->pembayaran->nama_bank;
                        } else {
                            return $pengunjungMasuk->type;
                        }
                    })
                    ->addColumn('pembayaran', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            $total = $pengunjungMasuk->durasi_extra
                            ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra
                            : $pengunjungMasuk->tarif;
                            $totalSemua = $total + $pengunjungMasuk->denda + $pengunjungMasuk->biaya_mengantar + $pengunjungMasuk->biaya_kaos_kaki;
                            return formatRupiah($totalSemua);
                        } else {
                            return formatRupiah($pengunjungMasuk->nominal);
                        }
                    })
                    ->addColumn('diskon', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            return formatRupiah($pengunjungMasuk->nominal_diskon);
                        } else {
                            return formatRupiah(0);
                        }
                    })
                    ->addColumn('total', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            $total = $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra : $pengunjungMasuk->tarif;
                            $totalAkhir = $total - $pengunjungMasuk->nominal_diskon + $pengunjungMasuk->denda + $pengunjungMasuk->biaya_mengantar + $pengunjungMasuk->biaya_kaos_kaki;
                            return formatRupiah($totalAkhir);
                        } else {
                            return formatRupiah($pengunjungMasuk->nominal);
                        }
                    })
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            $durasi = $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain;
                            return '<span class="badge bg-primary rounded-3 fw-semibold"><i class="ti ti-clock me-1"></i>' . $durasi . ' Jam</span>';
                        } else {
                            return $pengunjungMasuk->paketMembership->nama;
                        }
                    })
                    ->addColumn('type', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->type) {
                            return $pengunjungMasuk->type;
                        } else {
                            return "Membership";
                        }
                    })
                    ->rawColumns(['admin', 'tanggal', 'pembayaran', 'durasi', 'metode_pembayaran', 'diskon', 'total'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "single") {
                $pengunjungMasuks = PengunjungMasuk::whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                    ->when($pembayaranId == "Cash", function ($query) {
                        return $query->whereNull('pembayaran_id');
                    })
                    ->when($pembayaranId != "Semua" && $pembayaranId != "Cash", function ($query) use ($pembayaranId) {
                        return $query->where('pembayaran_id', $pembayaranId);
                    })
                    ->select([
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(tarif) + SUM(tarif_extra) + SUM(denda) + SUM(biaya_mengantar) + SUM(biaya_kaos_kaki) as total_tarif'),
                        DB::raw('SUM(nominal_diskon) as total_diskon'),
                    ])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->get();

                $transaksiMember = TransaksiMembership::whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
                    ->when($pembayaranId != "Semua", function ($query) use ($pembayaranId) {
                        return $query->where('pembayaran_id', $pembayaranId);
                    })
                    ->select([
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(nominal) as total_nominal'),
                    ])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->get();

                $mergedResults = $pengunjungMasuks->map(function ($item) use ($transaksiMember) {
                    $membershipData = $transaksiMember->firstWhere('date', $item->date);
                    $item->total_nominal = $membershipData ? $membershipData->total_nominal : 0;
                    return $item;
                });

                $labels = [];
                $dataKeuangan = [];
                $dates = Carbon::parse($tanggalMulai);

                while ($dates <= Carbon::parse($tanggalSelesai)) {
                    $dateString = $dates->toDateString();
                    $labels[] = formatTanggal($dateString, 'd');

                    $dailyData = $mergedResults->firstWhere('date', $dateString);
                    $totalTarif = $dailyData ? $dailyData->total_tarif + $dailyData->total_nominal - $dailyData->total_diskon : 0;
                    $dataKeuangan[] = $totalTarif;

                    $dates->addDay();
                }

                $pembayaranSum = $mergedResults->sum('total_tarif') + $mergedResults->sum('total_nominal');
                $diskonSum = $mergedResults->sum('total_diskon');
                $totalSum = $pembayaranSum - $diskonSum;

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
            $pdf = PDF::loadView('admin.keuangan.pdf', compact('finalResults', 'tanggalMulai', 'tanggalSelesai'));

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

        if (!getPermission('laporan_keuangan')) {
            return redirect()->route('dashboard');
        }

        $pembayaran = Pembayaran::all();
        return view('admin.keuangan.index', compact('pembayaran'));
    }
}