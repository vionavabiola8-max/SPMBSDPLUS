<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_hasil_prediksi', function (Blueprint $table) {
            $table->id('id_prediksi');
            $table->string('tahun_proyeksi', 20);
            $table->integer('estimasi_jumlah_siswa');
            $table->enum('status_kapasitas', ['Aman', 'Waspada', 'Overload']);
            $table->text('rekomendasi_sistem');
            $table->json('detail_komparasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_hasil_prediksi');
    }
};
