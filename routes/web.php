<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\ModelEvaluationController;
use App\Http\Controllers\HistorisPendaftaranController;
use App\Http\Controllers\DemografiDesaController;
use App\Http\Controllers\PengaturanKapasitasController;
use App\Http\Controllers\LaporanController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Forecasting & EWS
Route::get('/forecasting', [ForecastingController::class, 'index'])->name('forecasting.index');
Route::post('/forecasting/retrain', [ForecastingController::class, 'retrain'])->name('forecasting.retrain');

// Model Evaluation (XGBoost vs Moving Average)
Route::get('/evaluation', [ModelEvaluationController::class, 'index'])->name('evaluation.index');

// Historis Pendaftaran
Route::prefix('historis')->name('historis.')->group(function () {
    Route::get('/', [HistorisPendaftaranController::class, 'index'])->name('index');
    Route::post('/store', [HistorisPendaftaranController::class, 'store'])->name('store');
    Route::put('/update/{id}', [HistorisPendaftaranController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [HistorisPendaftaranController::class, 'destroy'])->name('destroy');
    Route::post('/import', [HistorisPendaftaranController::class, 'import'])->name('import');
    Route::get('/export', [HistorisPendaftaranController::class, 'export'])->name('export');
});

// Demografi Wilayah
Route::prefix('demografi')->name('demografi.')->group(function () {
    Route::get('/', [DemografiDesaController::class, 'index'])->name('index');
    Route::post('/store', [DemografiDesaController::class, 'store'])->name('store');
    Route::put('/update/{id}', [DemografiDesaController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [DemografiDesaController::class, 'destroy'])->name('destroy');
    Route::get('/export', [DemografiDesaController::class, 'export'])->name('export');
});

// Manajemen Kapasitas
Route::prefix('kapasitas')->name('kapasitas.')->group(function () {
    Route::get('/', [PengaturanKapasitasController::class, 'index'])->name('index');
    Route::post('/update', [PengaturanKapasitasController::class, 'update'])->name('update');
});

// Cetak Laporan Strategis
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
});
