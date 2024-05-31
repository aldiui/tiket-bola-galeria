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
        Schema::create('pengunjung_masuks', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pembayaran_id')->nullable();
            $table->string('nama_anak');
            $table->string('nama_panggilan');
            $table->string('nama_orang_tua');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->integer('durasi_bermain');
            $table->integer('durasi_extra')->nullable();
            $table->string('nomor_telepon');
            $table->string('email')->nullable();
            $table->integer('tarif');
            $table->integer('tarif_extra')->default("0");
            $table->integer('diskon')->nullable();
            $table->text('alasan_diskon')->nullable();
            $table->string('qr_code');
            $table->datetime('start_tiket')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunjung_masuks');
    }
};
