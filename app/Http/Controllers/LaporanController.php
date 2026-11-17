<?php

namespace App\Http\Controllers;

use App\Models\DemografiDesa;
use App\Models\HistorisPendaftaran;
use App\Models\HasilPrediksi;
use App\Models\ModelEvaluation;
use App\Models\PengaturanKapasitas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $historis = HistorisPendaftaran::orderBy('tahun_ajaran', 'asc')->get();
        $prediksi = HasilPrediksi::orderBy('id_prediksi', 'asc')->get();
        $kapasitas = PengaturanKapasitas::latest()->first() ?? (object)[
            'jumlah_ruang_kelas' => 4,
            'kursi_per_kelas' => 28,
            'jumlah_guru_aktif' => 8,
            'total_kapasitas' => 112,
            'ambang_waspada_persen' => 90.0,
        ];

        $latestXgb = ModelEvaluation::where('algoritma', 'XGBoost')->latest('id_evaluasi')->first();
        $latestMa = ModelEvaluation::where('algoritma', 'Moving Average')->latest('id_evaluasi')->first();
        $demografi = DemografiDesa::orderBy('tahun_pencatatan', 'desc')->take(10)->get();

        return view('laporan.index', compact(
            'historis',
            'prediksi',
            'kapasitas',
            'latestXgb',
            'latestMa',
            'demografi'
        ));
    }

    public function cetak()
    {
        $historis = HistorisPendaftaran::orderBy('tahun_ajaran', 'asc')->get();
        $prediksi = HasilPrediksi::orderBy('id_prediksi', 'asc')->get();
        $kapasitas = PengaturanKapasitas::latest()->first() ?? (object)[
            'jumlah_ruang_kelas' => 4,
            'kursi_per_kelas' => 28,
            'jumlah_guru_aktif' => 8,
            'total_kapasitas' => 112,
            'ambang_waspada_persen' => 90.0,
        ];

        $latestXgb = ModelEvaluation::where('algoritma', 'XGBoost')->latest('id_evaluasi')->first();
        $latestMa = ModelEvaluation::where('algoritma', 'Moving Average')->latest('id_evaluasi')->first();

        return view('laporan.cetak', compact(
            'historis',
            'prediksi',
            'kapasitas',
            'latestXgb',
            'latestMa'
        ));
    }
}
