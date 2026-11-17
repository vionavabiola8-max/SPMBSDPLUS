<?php

namespace App\Http\Controllers;

use App\Models\ModelEvaluation;
use App\Models\HistorisPendaftaran;
use Illuminate\Http\Request;

class ModelEvaluationController extends Controller
{
    public function index()
    {
        $evaluations = ModelEvaluation::orderBy('id_evaluasi', 'desc')->take(20)->get();
        $latestXgb = ModelEvaluation::where('algoritma', 'XGBoost')->latest('id_evaluasi')->first();
        $latestMa = ModelEvaluation::where('algoritma', 'Moving Average')->latest('id_evaluasi')->first();
        $historis = HistorisPendaftaran::orderBy('tahun_ajaran', 'asc')->get();

        // Calculate comparison summary
        $rmseDiff = 0;
        $mapeDiff = 0;
        $superiorityPercent = 0;

        if ($latestXgb && $latestMa && $latestMa->nilai_rmse > 0) {
            $rmseDiff = round($latestMa->nilai_rmse - $latestXgb->nilai_rmse, 2);
            $mapeDiff = round($latestMa->nilai_mape - $latestXgb->nilai_mape, 2);
            $superiorityPercent = round((($latestMa->nilai_rmse - $latestXgb->nilai_rmse) / $latestMa->nilai_rmse) * 100, 1);
        }

        $featureImportance = [
            ['name' => 'Tren Pertumbuhan Tahunan (Index)', 'importance' => 38.5, 'desc' => 'Tren peningkatan minat pendaftaran tahunan yang konsisten dari tahun ke tahun.'],
            ['name' => 'Populasi Anak TK Desa Terdekat (<2km)', 'importance' => 26.2, 'desc' => 'Konsentrasi murid TK di Desa Blumbungan & Larangan Dalam yang berjarak sangat dekat.'],
            ['name' => 'Pengaruh Demografi Berbobot Jarak (Gravity Model)', 'importance' => 18.3, 'desc' => 'Rasio populasi anak dibagi jarak geografis (aksesibilitas jalan ke sekolah).'],
            ['name' => 'Jumlah Pendaftar Tahun Sebelumnya (Lag-1)', 'importance' => 10.5, 'desc' => 'Efek word-of-mouth dan reputasi sekolah dari angkatan sebelumnya.'],
            ['name' => 'Total Populasi Anak TK Semua Wilayah', 'importance' => 6.5, 'desc' => 'Agregasi total anak usia 5-6 tahun di 6 desa binaan sekitar sekolah.'],
        ];

        return view('evaluation.index', compact(
            'evaluations',
            'latestXgb',
            'latestMa',
            'historis',
            'rmseDiff',
            'mapeDiff',
            'superiorityPercent',
            'featureImportance'
        ));
    }
}
