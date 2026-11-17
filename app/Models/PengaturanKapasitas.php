<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanKapasitas extends Model
{
    protected $table = 'tb_pengaturan_kapasitas';

    protected $fillable = [
        'tahun_ajaran',
        'jumlah_ruang_kelas',
        'kursi_per_kelas',
        'jumlah_guru_aktif',
        'total_kapasitas',
        'ambang_waspada_persen',
    ];

    protected $casts = [
        'jumlah_ruang_kelas' => 'integer',
        'kursi_per_kelas' => 'integer',
        'jumlah_guru_aktif' => 'integer',
        'total_kapasitas' => 'integer',
        'ambang_waspada_persen' => 'float',
    ];
}
