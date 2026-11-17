<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemografiDesa extends Model
{
    protected $table = 'tb_demografi_desa';
    protected $primaryKey = 'id_demografi';

    protected $fillable = [
        'tahun_pencatatan',
        'nama_desa',
        'jumlah_anak_tk',
        'jarak_ke_sekolah_km',
    ];

    protected $casts = [
        'tahun_pencatatan' => 'integer',
        'jumlah_anak_tk' => 'integer',
        'jarak_ke_sekolah_km' => 'float',
    ];
}
