<?php

namespace App\Http\Controllers;

use App\Models\DemografiDesa;
use App\Services\MachineLearningService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DemografiDesaController extends Controller
{
    protected MachineLearningService $mlService;

    public function __construct(MachineLearningService $mlService)
    {
        $this->mlService = $mlService;
    }

    public function index(Request $request)
    {
        $query = DemografiDesa::query();

        if ($request->filled('tahun')) {
            $query->where('tahun_pencatatan', $request->tahun);
        }

        if ($request->filled('desa')) {
            $query->where('nama_desa', 'like', '%' . $request->desa . '%');
        }

        $demografi = $query->orderBy('tahun_pencatatan', 'desc')
            ->orderBy('jarak_ke_sekolah_km', 'asc')
            ->get();

        $availableYears = DemografiDesa::distinct()->orderBy('tahun_pencatatan', 'desc')->pluck('tahun_pencatatan');
        $desaList = DemografiDesa::distinct()->pluck('nama_desa');

        // Aggregate statistics
        $latestYear = $availableYears->first() ?? 2025;
        $totalAnakLatest = DemografiDesa::where('tahun_pencatatan', $latestYear)->sum('jumlah_anak_tk');
        $avgJarak = DemografiDesa::avg('jarak_ke_sekolah_km') ?? 3.2;

        return view('demografi.index', compact(
            'demografi',
            'availableYears',
            'desaList',
            'latestYear',
            'totalAnakLatest',
            'avgJarak'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_pencatatan' => 'required|integer|min:2010|max:2035',
            'nama_desa' => 'required|string|max:100',
            'jumlah_anak_tk' => 'required|integer|min:0',
            'jarak_ke_sekolah_km' => 'required|numeric|min:0.1',
        ]);

        DemografiDesa::create($request->all());

        // Retrain model
        $this->mlService->runPipeline();

        return redirect()->route('demografi.index')->with('success', 'Data demografi desa berhasil ditambahkan dan model prediksi diperbarui!');
    }

    public function update(Request $request, $id)
    {
        $item = DemografiDesa::findOrFail($id);

        $request->validate([
            'tahun_pencatatan' => 'required|integer|min:2010|max:2035',
            'nama_desa' => 'required|string|max:100',
            'jumlah_anak_tk' => 'required|integer|min:0',
            'jarak_ke_sekolah_km' => 'required|numeric|min:0.1',
        ]);

        $item->update($request->all());

        $this->mlService->runPipeline();

        return redirect()->route('demografi.index')->with('success', 'Data demografi desa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $item = DemografiDesa::findOrFail($id);
        $item->delete();

        $this->mlService->runPipeline();

        return redirect()->route('demografi.index')->with('success', 'Data demografi berhasil dihapus!');
    }

    public function export(): StreamedResponse
    {
        $fileName = 'demografi_wilayah_spmb_' . date('Ymd_His') . '.csv';
        $records = DemografiDesa::orderBy('tahun_pencatatan', 'asc')->orderBy('jarak_ke_sekolah_km', 'asc')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['tahun_pencatatan', 'nama_desa', 'jumlah_anak_tk', 'jarak_ke_sekolah_km']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->tahun_pencatatan,
                    $r->nama_desa,
                    $r->jumlah_anak_tk,
                    $r->jarak_ke_sekolah_km
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
