<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelEvaluation extends Model
{
    protected $table = 'tb_model_evaluation';
    protected $primaryKey = 'id_evaluasi';

    protected $fillable = [
        'tanggal_training',
        'algoritma',
        'nilai_rmse',
        'nilai_mape',
        'catatan',
    ];

    protected $casts = [
        'tanggal_training' => 'datetime',
        'nilai_rmse' => 'float',
        'nilai_mape' => 'float',
    ];
}
