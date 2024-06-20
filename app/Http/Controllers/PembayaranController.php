<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if (!getPermission('daftar_bank')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $daftarBank = Pembayaran::all();
            if ($request->mode == "datatable") {
                return DataTables::of($daftarBank)
                    ->addColumn('aksi', function ($daftarBank) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`createModal`, `/daftar-bank/' . $daftarBank->id . '`, [`id`, `nama_bank`, `nama_akun`, `nomor_rekening`])">
                        <i class="ti ti-edit me-1"></i>Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/daftar-bank/' . $daftarBank->id . '`, `user-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            return $this->successResponse($daftarBank, 'Data Daftar Bank ditemukan.');
        }

        return view('admin.pembayaran.index');
    }

    public function show($id)
    {
        if (!getPermission('daftar_bank')) {return redirect()->route('dashboard');}

        $daftarBank = Pembayaran::find($id);

        if (!$daftarBank) {
            return $this->errorResponse(null, 'Data Daftar Bank tidak ditemukan.', 404);
        }

        return $this->successResponse($daftarBank, 'Data Daftar Bank ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('daftar_bank')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nama_bank' => 'required',
            'nama_akun' => 'required',
            'nomor_rekening' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $daftarBank = Pembayaran::create($request->only('nama_bank', 'nama_akun', 'nomor_rekening'));

        return $this->successResponse($daftarBank, 'Data Daftar Bank ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('daftar_bank')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nama_bank' => 'required',
            'nama_akun' => 'required',
            'nomor_rekening' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $daftarBank = Pembayaran::find($id);

        if (!$daftarBank) {
            return $this->errorResponse(null, 'Data Daftar Bank tidak ditemukan.', 404);
        }

        $daftarBank->update($request->only('nama_bank', 'nama_akun', 'nomor_rekening'));

        return $this->successResponse($daftarBank, 'Data Daftar Bank diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('daftar_bank')) {return redirect()->route('dashboard');}

        $daftarBank = Pembayaran::find($id);

        if (!$daftarBank) {
            return $this->errorResponse(null, 'Data Daftar Bank tidak ditemukan.', 404);
        }

        $daftarBank->delete();

        return $this->successResponse(null, 'Data Daftar Bank dihapus.');
    }
}
