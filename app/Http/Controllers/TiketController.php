<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;
use Carbon\Carbon;
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
                        $today = Carbon::now()->format('Y-m-d');
                        $ticketDate = $pengunjungMasuk->created_at->format('Y-m-d');

                        if ($today !== $ticketDate) {
                            $pengunjungMasuk->created_at = Carbon::parse($ticketDate)->startOfDay();
                        }

                        $endTime = $pengunjungMasuk->created_at->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);

                        $now = Carbon::now();
                        $now = $now->isAfter($endTime) ? $endTime : $now;

                        $durationDiff = $now->diff($endTime);

                        $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
                        $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;

                        $spanId = 'countdown_' . $pengunjungMasuk->uuid;

                        return '<span id="' . $spanId . '" class="badge bg-primary rounded-3 fw-semibold" data-sisa="' . $pengunjungMasuk->duration_difference . '">' . $pengunjungMasuk->duration_difference . '</span>';
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
        $pengunjungMasuk = PengunjungMasuk::where('uuid', $uuid)->first();

        if (!$pengunjungMasuk) {
            return redirect()->route('/');
        }

        $today = Carbon::now()->format('Y-m-d');
        $ticketDate = $pengunjungMasuk->created_at->format('Y-m-d');

        if ($today !== $ticketDate) {
            $pengunjungMasuk->created_at = Carbon::parse($ticketDate)->startOfDay();
        }

        $endTime = $pengunjungMasuk->created_at->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);

        $now = Carbon::now();

        $now = $now->isAfter($endTime) ? $endTime : $now;

        $durationDiff = $now->diff($endTime);

        $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
        $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;

        return view('admin.tiket.show', compact('pengunjungMasuk'));
    }
}
