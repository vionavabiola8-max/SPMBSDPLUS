<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_demografi_desa', function (Blueprint $table) {
            $table->id('id_demografi');
            $table->year('tahun_pencatatan')->nullable(false);
            $table->string('nama_desa', 100);
            $table->integer('jumlah_anak_tk')->default(0);
            $table->float('jarak_ke_sekolah_km')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_demografi_desa');
    }
};
