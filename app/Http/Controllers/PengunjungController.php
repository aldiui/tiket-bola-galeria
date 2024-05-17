<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\PengunjungKeluar;
use App\Models\PengunjungMasuk;
use App\Traits\ApiResponder;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PengunjungController extends Controller
{
    use ApiResponder;

    public function pengunjungMasuk(Request $request)
    {
        if (!getPermission('tambah_pengunjung_masuk')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
                'durasi_bermain' => 'required',
                'metode_pembayaran' => 'required',
                'tarif' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            $uuid = Uuid::uuid4()->toString();

            $baseUrl = config('app.url');
            $redirectUrl = $baseUrl . '/e-tiket/' . $uuid;

            $qrCode = QrCode::format('svg')->size(300)->generate($redirectUrl);
            $qrCodePath = 'public/pengunjung_masuk/' . $uuid . '_qrcode.svg';

            $pengunjungMasuk = PengunjungMasuk::create([
                'uuid' => $uuid,
                'nama_anak' => $request->nama_anak,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->nama_orang_tua,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'metode_pembayaran' => $request->metode_pembayaran,
                'tarif' => $request->tarif,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungMasuk, 'Pengunjung Masuk Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        return view('admin.pengunjung.masuk', compact('pengaturan'));
    }

    public function riwayatPengunjungMasuk(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $pengunjungMasuks = PengunjungMasuk::whereDate('created_at', $tanggal)->latest()->get();
            $toleransiWaktu = Pengaturan::find(1);

            if ($request->mode == "datatable") {
                return DataTables::of($pengunjungMasuks)
                    ->addColumn('durasi', function ($pengunjungMasuk) use ($toleransiWaktu) {
                        if ($pengunjungMasuk->start_tiket) {
                            if ($pengunjungMasuk->start_tiket) {
                                if ($pengunjungMasuk->pengunjungKeluar) {
                                    return '<span class="badge bg-danger"><i class="ti ti-clock me-1"></i> Sudah Selesai</span>';
                                } else {
                                    $startTicket = Carbon::parse($pengunjungMasuk->start_tiket);
                                    $today = Carbon::now()->format('Y-m-d');
                                    $ticketDate = $startTicket->format('Y-m-d');
                                    $waktuToleransi = $toleransiWaktu->toleransi_waktu;

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
                                    $toleransTime = $endTime->addMinutes($waktuToleransi);

                                    $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
                                    $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;

                                    $spanId = 'countdown_' . $pengunjungMasuk->uuid;

                                    if ($remainingSeconds < 5 * 60) {
                                        $badgeColor = 'bg-danger blink';
                                    } elseif ($remainingSeconds < 10 * 60) {
                                        $badgeColor = 'bg-warning blink';
                                    } else {
                                        $badgeColor = 'bg-primary';
                                    }

                                    return '
                                     <span id="' . $spanId . '" class="badge ' . $badgeColor . ' rounded-3 fw-semibold" data-sisa="' . $pengunjungMasuk->duration_difference . '"><i class="ti ti-clock me-1"></i>' . $pengunjungMasuk->duration_difference . '</span>
                                     <div class="mt-2"> Toleransi Waktu : ' . $toleransTime . '</div>'
                                    ;
                                }
                            }
                        } else {
                            return '<span class="badge bg-danger"><i class="ti ti-clock me-1"></i>Belum Mulai</span>';
                        }
                    })
                    ->addColumn('tiket', function ($pengunjungMasuk) {
                        $tiket = '<a target="_blank" class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungMasuk->uuid . '"><i class="ti ti-ticket me-1"></i>Tiket </a>';
                        $extra = '<a class="btn btn-success btn-sm" href="/extra-time/' . $pengunjungMasuk->uuid . '"> <i class="ti ti-clock me-1"></i>Extra Time </a>';

                        if ($pengunjungMasuk->start_tiket) {
                            return $pengunjungMasuk->durasi_extra ? $tiket : $tiket . $extra;
                        } else {
                            $konfirmasi = '<button class="btn btn-sm btn-success" onclick="confirmStart(`/konfirmasi-pengunjung/' . $pengunjungMasuk->id . '`, `pengunjung-masuk-table`)"><i class="ti ti-check me-1"></i>Konfirmasi</button>';
                            return $tiket . $konfirmasi;
                        }
                    })
                    ->addColumn('qrcode', function ($pengunjungMasuk) {
                        return '<img src="' . asset('/storage/pengunjung_masuk/' . $pengunjungMasuk->qr_code) . '" alt="qrcode" width="100px" height="100px">';
                    })
                    ->rawColumns(['durasi', 'tiket', 'qrcode'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "pie") {
                $countPengunjungMasukLakiLaki = PengunjungMasuk::whereDate('created_at', $tanggal)->where('jenis_kelamin', 'Laki-laki')->count();
                $countPengunjungMasukPerempuan = PengunjungMasuk::whereDate('created_at', $tanggal)->where('jenis_kelamin', 'Perempuan')->count();

                return $this->successResponse([
                    $countPengunjungMasukLakiLaki,
                    $countPengunjungMasukPerempuan,
                ], 'Data pengunjung masuk ditemukan.');
            } else {
                $pengunjungMasukDay = PengunjungMasuk::whereDate('start_tiket', date('Y-m-d'))
                    ->whereNotIn('id', function ($query) {
                        $query->select('pengunjung_masuk_id')->from('pengunjung_keluars');
                    })->get();

                return $this->successResponse($pengunjungMasukDay, 'Data admin ditemukan.');
            }
        }

        if (!getPermission('riwayat_pengunjung_masuk')) {return redirect()->route('dashboard');}

        return view('admin.pengunjung.riwayat-masuk');
    }

    public function pengunjungKeluar(Request $request)
    {
        if (!getPermission('tambah_pengunjung_keluar')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'pengunjung_masuk_id' => 'required',
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
                'durasi_bermain' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            $pengunjungMasuk = PengunjungMasuk::find($request->pengunjung_masuk_id);
            if (!$pengunjungMasuk) {
                return $this->errorResponse(null, 'Data Pengunjung Masuk tidak ditemukan.', 404);
            }

            $validasiPengunjungMasuk = [
                'nama_anak' => $pengunjungMasuk->nama_anak === $request->nama_anak ? null : ['Maaf Data Anak Tidak Sesuai'],
                'nama_panggilan' => $pengunjungMasuk->nama_panggilan === $request->nama_panggilan ? null : ['Maaf Data Panggilan Tidak Sesuai'],
                'nama_orang_tua' => $pengunjungMasuk->nama_orang_tua === $request->nama_orang_tua ? null : ['Maaf Data Orang Tua Tidak Sesuai'],
                'nomor_telepon' => $pengunjungMasuk->nomor_telepon === $request->nomor_telepon ? null : ['Maaf Data Nomor Telepon Tidak Sesuai'],
                'durasi_bermain' => $pengunjungMasuk->durasi_bermain == $request->durasi_bermain ? null : ['Maaf Data Durasi Bermain Tidak Sesuai'],
            ];

            $validasiPengunjungMasuk = array_filter($validasiPengunjungMasuk);

            if ($validasiPengunjungMasuk) {
                return $this->errorResponse($validasiPengunjungMasuk, 'Data tidak valid.', 422);
            }

            $ceKPengunjungKeluar = PengunjungKeluar::where('pengunjung_masuk_id', $request->pengunjung_masuk_id)->first();
            if ($ceKPengunjungKeluar) {
                return $this->errorResponse(null, 'Data Pengunjung Keluar sudah ada.', 409);
            }

            $uuid = Uuid::uuid4()->toString();
            $baseUrl = config('app.url');
            $redirectUrl = $baseUrl . '/e-tiket/' . $uuid;

            $qrCode = QrCode::format('svg')->size(300)->generate($redirectUrl);
            $qrCodePath = 'public/pengunjung_keluar/' . $uuid . '_qrcode.svg';

            $pengunjungKeluar = PengunjungKeluar::create([
                'uuid' => $uuid,
                'pengunjung_masuk_id' => $request->pengunjung_masuk_id,
                'nama_anak' => $request->nama_anak,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->nama_orang_tua,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungKeluar, 'Pengunjung Keluar Berhasil ditambahkan.', 200);
        }

        return view('admin.pengunjung.keluar');
    }

    public function riwayatPengunjungKeluar(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $pengunjungKeluars = PengunjungKeluar::with('pengunjungMasuk')->whereDate('created_at', $tanggal)->latest()->get();
            if ($request->mode == "datatable") {
                return DataTables::of($pengunjungKeluars)
                    ->addColumn('tiket', function ($pengunjungKeluar) {
                        return '<a target="_blank" class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungKeluar->uuid . '"><i class="ti ti-ticket me-1"></i>Tiket </a>';
                    })
                    ->addColumn('qrcode', function ($pengunjungKeluar) {
                        return '<img src="' . asset('/storage/pengunjung_keluar/' . $pengunjungKeluar->qr_code) . '" alt="qrcode" width="100px" height="100px">';
                    })
                    ->rawColumns(['tiket', 'qrcode'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->mode == "pie") {
                $countPengunjungKeluarLakiLaki = PengunjungKeluar::whereHas('pengunjungMasuk', function ($query) {
                    $query->where('jenis_kelamin', 'Laki-laki');
                })->whereDate('created_at', $tanggpal)->count();
                $countPengunjungKeluarPerempuan = PengunjungKeluar::whereHas('pengunjungMasuk', function ($query) {
                    $query->where('jenis_kelamin', 'Perempuan');
                })->whereDate('created_at', $tanggal)->count();

                return $this->successResponse([$countPengunjungKeluarLakiLaki, $countPengunjungKeluarPerempuan], 'Data pengunjung keluar ditemukan.');
            }
        }

        if (!getPermission('riwayat_pengunjung_keluar')) {return redirect()->route('dashboard');}

        return view('admin.pengunjung.riwayat-keluar');
    }

    public function konfirmasiPengunjung(Request $request, $id)
    {
        $pengunjung = PengunjungMasuk::find($id);

        if (!$pengunjung) {
            return $this->errorResponse(null, 'Data pengunjung tidak ditemukan.', 404);
        }

        $pengunjung->update([
            "start_tiket" => now(),
        ]);

        return $this->successResponse($pengunjung, 'Data pengunjung dikonfirmasi.', 200);
    }

    public function extraTime($uuid)
    {
        if (!getPermission('tambah_pengunjung_masuk')) {return redirect()->route('dashboard');}

        $pengunjung = PengunjungMasuk::where('uuid', $uuid)->whereNull('durasi_extra')->first();

        if (!$pengunjung) {
            return redirect()->route('riwayatPengunjungMasuk');
        }

        $pengaturan = Pengaturan::find(1);
        return view('admin.pengunjung.extra-time', compact('pengunjung', 'pengaturan'));
    }

    public function extraTimeUpdate(Request $request, $uuid)
    {
        if (!getPermission('tambah_pengunjung_masuk')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'durasi_extra' => 'required',
            'tarif_extra' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $pengunjung = PengunjungMasuk::where('uuid', $uuid)->whereNull('durasi_extra')->first();

        if (!$pengunjung) {
            return redirect()->route('riwayatPengunjungMasuk');
        }

        $pengunjung->update([
            "durasi_extra" => $request->durasi_extra,
            "tarif_extra" => $request->tarif_extra,
        ]);

        return $this->successResponse($pengunjung, 'Pengunjung Masuk Berhasil tambah extra waktu.', 200);
    }
}
