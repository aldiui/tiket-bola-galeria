<?php

namespace App\Models;

use App\Models\TransaksiMembership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transaksiMembership()
    {
        return $this->hasMany(TransaksiMembership::class);
    }
}
