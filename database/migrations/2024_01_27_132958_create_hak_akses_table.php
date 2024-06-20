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
            $table->boolean('tambah_pengunjung_perorangan')->default("1");
            $table->boolean('tambah_pengunjung_murid')->default("1");
            $table->boolean('tambah_pengunjung_membership')->default("1");
            $table->boolean('tambah_pengunjung_group')->default("1");
            $table->boolean('tambah_pengunjung_keluar')->default("1");
            $table->boolean('riwayat_pengunjung_masuk')->default("1");
            $table->boolean('riwayat_pengunjung_keluar')->default("1");
            $table->boolean('laporan_keuangan')->default("1");
            $table->boolean('user_management')->default("1");
            $table->boolean('ubah_tarif')->default("1");
            $table->boolean('daftar_bank')->default("1");
            $table->boolean('toleransi_waktu')->default("1");
            $table->boolean('murid')->default("1")->default("1");
            $table->boolean('paket_membership')->default("1");
            $table->boolean('membership')->default("1");
            $table->boolean('group')->default("1");
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
