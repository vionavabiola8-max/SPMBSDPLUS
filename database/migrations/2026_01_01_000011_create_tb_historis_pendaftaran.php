<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_historis_pendaftaran', function (Blueprint $table) {
            $table->id('id_historis');
            $table->string('tahun_ajaran', 20)->unique();
            $table->integer('jumlah_pendaftar_aktual')->default(0);
            $table->integer('jumlah_diterima')->default(0);
            $table->integer('kapasitas_tersedia')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_historis_pendaftaran');
    }
};
