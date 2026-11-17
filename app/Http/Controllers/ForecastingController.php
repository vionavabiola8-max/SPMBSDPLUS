<?php

namespace App\Http\Controllers;

use App\Models\HasilPrediksi;
use App\Models\HistorisPendaftaran;
use App\Models\PengaturanKapasitas;
use App\Services\MachineLearningService;
use Illuminate\Http\Request;

class ForecastingController extends Controller
{
    protected MachineLearningService $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function index()
    {
        $prediksi = HasilPrediksi::orderBy('id_prediksi', 'asc')->get();
        $kapasitas = PengaturanKapasitas::latest()->first() ?? (object)[
            'jumlah_ruang_kelas' => 4,
            'kursi_per_kelas' => 28,
            'jumlah_guru_aktif' => 8,
            'total_kapasitas' => 112,
            'ambang_waspada_persen' => 90.0,
        ];
        $historis = HistorisPendaftaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('forecasting.index', compact('prediksi', 'kapasitas', 'historis'));
    }

    public function retrain(Request $request)
    {
        $result = $this->mlService->runPipeline();

        if (isset($result['error'])) {
            return redirect()->route('forecasting.index')->with('error', $result['error']);
        }

        return redirect()->route('forecasting.index')->with('success', 'Model XGBoost berhasil dilatih ulang (Retrained) dan hasil proyeksi diperbarui secara real-time!');
    }
}
