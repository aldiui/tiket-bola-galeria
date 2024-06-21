<?php

namespace App\Models;

use App\Models\User;
use App\Models\Membership;
use App\Models\Pembayaran;
use App\Models\PaketMembership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiMembership extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function paketMembership()
    {
        return $this->belongsTo(PaketMembership::class);
    }
}