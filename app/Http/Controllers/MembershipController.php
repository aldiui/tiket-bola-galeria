<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class membershipController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $memberships = Membership::all();
            if ($request->mode == "datatable") {
                return DataTables::of($memberships)
                    ->addColumn('aksi', function ($membership) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`createModal`, `/membership/' . $membership->id . '`, [`id`, `nama_anak`,`nama_panggilan`, `nama_orang_tua`, `jenis_kelamin`, `nomor_telepon`, `email`])">
                        <i class="ti ti-edit me-1"></i>Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/membership/' . $membership->id . '`, `user-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            return $this->successResponse($memberships, 'Data Memberhip ditemukan.');
        }

        return view('admin.membership.index');
    }

    public function show($id)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $membership = Membership::find($id);

        if (!$membership) {
            return $this->errorResponse(null, 'Data Memberhip tidak ditemukan.', 404);
        }

        return $this->successResponse($membership, 'Data Memberhip ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nama_anak' => 'required',
            'nama_panggilan' => 'required',
            'nama_orang_tua' => 'required',
            'jenis_kelamin' => 'required',
            'nomor_telepon' => 'required',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $membership = Membership::create($request->only('nama_anak', 'nama_panggilan', 'nama_orang_tua', 'jenis_kelamin', 'nomor_telepon', 'email'));

        return $this->successResponse($membership, 'Data Memberhip ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nama_anak' => 'required',
            'nama_panggilan' => 'required',
            'nama_orang_tua' => 'required',
            'jenis_kelamin' => 'required',
            'nomor_telepon' => 'required',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $membership = Membership::find($id);

        if (!$membership) {
            return $this->errorResponse(null, 'Data Memberhip tidak ditemukan.', 404);
        }

        $membership->update($request->only('nama_anak', 'nama_panggilan', 'nama_orang_tua', 'jenis_kelamin', 'nomor_telepon', 'email'));

        return $this->successResponse($membership, 'Data Memberhip diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $membership = Membership::find($id);

        if (!$membership) {
            return $this->errorResponse(null, 'Data Memberhip tidak ditemukan.', 404);
        }

        $membership->delete();

        return $this->successResponse(null, 'Data Memberhip dihapus.');
    }
}