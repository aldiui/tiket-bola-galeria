<?php

namespace App\Http\Controllers;

use DataTables;
use Carbon\Carbon;
use App\Models\Pengaturan;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Models\PengunjungMasuk;
use App\Models\PengunjungKeluar;
use App\Http\Controllers\Controller;

class TiketController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = date('Y-m-d');
            $pengunjungMasuks = PengunjungMasuk::leftJoin('pengunjung_keluars', 'pengunjung_masuks.id', '=', 'pengunjung_keluars.pengunjung_masuk_id')
                ->whereDate('pengunjung_masuks.start_tiket', $tanggal)
                ->whereNull('pengunjung_keluars.pengunjung_masuk_id')
                ->latest('pengunjung_masuks.created_at')
                ->select('pengunjung_masuks.*')
                ->get();
            $pengaturan = Pengaturan::find(1);

            if ($request->input("mode") == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('durasi', function ($pengunjungMasuk) use ($pengaturan) {
                        if ($pengunjungMasuk->start_tiket) {
                            if ($pengunjungMasuk->pengunjungKeluar) {
                                return '<span style="font-size: 20px" class="badge bg-danger"><i class="ti ti-clock me-1"></i> Sudah Selesai</span>';
                            } else {
                                $startTicket = Carbon::parse($pengunjungMasuk->start_tiket)->addMinutes($pengaturan->toleransi_waktu);
                                $today = Carbon::now()->format('Y-m-d');
                                $ticketDate = $startTicket->format('Y-m-d');

                                if ($today !== $ticketDate) {
                                    $startTicket->startOfDay();
                                    $pengunjungMasuk->created_at = $startTicket;
                                }

                                $endTime = $startTicket->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);
                                if ($pengunjungMasuk->durasi_extra) {
                                    $endTime->addMinutes($pengunjungMasuk->durasi_extra * 60);
                                }
                                $now = Carbon::now();
                                $now = $now->isAfter($endTime) ? $endTime : $now;
                                $durationDiff = $now->diff($endTime);
                                $remainingSeconds = $now->diffInSeconds($endTime, false);

                                $duration_difference = $durationDiff->format('%H:%I:%S');
                                $duration_difference = $duration_difference < '00:00:00' ? '00:00:00' : $duration_difference;

                                $spanId = 'countdown_' . $pengunjungMasuk->uuid;

                                if ($remainingSeconds < 5 * 60) {
                                    $badgeColor = 'bg-warning blink';
                                } elseif ($remainingSeconds < 10 * 60) {
                                    $badgeColor = 'bg-success blink';
                                } else {
                                    $badgeColor = 'bg-primary';
                                }

                                return '<span style="font-size: 20px" id="' . $spanId . '" class="badge ' . $badgeColor . ' rounded-3 fw-semibold" data-sisa="' . $duration_difference . '"><i class="ti ti-clock me-1"></i>' . $duration_difference . '</span>';
                            }
                        } else {
                            return '<span style="font-size: 20px" class="badge bg-danger"><i class="ti ti-clock me-1"></i> Belum Mulai</span>';
                        }
                    })
                    ->addColumn('nama_anak', function ($pengunjungMasuk) {
                        return '<span style="font-size: 20px" class="fw-bolder">' . $pengunjungMasuk->nama_anak . '</span>';
                    })
                    ->rawColumns(['durasi', 'nama_anak'])
                    ->addIndexColumn()
                    ->make(true);
            }

            return $this->successResponse($pengunjungMasuks->count(), 'Data tiket ditemukan.');
        }
        return view('admin.tiket.index');
    }

    public function show($uuid)
    {
        $pengunjungMasuk = PengunjungMasuk::with('pembayaran')->where('uuid', $uuid)->first();

        if ($pengunjungMasuk) {
            if ($pengunjungMasuk->start_tiket) {
                $startTicket = Carbon::parse($pengunjungMasuk->start_tiket);
                $today = Carbon::now()->format('Y-m-d');
                $ticketDate = $startTicket->format('Y-m-d');

                if ($today !== $ticketDate) {
                    $pengunjungMasuk->start_tiket = Carbon::parse($ticketDate)->startOfDay();
                }

                $endTime = $startTicket->copy()->addMinutes($pengunjungMasuk->durasi_bermain * 60);
                if ($pengunjungMasuk->durasi_extra) {
                    $endTime->addMinutes($pengunjungMasuk->durasi_extra * 60);
                }

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
                ->where('created_at', '>=', now()->subMinutes(30))
                ->latest()
                ->first();

            $pengunjungKeluar = PengunjungKeluar::where('created_at', '>=', now()->subMinutes(30))
                ->latest()
                ->first();

            $pengunjungMasukData = new \stdClass();

            if ($pengunjungMasuk && (!$pengunjungKeluar || $pengunjungMasuk->created_at >= $pengunjungKeluar->created_at)) {
                $pengunjungMasuk->label = 'Pengunjung Masuk';
                $pengunjungMasukData = $pengunjungMasuk;
            } else {
                $pengunjungKeluar->label = 'Pengunjung Keluar';
                $pengunjungMasukData = $pengunjungKeluar;
            }

            return $this->successResponse($pengunjungMasukData, 'Data tiket ditemukan.');
        }

        return view('admin.tiket.now');
    }

}