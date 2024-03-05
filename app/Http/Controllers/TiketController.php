<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungKeluar;
use App\Models\PengunjungMasuk;
use App\Traits\ApiResponder;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = date('Y-m-d');
            $pengunjungMasuks = PengunjungMasuk::whereDate('start_tiket', $tanggal)->latest()->get();

            if ($request->input("mode") == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('durasi', function ($pengunjungMasuk) {
                        if ($pengunjungMasuk->start_tiket) {
                            if ($pengunjungMasuk->pengunjungKeluar) {
                                return '<span class="badge bg-danger"><i class="ti ti-clock me-1"></i> Sudah Selesai</span>';
                            } else {
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

                                return '<span id="' . $spanId . '" class="badge bg-primary rounded-3 fw-semibold" data-sisa="' . $pengunjungMasuk->duration_difference . '"><i class="ti ti-clock me-1"></i>' . $pengunjungMasuk->duration_difference . '</span>';
                            }
                        } else {
                            return '<span class="badge bg-danger"><i class="ti ti-clock me-1"></i> Belum Mulai</span>';
                        }
                    })
                    ->addColumn('tiket', function ($pengunjungMasuk) {
                        return '<a class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungMasuk->uuid . '"><i class="ti ti-ticket me-1"></i> Tiket </a>';
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

        if ($pengunjungMasuk) {
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

            return view('admin.tiket.pengunjung-masuk', compact('pengunjungMasuk'));
        } else {
            $pengunjungKeluar = PengunjungKeluar::where('uuid', $uuid)->first();
            return view('admin.tiket.pengunjung-keluar', compact('pengunjungKeluar'));
        }

    }

    public function getTiketNow(Request $request)
    {
        if ($request->ajax()) {
            $pengunjungMasuk = PengunjungMasuk::where('start_tiket', null)
                ->where('created_at', '>=', now()->subHours(6))
                ->latest()
                ->first();

            if ($pengunjungMasuk) {
                $pengunjungMasuk->label = 'Pengunjung Masuk';
            } else {
                $pengunjungMasuk = PengunjungKeluar::where('created_at', '>=', now()->subHours(6))
                    ->latest()
                    ->first();
                $pengunjungMasuk->label = 'Pengunjung Keluar';
            }

            return $this->successResponse($pengunjungMasuk, 'Data tiket ditemukan.');
        }

        return view('admin.tiket.now');
    }

}
