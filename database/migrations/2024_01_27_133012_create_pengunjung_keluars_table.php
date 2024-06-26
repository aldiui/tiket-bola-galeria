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
        Schema::create('pengunjung_keluars', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pengunjung_masuk_id');
            $table->string('nama_anak')->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('nama_orang_tua')->nullable();
            $table->string('nama_group')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->integer('jumlah_anak')->nullable();
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->string('qr_code');
            $table->timestamps();

            $table->foreign('pengunjung_masuk_id')->references('id')->on('pengunjung_masuks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunjung_keluars');
    }
};
