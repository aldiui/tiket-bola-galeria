<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;
use DataTables;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
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
            }
        }
        getPermission('laporan_keuangan');
        return view('admin.keuangan.index');
    }
}
