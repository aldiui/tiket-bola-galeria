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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_group');
            $table->string('penganggung_jawab');
            $table->integer('jumlah_anak');
            $table->string('nomor_telepon');
            $table->integer('tarif_per_anak');
            $table->integer('durasi_bermain');
            $table->integer('nominal_diskon')->default("0");
            $table->integer('diskon')->default("0");
            $table->integer('biaya_mengantar')->default("0");
            $table->integer('biaya_kaos_kaki')->default("0");
            $table->integer('total_tarif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
