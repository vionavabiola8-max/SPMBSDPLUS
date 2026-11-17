<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKapasitas;
use App\Services\MachineLearningService;
use Illuminate\Http\Request;

class PengaturanKapasitasController extends Controller
{
    protected MachineLearningService $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function index()
    {
        $kapasitas = PengaturanKapasitas::latest()->first() ?? (object)[
            'tahun_ajaran' => '2024/2025',
            'jumlah_ruang_kelas' => 4,
            'kursi_per_kelas' => 28,
            'jumlah_guru_aktif' => 8,
            'total_kapasitas' => 112,
            'ambang_waspada_persen' => 90.0,
        ];

        $history = PengaturanKapasitas::orderBy('id', 'desc')->get();

        return view('kapasitas.index', compact('kapasitas', 'history'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'jumlah_ruang_kelas' => 'required|integer|min:1',
            'kursi_per_kelas' => 'required|integer|min:10',
            'jumlah_guru_aktif' => 'required|integer|min:1',
            'ambang_waspada_persen' => 'required|numeric|min:50|max:100',
        ]);

        $totalKapasitas = $request->jumlah_ruang_kelas * $request->kursi_per_kelas;

        PengaturanKapasitas::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'jumlah_ruang_kelas' => $request->jumlah_ruang_kelas,
            'kursi_per_kelas' => $request->kursi_per_kelas,
            'jumlah_guru_aktif' => $request->jumlah_guru_aktif,
            'total_kapasitas' => $totalKapasitas,
            'ambang_waspada_persen' => $request->ambang_waspada_persen,
        ]);

        // Retrigger ML pipeline so EWS immediately reflects the updated capacity!
        $this->mlService->runPipeline();

        return redirect()->route('kapasitas.index')->with('success', 'Pengaturan kapasitas berhasil diperbarui! Proyeksi EWS telah disinkronkan secara otomatis.');
    }
}
