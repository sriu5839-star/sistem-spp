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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('id_spp')->constrained('spp')->onDelete('cascade');
            $table->foreignId('id_petugas')->constrained('users')->onDelete('cascade');
            $table->string('bulan_dibayar');
            $table->integer('tahun_dibayar');
            $table->date('tgl_bayar');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->enum('status', ['Lunas', 'Belum Lunas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
