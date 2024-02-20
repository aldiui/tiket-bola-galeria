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
                'nama_anak' => $request->input('nama_anak'),
                'nama_panggilan' => $request->input('nama_panggilan'),
                'nama_orang_tua' => $request->input('nama_orang_tua'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'nomor_telepon' => $request->input('nomor_telepon'),
                'durasi_bermain' => $request->input('durasi_bermain'),
                'metode_pembayaran' => $request->input('metode_pembayaran'),
                'tarif' => $request->input('tarif'),
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
            $tanggal = $request->input("tanggal") ?? date('Y-m-d');
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
                        if ($pengunjungMasuk->start_tiket) {
                            return '<a class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungMasuk->uuid . '"> Tiket </a>';
                        } else {
                            return '<a class="btn btn-warning btn-sm" href="/e-tiket/' . $pengunjungMasuk->uuid . '"> Tiket </a> <button class="btn btn-sm btn-success" onclick="confirmStart(`/konfirmasi-pengunjung/' . $pengunjungMasuk->id . '`, `pengunjung-masuk-table`)">Konfirmasi</button>';
                        }

                    })
                    ->addColumn('qrcode', function ($pengunjungMasuk) {
                        return '<img src="' . asset('/storage/pengunjung_masuk/' . $pengunjungMasuk->qr_code) . '" alt="qrcode" width="100px" height="100px">';
                    })
                    ->rawColumns(['durasi', 'tiket', 'qrcode'])
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->input("mode") == "pie") {
                $countPengunjungMasukLakiLaki = PengunjungMasuk::whereDate('created_at', $tanggal)->where('jenis_kelamin', 'Laki-laki')->count();
                $countPengunjungMasukPerempuan = PengunjungMasuk::whereDate('created_at', $tanggal)->where('jenis_kelamin', 'Perempuan')->count();

                return $this->successResponse([
                    $countPengunjungMasukLakiLaki,
                    $countPengunjungMasukPerempuan,
                ], 'Data pengunjung masuk ditemukan.');
            } else {
                $pengunjungMasukDay = PengunjungMasuk::whereDate('created_at', date('Y-m-d'))
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

            $pengunjungMasuk = PengunjungMasuk::find($request->input('pengunjung_masuk_id'));
            if (!$pengunjungMasuk) {
                return $this->errorResponse(null, 'Data Pengunjung Masuk tidak ditemukan.', 404);
            }

            $ceKPengunjungKeluar = PengunjungKeluar::where('pengunjung_masuk_id', $request->input('pengunjung_masuk_id'))->first();
            if ($ceKPengunjungKeluar) {
                return $this->errorResponse(null, 'Data Pengunjung Keluar sudah ada.', 409);
            }

            $uuid = Uuid::uuid4()->toString();

            $pengunjungKeluar = PengunjungKeluar::create([
                'uuid' => $uuid,
                'pengunjung_masuk_id' => $request->input('pengunjung_masuk_id'),
                'nama_anak' => $request->input('nama_anak'),
                'nama_panggilan' => $request->input('nama_panggilan'),
                'nama_orang_tua' => $request->input('nama_orang_tua'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'nomor_telepon' => $request->input('nomor_telepon'),
                'durasi_bermain' => $request->input('durasi_bermain'),
                'user_id' => Auth::user()->id,
            ]);

            return $this->successResponse($pengunjungKeluar, 'Pengunjung Keluar Berhasil ditambahkan.', 200);
        }

        return view('admin.pengunjung.keluar');
    }

    public function riwayatPengunjungKeluar(Request $request)
    {
        if ($request->ajax()) {
            $tanggal = $request->input("tanggal") ?? date('Y-m-d');
            $pengunjungKeluars = PengunjungKeluar::with('pengunjungMasuk')->whereDate('created_at', $tanggal)->latest()->get();
            if ($request->input("mode") == "datatable") {
                return DataTables::of($pengunjungKeluars)
                    ->addIndexColumn()
                    ->make(true);
            } elseif ($request->input("mode") == "pie") {
                $countPengunjungKeluarLakiLaki = PengunjungKeluar::whereHas('pengunjungMasuk', function ($query) {
                    $query->where('jenis_kelamin', 'Laki-laki');
                })->whereDate('created_at', $tanggal)->count();
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

    public function getPengunjungMasuk($id)
    {
        $pengunjung = PengunjungMasuk::find($id);

        if (!$pengunjung) {
            return $this->errorResponse(null, 'Data pengunjung tidak ditemukan.', 404);
        }

        return $this->successResponse($pengunjung, 'Data pengunjung dikonfirmasi.', 200);
    }
}
