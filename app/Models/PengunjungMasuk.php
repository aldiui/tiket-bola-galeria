<?php

namespace App\Models;

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
}