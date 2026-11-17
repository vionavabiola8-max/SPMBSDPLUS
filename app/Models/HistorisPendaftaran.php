<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorisPendaftaran extends Model
{
    protected $table = 'tb_historis_pendaftaran';
    protected $primaryKey = 'id_historis';

    protected $fillable = [
        'tahun_ajaran',
        'jumlah_pendaftar_aktual',
        'jumlah_diterima',
        'kapasitas_tersedia',
    ];

    protected $casts = [
        'jumlah_pendaftar_aktual' => 'integer',
        'jumlah_diterima' => 'integer',
        'kapasitas_tersedia' => 'integer',
    ];
}
