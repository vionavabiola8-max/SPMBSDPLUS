<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pengaturan_kapasitas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20)->default('2024/2025');
            $table->integer('jumlah_ruang_kelas')->default(4);
            $table->integer('kursi_per_kelas')->default(28);
            $table->integer('jumlah_guru_aktif')->default(8);
            $table->integer('total_kapasitas')->default(112); // ruang_kelas * kursi_per_kelas
            $table->float('ambang_waspada_persen')->default(90.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pengaturan_kapasitas');
    }
};
