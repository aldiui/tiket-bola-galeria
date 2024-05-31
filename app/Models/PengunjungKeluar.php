<?php

namespace App\Models;

use App\Models\Pembayaran;
use App\Models\PengunjungMasuk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengunjungKeluar extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pengunjungMasuk()
    {
        return $this->belongsTo(PengunjungMasuk::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
