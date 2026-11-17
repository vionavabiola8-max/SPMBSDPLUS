<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilPrediksi extends Model
{
    protected $table = 'tb_hasil_prediksi';
    protected $primaryKey = 'id_prediksi';

    protected $fillable = [
        'tahun_proyeksi',
        'estimasi_jumlah_siswa',
        'status_kapasitas',
        'rekomendasi_sistem',
        'detail_komparasi',
    ];

    protected $casts = [
        'estimasi_jumlah_siswa' => 'integer',
        'detail_komparasi' => 'array',
    ];
}
