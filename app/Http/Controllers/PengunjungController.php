<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\PengunjungKeluar;
use App\Models\PengunjungMasuk;
use App\Traits\ApiResponder;
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
        getPermission('tambah_pengunjung_masuk');
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
                        return '<span class="badge bg-primary rounded-3 fw-semibold">' . $pengunjungMasuk->durasi_bermain . ' Jam</span>';
                    })
                    ->rawColumns(['durasi'])
                    ->addIndexColumn()
                    ->make(true);
            }

            $pengunjungMasukDay = PengunjungMasuk::whereDate('created_at', date('Y-m-d'))
                ->whereNotIn('id', function ($query) {
                    $query->select('pengunjung_masuk_id')->from('pengunjung_keluars');
                })->get();

            return $this->successResponse($pengunjungMasukDay, 'Data admin ditemukan.');
        }

        getPermission('riwayat_pengunjung_masuk');
        return view('admin.pengunjung.riwayat-masuk');
    }

    public function pengunjungKeluar(Request $request)
    {
        getPermission('tambah_pengunjung_keluar');
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'pengunjung_masuk_id' => 'required',
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
                    ->addColumn('nama_anak', function ($pengunjungKeluar) {
                        return $pengunjungKeluar->pengunjungMasuk->nama_anak;
                    })
                    ->addColumn('nama_panggilan', function ($pengunjungKeluar) {
                        return $pengunjungKeluar->pengunjungMasuk->nama_panggilan;
                    })
                    ->addColumn('durasi_bermain', function ($pengunjungKeluar) {
                        return $pengunjungKeluar->pengunjungMasuk->durasi_bermain;
                    })
                    ->addColumn('nama_orang_tua', function ($pengunjungKeluar) {
                        return $pengunjungKeluar->pengunjungMasuk->nama_orang_tua;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['nama_anak', 'nama_panggilan', 'durasi_bermain', 'nama_orang_tua'])
                    ->make(true);
            }
        }

        getPermission('riwayat_pengunjung_masuk');
        return view('admin.pengunjung.riwayat-keluar');
    }
}