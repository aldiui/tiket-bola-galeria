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
                'tarif' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
            }

            if (!$pengaturan) {
                $pengaturan = Pengaturan::create(['tarif' => $request->input('tarif')]);
            }

            $pengaturan->update(['tarif' => $request->input('tarif')]);

            return $this->successResponse($pengaturan, 'Ubah Tarif berhasil diubah.', 200);
        }

        return view('admin.pengaturan.ubah-tarif', compact('pengaturan'));
    }

    public function index(Request $request)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $admins = User::all();
            if ($request->input("mode") == "datatable") {
                return DataTables::of($admins)
                    ->addColumn('aksi', function ($admin) {
                        $editButton = '<button class="btn btn-sm btn-warning me-1" onclick="getModal(`editModal`, `/user-management/' . $admin->id . '`, [`id`, `nama`, `email`])">Edit</button>';
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/user-management/' . $admin->id . '`, `user-table`)">Hapus</button>';
                        return $editButton . $deleteButton;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi'])
                    ->make(true);
            }

            return $this->successResponse($admins, 'Data admin ditemukan.');
        }

        return view('admin.pengaturan.index');
    }

    public function show($id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $admin = User::with('hakAkses')->find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data admin tidak ditemukan.', 404);
        }

        return $this->successResponse($admin, 'Data admin ditemukan.');
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
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        HakAkses::create([
            'user_id' => $admin->id,
            'tambah_pengunjung_masuk' => $request->input('tambah_pengunjung_masuk') ?? 0,
            'tambah_pengunjung_keluar' => $request->input('tambah_pengunjung_keluar') ?? 0,
            'riwayat_pengunjung_masuk' => $request->input('riwayat_pengunjung_masuk') ?? 0,
            'riwayat_pengunjung_keluar' => $request->input('riwayat_pengunjung_keluar') ?? 0,
            'laporan_keuangan' => $request->input('laporan_keuangan') ?? 0,
            'user_management' => $request->input('user_management') ?? 0,
            'ubah_tarif' => $request->input('ubah_tarif') ?? 0,
        ]);

        return $this->successResponse($admin, 'Data admin ditambahkan.', 201);
    }

    public function update(Request $request, $id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $dataValidator = [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        if ($request->input('password') != null) {
            $dataValidator['password'] = 'required|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $dataValidator);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $admin = User::find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data admin tidak ditemukan.', 404);
        }

        $updateAdmin = [
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
        ];

        if ($request->input('password') != null) {
            $updateAdmin['password'] = bcrypt($request->input('password'));
        }

        $admin->update($updateAdmin);

        $admin->hakAkses()->update([
            'tambah_pengunjung_masuk' => $request->input('tambah_pengunjung_masuk') ?? 0,
            'tambah_pengunjung_keluar' => $request->input('tambah_pengunjung_keluar') ?? 0,
            'riwayat_pengunjung_masuk' => $request->input('riwayat_pengunjung_masuk') ?? 0,
            'riwayat_pengunjung_keluar' => $request->input('riwayat_pengunjung_keluar') ?? 0,
            'laporan_keuangan' => $request->input('laporan_keuangan') ?? 0,
            'user_management' => $request->input('user_management') ?? 0,
            'ubah_tarif' => $request->input('ubah_tarif') ?? 0,
        ]);

        return $this->successResponse($admin, 'Data admin diubah.');
    }

    public function destroy($id)
    {
        if (!getPermission('user_management')) {return redirect()->route('dashboard');}

        $admin = User::find($id);

        if (!$admin) {
            return $this->errorResponse(null, 'Data admin tidak ditemukan.', 404);
        }

        $admin->delete();

        return $this->successResponse(null, 'Data admin dihapus.');
    }
}