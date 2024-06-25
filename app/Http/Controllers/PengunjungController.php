<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
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

    public function pengunjungMurid(Request $request)
    {
        if (!getPermission('tambah_pengunjung_murid')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
                'durasi_bermain' => 'required|numeric',
                'pembayaran_id' => 'required|exists:pembayarans,id',
                'tarif' => 'required',
                'email' => 'nullable|email',
                'nominal_diskon' => 'required|numeric',
                'biaya_mengantar' => 'required|numeric',
                'biaya_kaos_kaki' => 'required|numeric',
                'murid_id' => 'required|exists:murids,id',
            ], [
                'pembayaran_id.exists' => 'Pembayaran tidak valid.',
                'pembayaran_id.required' => 'Pembayaran harus diisi.',
                'murid_id.exists' => 'Murid tidak valid.',
                'murid_id.required' => 'Murid harus diisi.',
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
                'status' => '0',
                'nama_anak' => $request->nama_anak,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->nama_orang_tua,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'pembayaran_id' => $request->pembayaran_id,
                'tarif' => $request->tarif,
                'email' => $request->email,
                'diskon' => 100,
                'nominal_diskon' => $request->tarif,
                'biaya_mengantar' => $request->biaya_mengantar ?? 0,
                'biaya_kaos_kaki' => $request->biaya_kaos_kaki ?? 0,
                'alasan_diskon' => "Murid",
                'murid_id' => $request->murid_id,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
                'type' => 'Murid',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungMasuk, 'Pengunjung Murid Champs Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        $pembayaran = Pembayaran::all();
        return view('admin.pengunjung.murid', compact('pengaturan', 'pembayaran'));
    }

    public function pengunjungMembership(Request $request)
    {
        if (!getPermission('tambah_pengunjung_membership')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
                'durasi_bermain' => 'required|numeric',
                'email' => 'nullable|email',
                'biaya_mengantar' => 'required|numeric',
                'biaya_kaos_kaki' => 'required|numeric',
                'membership_id' => 'required|exists:memberships,id',
                'pembayaran_id' => 'required|exists:pembayarans,id',
            ], [
                'pembayaran_id.exists' => 'Pembayaran tidak valid.',
                'pembayaran_id.required' => 'Pembayaran harus diisi.',
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
                'status' => '0',
                'nama_anak' => $request->nama_anak,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->nama_orang_tua,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'pembayaran_id' => $request->pembayaran_id,
                'tarif' => 0,
                'email' => $request->email,
                'diskon' => 100,
                'nominal_diskon' => 0,
                'biaya_mengantar' => $request->biaya_mengantar ?? 0,
                'biaya_kaos_kaki' => $request->biaya_kaos_kaki ?? 0,
                'alasan_diskon' => "Membership",
                'membership_id' => $request->membership_id,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
                'type' => 'Membership',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungMasuk, 'Pengunjung Membership Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        $pembayaran = Pembayaran::all();
        return view('admin.pengunjung.membership', compact('pengaturan', 'pembayaran'));
    }

    public function pengunjungPerorangan(Request $request)
    {
        if (!getPermission('tambah_pengunjung_perorangan')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
                'durasi_bermain' => 'required|numeric',
                'pembayaran_id' => 'required|exists:pembayarans,id',
                'tarif' => 'required',
                'email' => 'nullable|email',
                'diskon' => 'required|numeric|min:0|max:100',
                'nominal_diskon' => 'required|numeric',
                'biaya_mengantar' => 'required|numeric',
                'biaya_kaos_kaki' => 'required|numeric',
                'alasan_diskon' => "required_if:diskon,>0",
            ], [
                'pembayaran_id.exists' => 'Pembayaran tidak valid.',
                'pembayaran_id.required' => 'Pembayaran harus diisi.',
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
                'status' => '0',
                'nama_anak' => $request->nama_anak,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->nama_orang_tua,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'pembayaran_id' => $request->pembayaran_id,
                'tarif' => $request->tarif,
                'email' => $request->email,
                'diskon' => $request->diskon ?? 0,
                'nominal_diskon' => $request->nominal_diskon ?? 0,
                'biaya_mengantar' => $request->biaya_mengantar ?? 0,
                'biaya_kaos_kaki' => $request->biaya_kaos_kaki ?? 0,
                'alasan_diskon' => $request->alasan_diskon,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
                'type' => 'Perorangan',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungMasuk, 'Pengunjung Peorangan Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        $pembayaran = Pembayaran::all();
        return view('admin.pengunjung.perorangan', compact('pengaturan', 'pembayaran'));
    }

    public function pengunjungGroup(Request $request)
    {
        if (!getPermission('tambah_pengunjung_group')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'nama_group' => 'required',
                'nama_panggilan' => 'required',
                'penanggung_jawab' => 'required',
                'durasi_bermain' => 'required|numeric',
                'jumlah_anak' => 'required|numeric|min:10',
                'nomor_telepon' => 'required',
                'pembayaran_id' => 'required|exists:pembayarans,id',
                'tarif' => 'required',
                'diskon' => 'required|numeric|min:0|max:100',
                'nominal_diskon' => 'required|numeric',
                'biaya_mengantar' => 'required|numeric',
                'biaya_kaos_kaki' => 'required|numeric',
                'alasan_diskon' => "required_if:diskon,>0",
            ], [
                'pembayaran_id.exists' => 'Pembayaran tidak valid.',
                'pembayaran_id.required' => 'Pembayaran harus diisi.',
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
                'status' => '0',
                'nama_anak' => $request->nama_group,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->penanggung_jawab,
                'nomor_telepon' => $request->nomor_telepon,
                'durasi_bermain' => $request->durasi_bermain,
                'pembayaran_id' => $request->pembayaran_id,
                'tarif' => $request->tarif,
                'jumlah_anak' => $request->jumlah_anak,
                'diskon' => $request->diskon ?? 0,
                'nominal_diskon' => $request->nominal_diskon ?? 0,
                'biaya_mengantar' => $request->biaya_mengantar ?? 0,
                'biaya_kaos_kaki' => $request->biaya_kaos_kaki ?? 0,
                'alasan_diskon' => $request->alasan_diskon,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
                'type' => 'Group',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungMasuk, 'Pengunjung Group Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        $pembayaran = Pembayaran::all();
        return view('admin.pengunjung.group', compact('pengaturan', 'pembayaran'));
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
                                    $waktuToleransi = $toleransiWaktu->toleransi_waktu;
                                    $startTicket = Carbon::parse($pengunjungMasuk->start_tiket)->addMinutes($waktuToleransi);
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
                                    $toleransTime = $endTime->addMinutes($waktuToleransi);

                                    $pengunjungMasuk->duration_difference = $durationDiff->format('%H:%I:%S');
                                    $pengunjungMasuk->duration_difference = $pengunjungMasuk->duration_difference < '00:00:00' ? '00:00:00' : $pengunjungMasuk->duration_difference;

                                    $spanId = 'countdown_' . $pengunjungMasuk->uuid;

                                    if ($remainingSeconds < 5 * 60) {
                                        $badgeColor = 'bg-warning blink';
                                    } elseif ($remainingSeconds < 10 * 60) {
                                        $badgeColor = 'bg-success blink';
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
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/pengunjung-masuk/' . $pengunjungMasuk->id . '`, `pengunjung-masuk-table`)"><i class="ti ti-trash me-1"></i>Cancel</button>';
                        if ($pengunjungMasuk->start_tiket) {
                            if ($pengunjungMasuk->status == 2 || $pengunjungMasuk->status == 1) {
                                return $tiket;
                            } else {
                                return $pengunjungMasuk->durasi_extra ? $tiket : $tiket . $extra;
                            }
                        } else {
                            if ($pengunjungMasuk->status == 2) {
                                return $tiket;
                            }
                            $konfirmasi = '<button class="btn btn-sm btn-success" onclick="confirmStart(`/konfirmasi-pengunjung/' . $pengunjungMasuk->id . '`, `pengunjung-masuk-table`)"><i class="ti ti-check me-1"></i>Konfirmasi</button>';
                            return $tiket . $konfirmasi . $deleteButton;
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
                'pengunjung_masuk_id' => 'required|exists:pengunjung_masuks,id|unique:pengunjung_keluars,pengunjung_masuk_id',
                'nama_anak' => 'required',
                'nama_panggilan' => 'required',
                'nama_orang_tua' => 'required',
                'jenis_kelamin' => 'required',
                'nomor_telepon' => 'required',
            ], [
                'pengunjung_masuk_id.unique' => 'Data Pengunjung Masuk Telah Di Konfirmasi.',
                'pengunjung_masuk_id.exists' => 'Data Pengunjung Masuk Tidak Ditemukan.',
                'pengunjung_masuk_id.required' => 'Data Pengunjung Masuk Harus Diisi.',
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
                'jenis_kelamin' => $pengunjungMasuk->jenis_kelamin === $request->jenis_kelamin ? null : ['Maaf Data Jenis Kelamin Tidak Sesuai'],
                'nomor_telepon' => $pengunjungMasuk->nomor_telepon === $request->nomor_telepon ? null : ['Maaf Data Nomor Telepon Tidak Sesuai'],
            ];

            $validasiPengunjungMasuk = array_filter($validasiPengunjungMasuk);

            if ($validasiPengunjungMasuk) {
                return $this->errorResponse($validasiPengunjungMasuk, 'Data tidak valid.', 422);
            }

            $ceKPengunjungKeluar = PengunjungKeluar::where('pengunjung_masuk_id', $request->pengunjung_masuk_id)->first();
            if ($ceKPengunjungKeluar) {
                return $this->errorResponse(null, 'Data Pengunjung Keluar sudah ada.', 409);
            }

            $pengunjungMasuk = PengunjungMasuk::find($request->pengunjung_masuk_id);

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
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
            ]);

            $pengunjungMasuk->update([
                'status' => '1',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungKeluar, 'Pengunjung Keluar Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        return view('admin.pengunjung.keluar', compact('pengaturan'));
    }

    public function pengunjungKeluarGroup(Request $request)
    {
        if (!getPermission('tambah_pengunjung_keluar')) {return redirect()->route('dashboard');}

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'pengunjung_masuk_id' => 'required|exists:pengunjung_masuks,id|unique:pengunjung_keluars,pengunjung_masuk_id',
                'nama_group' => 'required',
                'nama_panggilan' => 'required',
                'penanggung_jawab' => 'required',
                'nomor_telepon' => 'required',
            ], [
                'pengunjung_masuk_id.unique' => 'Data Pengunjung Masuk Telah Di Konfirmasi.',
                'pengunjung_masuk_id.exists' => 'Data Pengunjung Masuk Tidak Ditemukan.',
                'pengunjung_masuk_id.required' => 'Data Pengunjung Masuk Harus Diisi.',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            $pengunjungMasuk = PengunjungMasuk::find($request->pengunjung_masuk_id);
            if (!$pengunjungMasuk) {
                return $this->errorResponse(null, 'Data Pengunjung Masuk tidak ditemukan.', 404);
            }

            $validasiPengunjungMasuk = [
                'nama_group' => $pengunjungMasuk->nama_anak === $request->nama_group ? null : ['Maaf Data Group Tidak Sesuai'],
                'nama_panggilan' => $pengunjungMasuk->nama_panggilan === $request->nama_panggilan ? null : ['Maaf Data Panggilan Tidak Sesuai'],
                'penannggung_jawab' => $pengunjungMasuk->penannggung_jawab === $request->nama_orang_tua ? null : ['Maaf Data Penanggung Jawab Tidak Sesuai'],
                'nomor_telepon' => $pengunjungMasuk->nomor_telepon === $request->nomor_telepon ? null : ['Maaf Data Nomor Telepon Tidak Sesuai'],
            ];

            $validasiPengunjungMasuk = array_filter($validasiPengunjungMasuk);

            if ($validasiPengunjungMasuk) {
                return $this->errorResponse($validasiPengunjungMasuk, 'Data tidak valid.', 422);
            }

            $ceKPengunjungKeluar = PengunjungKeluar::where('pengunjung_masuk_id', $request->pengunjung_masuk_id)->first();
            if ($ceKPengunjungKeluar) {
                return $this->errorResponse(null, 'Data Pengunjung Keluar sudah ada.', 409);
            }

            $pengunjungMasuk = PengunjungMasuk::find($request->pengunjung_masuk_id);

            $uuid = Uuid::uuid4()->toString();
            $baseUrl = config('app.url');
            $redirectUrl = $baseUrl . '/e-tiket/' . $uuid;

            $qrCode = QrCode::format('svg')->size(300)->generate($redirectUrl);
            $qrCodePath = 'public/pengunjung_keluar/' . $uuid . '_qrcode.svg';

            $pengunjungKeluar = PengunjungKeluar::create([
                'uuid' => $uuid,
                'pengunjung_masuk_id' => $request->pengunjung_masuk_id,
                'nama_anak' => $request->nama_group,
                'nama_panggilan' => $request->nama_panggilan,
                'nama_orang_tua' => $request->penanggung_jawab,
                'user_id' => Auth::user()->id,
                'qr_code' => $uuid . '_qrcode.svg',
            ]);

            $pengunjungMasuk->update([
                'status' => '1',
            ]);

            Storage::put($qrCodePath, $qrCode);

            return $this->successResponse($pengunjungKeluar, 'Pengunjung Keluar Group Berhasil ditambahkan.', 200);
        }

        $pengaturan = Pengaturan::find(1);
        return view('admin.pengunjung.keluar-group', compact('pengaturan'));
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
                    ->addColumn('type', function ($pengunjungKeluar) {
                        return $pengunjungKeluar->pengunjungMasuk->type;
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

        $pengunjung = PengunjungMasuk::where('uuid', $uuid)->whereNull('durasi_extra')->first();

        if (!$pengunjung) {
            return redirect()->route('riwayatPengunjungMasuk');
        }

        $pengaturan = Pengaturan::find(1);
        return view('admin.pengunjung.extra-time', compact('pengunjung', 'pengaturan'));
    }

    public function extraTimeUpdate(Request $request, $uuid)
    {
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
            "biaya_mengantar_extra" => $pengunjung->biaya_mengantar > 0 ? $request->tarif_mengantar_extra : 0,
        ]);

        return $this->successResponse($pengunjung, 'Pengunjung Masuk Berhasil tambah extra waktu.', 200);
    }

    public function getPengunjungMasuk($id)
    {
        $pengunjung = PengunjungMasuk::find($id);

        if (!$pengunjung) {
            return $this->errorResponse(null, 'Data pengunjung tidak ditemukan.', 404);
        }

        $pengunjung->durasi_bermain = $pengunjung->durasi_extra ? $pengunjung->durasi_bermain + $pengunjung->durasi_extra : $pengunjung->durasi_bermain;

        return $this->successResponse($pengunjung, 'Data pengunjung ditemukan.', 200);
    }

    public function deletePengunjungMasuk($id)
    {
        $pengunjung = PengunjungMasuk::find($id);

        if (!$pengunjung) {
            return $this->errorResponse(null, 'Data pengunjung tidak ditemukan.', 404);
        }

        $pengunjung->update([
            'status' => '2',
            'tarif' => 0,
            'biaya_mengantar' => 0,
            'biaya_kaos_kaki' => 0,
            'type' => 'Cancel',
        ]);

        return $this->successResponse(null, 'Data pengunjung dicancel.', 200);
    }
}
