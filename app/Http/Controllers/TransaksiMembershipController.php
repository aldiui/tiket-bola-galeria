<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\PaketMembership;
use App\Models\Pembayaran;
use App\Models\TransaksiMembership;
use App\Traits\ApiResponder;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiMembershipController extends Controller
{
    use ApiResponder;

    public function index(Request $request)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        if ($request->ajax()) {
            $transaksiMembership = TransaksiMembership::with('membership', 'pembayaran', 'paketMembership')->get();
            if ($request->mode == "datatable") {
                return DataTables::of($transaksiMembership)
                    ->addColumn('aksi', function ($transaksiMembership) {
                        $deleteButton = '<button class="btn btn-sm btn-danger" onclick="confirmDelete(`/transaksi-membership/' . $transaksiMembership->id . '`, `transaksi-membership-table`)"><i class="ti ti-trash me-1"></i>Hapus</button>';
                        return $deleteButton;
                    })
                    ->addColumn('nominal', function ($transaksiMembership) {
                        return formatRupiah($transaksiMembership->nominal);
                    })
                    ->addColumn('status', function ($transaksiMembership) {
                        return $transaksiMembership->status == "1" ? 'Aktif' : 'Tidak Aktif';
                    })
                    ->addColumn('paket_membership', function ($transaksiMembership) {
                        return $transaksiMembership->paketMembership->nama;
                    })
                    ->addColumn('membership', function ($transaksiMembership) {
                        return $transaksiMembership->membership->nama_anak;
                    })
                    ->addColumn('pembayaran', function ($transaksiMembership) {
                        return $transaksiMembership->pembayaran->nama_bank;
                    })
                    ->addColumn('start_membership', function ($transaksiMembership) {
                        return formatTanggal($transaksiMembership->start_membership, 'd M Y');
                    })
                    ->addColumn('end_membership', function ($transaksiMembership) {
                        return formatTanggal($transaksiMembership->end_membership, 'd M Y');
                    })
                    ->addColumn('tanggal', function ($transaksiMembership) {
                        return formatTanggal($transaksiMembership->created_at, 'j M Y H:i:s');
                    })
                    ->addIndexColumn()
                    ->rawColumns(['aksi', 'nominal', 'status', 'paket_membership', 'membership', 'pembayaran', 'start_membership', 'end_membership', 'tanggal'])
                    ->make(true);
            }

            $transaksiMembershipStatusAktif = TransaksiMembership::with('membership', 'pembayaran', 'paketMembership')->where('status', '1')->get();
            return $this->successResponse($transaksiMembershipStatusAktif, 'Data Transaksi Membership ditemukan.');
        }

        $membership = Membership::all();
        $pembayaran = Pembayaran::all();
        $paketMembership = PaketMembership::all();
        return view('admin.transaksi-membership.index', compact('membership', 'pembayaran', 'paketMembership'));
    }

    public function show($id)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $transaksiMembership = TransaksiMembership::find($id);

        if (!$transaksiMembership) {
            return $this->errorResponse(null, 'Data Transaksi Membership tidak ditemukan.', 404);
        }

        return $this->successResponse($transaksiMembership, 'Data Transaksi Membership ditemukan.');
    }

    public function store(Request $request)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $validator = Validator::make($request->all(), [
            'membership_id' => 'required|exists:memberships,id',
            'paket_membership_id' => 'required|exists:paket_memberships,id',
            'pembayaran_id' => 'required|exists:pembayarans,id',
            'nominal' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Data tidak valid.', 422);
        }

        $transaksiMembership = TransaksiMembership::where('membership_id', $request->membership_id)
            ->where('status', '1')
            ->first();

        if ($transaksiMembership) {
            return $this->errorResponse(null, 'Data Transaksi Membership sudah ada.', 409);
        }

        $cekPaket = PaketMembership::find($request->paket_membership_id);
        if (!$cekPaket) {
            return $this->errorResponse(null, 'Data Paket Membership tidak ditemukan.', 404);
        }

        $transaksiMembership = TransaksiMembership::create([
            'user_id' => auth()->user()->id,
            'status' => '1',
            'membership_id' => $request->membership_id,
            'paket_membership_id' => $request->paket_membership_id,
            'pembayaran_id' => $request->pembayaran_id,
            'nominal' => $request->nominal,
            'start_membership' => date('Y-m-d'),
            'end_membership' => date('Y-m-d', strtotime("+" . $cekPaket->durasi_hari . " days")),
        ]);

        return $this->successResponse($transaksiMembership, 'Data Transaksi Membership ditambahkan.', 201);
    }

    public function destroy($id)
    {
        if (!getPermission('membership')) {return redirect()->route('dashboard');}

        $transaksiMembership = TransaksiMembership::find($id);

        if (!$transaksiMembership) {
            return $this->errorResponse(null, 'Data Transaksi Membership tidak ditemukan.', 404);
        }

        $transaksiMembership->delete();

        return $this->successResponse(null, 'Data Transaksi Membership dihapus.');
    }
}