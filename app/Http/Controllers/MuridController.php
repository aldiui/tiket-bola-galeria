<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Murid;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MuridController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if (!getPermission('murid')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $murids = Murid::all();
            if ($request->mode == "datatable") {
                return DataTables::of($murids)
                    ->addColumn('aksi', function ($murid) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`createModal`, `/murid/' . $murid->id . '`, [`id`, `nomor_murid`, `nama_anak`,`umur`, `kelas`, `nama_orang_tua`, `nomor_telepon`])">
                        <i class="ti ti-edit me-1"></i>Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/murid/' . $murid->id . '`, `user-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            return $this->successResponse($murids, 'Data Murid Champs ditemukan.');
        }

        return view('admin.murid.index');
    }

    public function show($id)
    {
        if (!getPermission('murid')) {return redirect()->route('dashboard');}

        $murid = Murid::find($id);

        if (!$murid) {
            return $this->errorResponse(null, 'Data Murid Champs tidak ditemukan.', 404);
        }

        return $this->successResponse($murid, 'Data Murid Champs ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('murid')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nomor_murid' => 'required|unique:murids',
            'nama_anak' => 'required',
            'umur' => 'required|numeric',
            'kelas' => 'required',
            'nama_orang_tua' => 'required',
            'nomor_telepon' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $murid = Murid::create($request->only('nomor_murid', 'nama_anak', 'umur', 'kelas', 'nama_orang_tua', 'nomor_telepon'));

        return $this->successResponse($murid, 'Data Murid Champs ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('murid')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nomor_murid' => 'required|unique:murids,nomor_murid,' . $id,
            'nama_anak' => 'required',
            'umur' => 'required|numeric',
            'kelas' => 'required',
            'nama_orang_tua' => 'required',
            'nomor_telepon' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $murid = Murid::find($id);

        if (!$murid) {
            return $this->errorResponse(null, 'Data Murid Champs tidak ditemukan.', 404);
        }

        $murid->update($request->only('nomor_murid', 'nama_anak', 'umur', 'kelas', 'nama_orang_tua', 'nomor_telepon'));

        return $this->successResponse($murid, 'Data Murid Champs diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('murid')) {return redirect()->route('dashboard');}

        $murid = Murid::find($id);

        if (!$murid) {
            return $this->errorResponse(null, 'Data Murid Champs tidak ditemukan.', 404);
        }

        $murid->delete();

        return $this->successResponse(null, 'Data Murid Champs dihapus.');
    }
}
