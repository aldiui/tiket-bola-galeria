<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        return Auth::user()->hakAkses()->first();
    }
}

function getPermission($value)
{
    $administrator = getAdmin();

    if (!$administrator || $administrator->$value == 0) {
        return false;
    }

    return true;
}
