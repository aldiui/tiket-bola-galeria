<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaketMembership;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaketMembershipController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if (!getPermission('paket_membership')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $paketMembership = PaketMembership::all();
            if ($request->mode == "datatable") {
                return DataTables::of($paketMembership)
                    ->addColumn('aksi', function ($paketMembership) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`createModal`, `/paket-membership/' . $paketMembership->id . '`, [`id`, `kode` ,`nama`, `durasi_hari`, `tarif`])">
                        <i class="ti ti-edit me-1"></i>Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/paket-membership/' . $paketMembership->id . '`, `user-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addColumn('tarif', function ($paketMembership) {
                        return formatRupiah($paketMembership->tarif);
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi', 'tarif'])
                    ->make(true);
            }

            return $this->successResponse($paketMembership, 'Data Paket Membership ditemukan.');
        }

        return view('admin.paket-membership.index');
    }

    public function show($id)
    {
        if (!getPermission('paket_membership')) {return redirect()->route('dashboard');}

        $paketMembership = PaketMembership::find($id);

        if (!$paketMembership) {
            return $this->errorResponse(null, 'Data Paket Membership tidak ditemukan.', 404);
        }

        return $this->successResponse($paketMembership, 'Data Paket Membership ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('paket_membership')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'kode' => 'required|unique:paket_memberships,kode',
            'nama' => 'required',
            'durasi_hari' => 'required',
            'tarif' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $paketMembership = PaketMembership::create($request->only('kode', 'nama', 'durasi_hari', 'tarif'));

        return $this->successResponse($paketMembership, 'Data Paket Membership ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('paket_membership')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'kode' => 'required|unique:paket_memberships,kode,' . $id,
            'nama' => 'required',
            'durasi_hari' => 'required',
            'tarif' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $paketMembership = PaketMembership::find($id);

        if (!$paketMembership) {
            return $this->errorResponse(null, 'Data Paket Membership tidak ditemukan.', 404);
        }

        $paketMembership->update($request->only('kode', 'nama', 'durasi_hari', 'tarif'));

        return $this->successResponse($paketMembership, 'Data Paket Membership diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('paket_membership')) {return redirect()->route('dashboard');}

        $paketMembership = PaketMembership::find($id);

        if (!$paketMembership) {
            return $this->errorResponse(null, 'Data Paket Membership tidak ditemukan.', 404);
        }

        $paketMembership->delete();

        return $this->successResponse(null, 'Data Paket Membership dihapus.');
    }
}