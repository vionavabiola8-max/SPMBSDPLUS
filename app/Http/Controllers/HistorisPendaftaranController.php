<?php

namespace App\Http\Controllers;

use App\Models\HistorisPendaftaran;
use App\Services\MachineLearningService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistorisPendaftaranController extends Controller
{
    protected MachineLearningService $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function index()
    {
        $historis = HistorisPendaftaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('historis.index', compact('historis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|unique:tb_historis_pendaftaran,tahun_ajaran',
            'jumlah_pendaftar_aktual' => 'required|integer|min:0',
            'jumlah_diterima' => 'required|integer|min:0',
            'kapasitas_tersedia' => 'required|integer|min:1',
        ]);

        HistorisPendaftaran::create($request->all());

        // Automatic Model Retraining as required by Blueprint!
        $this->mlService->runPipeline();

        return redirect()->route('historis.index')->with('success', 'Data historis pendaftaran berhasil disimpan dan model XGBoost telah dilatih ulang (retrained) otomatis!');
    }

    public function update(Request $request, $id)
    {
        $historis = HistorisPendaftaran::findOrFail($id);

        $request->validate([
            'tahun_ajaran' => 'required|string|unique:tb_historis_pendaftaran,tahun_ajaran,' . $id . ',id_historis',
            'jumlah_pendaftar_aktual' => 'required|integer|min:0',
            'jumlah_diterima' => 'required|integer|min:0',
            'kapasitas_tersedia' => 'required|integer|min:1',
        ]);

        $historis->update($request->all());

        // Retrain model
        $this->mlService->runPipeline();

        return redirect()->route('historis.index')->with('success', 'Data historis berhasil diperbarui dan model XGBoost telah disesuaikan!');
    }

    public function destroy($id)
    {
        $historis = HistorisPendaftaran::findOrFail($id);
        $historis->delete();

        // Retrain model
        $this->mlService->runPipeline();

        return redirect()->route('historis.index')->with('success', 'Data historis berhasil dihapus dan model XGBoost diperbarui!');
    }

    /**
     * Batch Upload / Import CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Read header

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $tahun = trim($row[0]);
                $pendaftar = (int)trim($row[1]);
                $diterima = (int)trim($row[2]);
                $kapasitas = (int)trim($row[3]);

                if (!empty($tahun)) {
                    HistorisPendaftaran::updateOrCreate(
                        ['tahun_ajaran' => $tahun],
                        [
                            'jumlah_pendaftar_aktual' => $pendaftar,
                            'jumlah_diterima' => $diterima,
                            'kapasitas_tersedia' => $kapasitas,
                        ]
                    );
                    $imported++;
                }
            }
        }
        fclose($handle);

        // Automatic retraining
        $this->mlService->runPipeline();

        return redirect()->route('historis.index')->with('success', "Batch Import berhasil! {$imported} baris data berhasil diimpor & model XGBoost telah dilatih ulang otomatis.");
    }

    /**
     * Batch Export CSV
     */
    public function export(): StreamedResponse
    {
        $fileName = 'historis_pendaftaran_spmb_' . date('Ymd_His') . '.csv';
        $records = HistorisPendaftaran::orderBy('tahun_ajaran', 'asc')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['tahun_ajaran', 'jumlah_pendaftar_aktual', 'jumlah_diterima', 'kapasitas_tersedia']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->tahun_ajaran,
                    $r->jumlah_pendaftar_aktual,
                    $r->jumlah_diterima,
                    $r->kapasitas_tersedia
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
