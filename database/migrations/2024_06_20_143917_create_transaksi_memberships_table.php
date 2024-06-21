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
        Schema::create('transaksi_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('membership_id');
            $table->unsignedBigInteger('paket_membership_id');
            $table->unsignedBigInteger('pembayaran_id');
            $table->integer('nominal');
            $table->date('start_membership')->nullable();
            $table->date('end_membership')->nullable();
            $table->enum('status', ['0', '1', '2'])->default('0');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_id')->references('id')->on('memberships')->onDelete('cascade');
            $table->foreign('pembayaran_id')->references('id')->on('pembayarans')->onDelete('cascade');
            $table->foreign('paket_membership_id')->references('id')->on('paket_memberships')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_memberships');
    }
};