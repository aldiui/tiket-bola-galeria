<?php

use Carbon\Carbon;

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal = null, $format = 'l, j F Y')
    {
        $parsedDate = Carbon::parse($tanggal)->locale('id')->settings(['formatFunction' => 'translatedFormat']);
        return $parsedDate->format($format);
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('getAdmin')) {
    function getAdmin()
    {
        return auth()->user()->hakAkses()->first();
    }
}

if (!function_exists('getPermission')) {
    function getPermission($value)
    {
        $admin = getAdmin();

        if (!$admin || $admin->$value == 0) {
            return redirect('/');
        }
    }
}
