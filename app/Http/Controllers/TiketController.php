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
                        if ($pengunjungMasuk->start_tiket) {
                            $startTicket = Carbon::parse($pengunjungMasuk->start_tiket);
                            $today = Carbon::now()->format('Y-m-d');
                            $ticketDate = $startTicket->format('Y-m-d');

                            if ($today !== $ticketDate) {
                                $startTicket->startOfDay();
                                $pengunjungMasuk->created_at = $startTicket;
                            }

                            $endTime = $startTicket->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);

                            $now = Carbon::now();
                            $now = $now->isAfter($endTime) ? $endTime : $now;

                            $durationDiff = $now->diff($endTime);

                            $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
                            $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;

                            $spanId = 'countdown_' . $pengunjungMasuk->uuid;

                            return '<span id="' . $spanId . '" class="badge bg-primary rounded-3 fw-semibold" data-sisa="' . $pengunjungMasuk->duration_difference . '">' . $pengunjungMasuk->duration_difference . '</span>';
                        } else {
                            return '<span class="badge bg-danger">Belum Mulai</span>';
                        }
                    })
                    ->addColumn('tiket', function ($pengunjungMasuk) {
                        return '<a class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungMasuk->uuid . '"> Tiket </a>';
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

        if ($pengunjungMasuk->start_tiket) {
            $startTicket = Carbon::parse($pengunjungMasuk->start_tiket);
            $today = Carbon::now()->format('Y-m-d');
            $ticketDate = $startTicket->format('Y-m-d');

            if ($today !== $ticketDate) {
                $pengunjungMasuk->start_tiket = Carbon::parse($ticketDate)->startOfDay();
            }

            $endTime = $startTicket->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);

            $now = Carbon::now();
            $now = $now->isAfter($endTime) ? $endTime : $now;

            $durationDiff = $now->diff($endTime);

            $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
            $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;
        } else {
            $pengunjungMasuk->duration_difference = '00:00:00';
        }

        return view('admin.tiket.show', compact('pengunjungMasuk'));
    }

}
