<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_model_evaluation', function (Blueprint $table) {
            $table->id('id_evaluasi');
            $table->dateTime('tanggal_training');
            $table->string('algoritma', 50); // 'XGBoost' atau 'Moving Average'
            $table->float('nilai_rmse');
            $table->float('nilai_mape');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_model_evaluation');
    }
};
