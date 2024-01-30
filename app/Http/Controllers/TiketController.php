<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengunjungMasuk;

class TiketController extends Controller
{
    public function show($uuid)
    {
        $cekTiket = PengunjungMasuk::where('uuid', $uuid)->first();

        if (!$cekTiket) {
            return redirect()->route('/');
        }

        return view('admin.tiket.index', compact('cekTiket'));
    }
}
