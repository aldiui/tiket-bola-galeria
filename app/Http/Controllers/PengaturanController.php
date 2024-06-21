<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HakAkses;
use App\Models\Pengaturan;
use App\Models\User;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    use ApiResponder;

    public function ubahTarif(Request $request)
    {
        if (!getPermission('ubah_tarif')) {return redirect()->route('dashboard');}

        $pengaturan = Pengaturan::find(1);
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'tarif' => 'required|numeric',
                'denda' => 'required|numeric',
                'tarif_mengantar' => 'required|numeric',
                'tarif_kaos_kaki' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            if (!$pengaturan) {
                $pengaturan = Pengaturan::create(['tarif' => $request->tarif]);
            }

            $pengaturan->update([
                'tarif' => $request->tarif,
                'denda' => $request->denda,
                'tarif_mengantar' => $request->tarif_mengantar,
                'tarif_kaos_kaki' => $request->tarif_kaos_kaki,
            ]);

            return $this->successResponse($pengaturan, 'Ubah Tarif berhasil diubah.', 200);
        }

        return view('admin.pengaturan.ubah-tarif', compact('pengaturan'));
    }

    public function toleransiWaktu(Request $request)
    {
        if (!getPermission('toleransi_waktu')) {return redirect()->route('dashboard');}

        $pengaturan = Pengaturan::find(1);
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'toleransi_waktu' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            $pengaturan->update([
                'toleransi_waktu' => $request->toleransi_waktu,
            ]);

            return $this->successResponse($pengaturan, 'Toleransi Waktu berhasil diubah.', 200);
        }

        return view('admin.pengaturan.toleransi-waktu', compact('pengaturan'));
    }

    public function index(Request $request)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $admins = User::all();
            if ($request->mode == "datatable") {
                return DataTables::of($admins)
                    ->addColumn('aksi', function ($admin) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`createModal`, `/user-management/' . $admin->id . '`, [`id`, `nama`, `email`])">
                        <i class="ti ti-edit me-1"></i>Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/user-management/' . $admin->id . '`, `user-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            return $this->successResponse($admins, 'Data User ditemukan.');
        }

        return view('admin.pengaturan.index');
    }

    public function show($id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $admin = User::with('hakAkses')->find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data User tidak ditemukan.', 404);
        }

        return $this->successResponse($admin, 'Data User ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $admin = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        HakAkses::create([
            'user_id' => $admin->id,
            'tambah_pengunjung_perorangan' => $request->tambah_pengunjung_perorangan ?? 0,
            'tambah_pengunjung_murid' => $request->tambah_pengunjung_murid ?? 0,
            'tambah_pengunjung_membership' => $request->tambah_pengunjung_membership ?? 0,
            'tambah_pengunjung_group' => $request->tambah_pengunjung_group ?? 0,
            'tambah_pengunjung_keluar' => $request->tambah_pengunjung_keluar ?? 0,
            'riwayat_pengunjung_masuk' => $request->riwayat_pengunjung_masuk ?? 0,
            'riwayat_pengunjung_keluar' => $request->riwayat_pengunjung_keluar ?? 0,
            'laporan_keuangan' => $request->laporan_keuangan ?? 0,
            'user_management' => $request->user_management ?? 0,
            'ubah_tarif' => $request->ubah_tarif ?? 0,
            'daftar_bank' => $request->daftar_bank ?? 0,
            'toleransi_waktu' => $request->toleransi_waktu ?? 0,
            'murid' => $request->murid ?? 0,
            'paket_membership' => $request->paket_membership ?? 0,
            'membership' => $request->membership ?? 0,
            'group' => $request->group ?? 0,
        ]);

        return $this->successResponse($admin, 'Data User ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $dataValidator = [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        if ($request->password != null) {
            $dataValidator['password'] = 'required|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $dataValidator);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $admin = User::find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data User tidak ditemukan.', 404);
        }

        $updateAdmin = [
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->password != null) {
            $updateAdmin['password'] = bcrypt($request->password);
        }

        $admin->update($updateAdmin);

        $admin->hakAkses()->update([
            'tambah_pengunjung_perorangan' => $request->tambah_pengunjung_perorangan ?? 0,
            'tambah_pengunjung_murid' => $request->tambah_pengunjung_murid ?? 0,
            'tambah_pengunjung_membership' => $request->tambah_pengunjung_membership ?? 0,
            'tambah_pengunjung_group' => $request->tambah_pengunjung_group ?? 0,
            'tambah_pengunjung_keluar' => $request->tambah_pengunjung_keluar ?? 0,
            'riwayat_pengunjung_masuk' => $request->riwayat_pengunjung_masuk ?? 0,
            'riwayat_pengunjung_keluar' => $request->riwayat_pengunjung_keluar ?? 0,
            'laporan_keuangan' => $request->laporan_keuangan ?? 0,
            'user_management' => $request->user_management ?? 0,
            'ubah_tarif' => $request->ubah_tarif ?? 0,
            'daftar_bank' => $request->daftar_bank ?? 0,
            'toleransi_waktu' => $request->toleransi_waktu ?? 0,
            'murid' => $request->murid ?? 0,
            'paket_membership' => $request->paket_membership ?? 0,
            'membership' => $request->membership ?? 0,
            'group' => $request->group ?? 0,
        ]);

        return $this->successResponse($admin, 'Data User diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $admin = User::find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data User tidak ditemukan.', 404);
        }

        $admin->delete();

        return $this->successResponse(null, 'Data User dihapus.');
    }
}