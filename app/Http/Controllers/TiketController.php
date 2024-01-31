<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;
use DataTables;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = date('Y-m-d');
            $pengunjungMasuks = PengunjungMasuk::whereDate('created_at', $tanggal)->latest()->get();

            if ($request->input("mode") == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        return '<span class="badge bg-primary rounded-3 fw-semibold">' . $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->addColumn('tiket', function ($pengunjungMasuk) {
                        return '<a class="btn btn-warning" href="/e-tiket/' . $pengunjungMasuk->uuid . '"> Tiket </a>';
                    })
                    ->rawColumns(['durasi', 'tiket'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        return view('admin.tiket.index');
    }

    public function show($uuid)
    {
        $cekTiket = PengunjungMasuk::where('uuid', $uuid)->first();

        if (!$cekTiket) {
            return redirect()->route('/');
        }

        return view('admin.tiket.show', compact('cekTiket'));
    }
}
