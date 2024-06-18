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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_anak');
            $table->string('nama_panggilan');
            $table->string('nama_orang_tua');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('nomor_telepon');
            $table->string('email')->nullable();
            $table->integer('durasi_bermain');
            $table->integer('tarif');
            $table->integer('biaya_mengantar')->default("0");
            $table->integer('biaya_kaos_kaki')->default("0");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
