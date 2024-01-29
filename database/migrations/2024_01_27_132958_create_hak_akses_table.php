<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hak_akses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('tambah_pengunjung_masuk');
            $table->boolean('tambah_pengunjung_keluar');
            $table->boolean('riwayat_pengunjung_masuk');
            $table->boolean('riwayat_pengunjung_keluar');
            $table->boolean('laporan_keuangan');
            $table->boolean('user_management');
            $table->boolean('ubah_tarif');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hak_akses');
    }
};