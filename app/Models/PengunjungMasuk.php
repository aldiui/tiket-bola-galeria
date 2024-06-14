<?php

namespace App\Models;

use App\Models\Murid;
use App\Models\Pembayaran;
use App\Models\PengunjungKeluar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengunjungMasuk extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pengunjungKeluar()
    {
        return $this->hasOne(PengunjungKeluar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function murid()
    {
        return $this->belongsTo(Murid::class);

    }
}
