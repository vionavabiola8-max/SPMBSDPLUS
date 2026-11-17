@extends('layouts.app')

@section('title', 'Proyeksi & Early Warning System')
@section('page_heading', 'Modul Forecasting & Decision Support (EWS)')

@section('content')
<div class="container-fluid p-0">

    <!-- Header Actions & Retrain Trigger -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Proyeksi Pendaftaran Siswa 1–3 Tahun ke Depan</h4>
            <p class="text-muted small mb-0">Estimasi jumlah pendaftar baru SD Plus Darul Ulum berbasis Machine Learning XGBoost & EWS Kapasitas</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('forecasting.retrain') }}" method="POST" id="retrainForm">
                @csrf
                <button type="submit" class="btn btn-primary px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" id="retrainBtn">
                    <i class="bi bi-arrow-repeat" id="retrainIcon"></i>
                    <span>Latih Ulang Model (Retrain)</span>
                </button>
            </form>
            <a href="{{ route('laporan.cetak') }}" target="_blank" class="btn btn-outline-secondary px-3 py-2">
                <i class="bi bi-printer me-1"></i> Cetak Dokumen Rapat
            </a>
        </div>
    </div>

    <!-- 3-Year Projection Cards (1-3 Tahun ke Depan) -->
    <div class="row g-4 mb-4">
        @forelse($prediksi as $index => $item)
            @php
                $ratio = round(($item->estimasi_jumlah_siswa / $kapasitas->total_kapasitas) * 100, 1);
                $isOver = $item->status_kapasitas === 'Overload';
                $isWarn = $item->status_kapasitas === 'Waspada';
            @endphp
            <div class="col-12 col-md-4">
                <div class="card shadow-sm h-100 border @if($isOver) border-danger-subtle @elseif($isWarn) border-warning-subtle @else border-success-subtle @endif">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill px-2 py-1">Tahun +{{ $index + 1 }}</span>
                            <span class="fw-bold text-dark fs-6">{{ $item->tahun_proyeksi }}</span>
                        </div>
                        @if($isOver)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 d-inline-flex align-items-center gap-1">
                                <span class="pulse-danger"></span> Overload
                            </span>
                        @elseif($isWarn)
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 d-inline-flex align-items-center gap-1">
                                <span class="pulse-warning"></span> Waspada
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 d-inline-flex align-items-center gap-1">
                                <span class="pulse-success"></span> Aman
                            </span>
                        @endif
                    </div>

                    <div class="card-body p-4 text-center">
                        <small class="text-uppercase tracking-wider text-muted fw-semibold d-block mb-1">Estimasi Pendaftar</small>
                        <div class="display-5 fw-extrabold text-primary mb-2">
                            {{ $item->estimasi_jumlah_siswa }}
                            <span class="fs-6 fw-normal text-muted">Siswa</span>
                        </div>

                        <!-- Capacity Fill Meter -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-xs mb-1">
                                <span class="text-muted">Kapasitas Maksimal ({{ $kapasitas->total_kapasitas }} Kursi)</span>
                                <span class="fw-bold @if($isOver) text-danger @elseif($isWarn) text-warning @else text-success @endif">
                                    {{ $ratio }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px;">
                                <div class="progress-bar @if($isOver) bg-danger @elseif($isWarn) bg-warning @else bg-success @endif" 
                                     role="progressbar" 
                                     style="width: {{ min(100, $ratio) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- EWS Status Badge & Box -->
                        <div class="p-3 rounded-3 text-start mb-3 @if($isOver) bg-danger bg-opacity-10 text-danger border border-danger-subtle @elseif($isWarn) bg-warning bg-opacity-10 text-dark border border-warning-subtle @else bg-success bg-opacity-10 text-success border border-success-subtle @endif">
                            <div class="fw-bold text-xs text-uppercase mb-1">
                                <i class="bi @if($isOver) bi-exclamation-octagon-fill @elseif($isWarn) bi-exclamation-triangle-fill @else bi-check-circle-fill @endif me-1"></i>
                                Rekomendasi Sistem EWS:
                            </div>
                            <p class="small mb-0 lh-sm">{{ $item->rekomendasi_sistem }}</p>
                        </div>
                    </div>

                    <div class="card-footer bg-light-subtle border-top py-2 px-3 small text-muted d-flex justify-content-between">
                        <span>Ambang Batas: {{ $kapasitas->ambang_waspada_persen }}%</span>
                        <span>Ruang Kelas: {{ $kapasitas->jumlah_ruang_kelas }} ({{ $kapasitas->kursi_per_kelas }} krs)</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info p-4 text-center">
                    <h5>Belum Ada Proyeksi Pendaftaran</h5>
                    <p class="mb-3">Silakan klik tombol di bawah untuk melatih model dan menghasilkan proyeksi 3 tahun ke depan.</p>
                    <form action="{{ route('forecasting.retrain') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Generate Proyeksi Sekarang</button>
                    </form>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Capacity What-If Simulator & Decision Support Guide -->
    <div class="row g-4 mb-4">
        <!-- Interactive Simulator -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-calculator-fill text-primary me-2"></i>Simulasi Kapasitas Interaktif (What-If Analysis)
                    </h5>
                    <small class="text-muted">Simulasikan dampak penambahan ruang kelas fisik atau kursi terhadap status EWS</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Jumlah Ruang Kelas Fisik</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="btnMinusKelas">-</button>
                                <input type="number" id="simKelas" class="form-control text-center fw-bold" value="{{ $kapasitas->jumlah_ruang_kelas }}" min="1" max="20">
                                <button class="btn btn-outline-secondary" type="button" id="btnPlusKelas">+</button>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-dark">Kapasitas Kursi per Kelas</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="btnMinusKursi">-</button>
                                <input type="number" id="simKursi" class="form-control text-center fw-bold" value="{{ $kapasitas->kursi_per_kelas }}" min="15" max="40">
                                <button class="btn btn-outline-secondary" type="button" id="btnPlusKursi">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Simulator Output Live Box -->
                    <div class="p-3 border rounded-3 bg-light mb-3">
                        <div class="row text-center g-2">
                            <div class="col-4">
                                <span class="text-xs text-muted d-block">Total Kapasitas Simulasi</span>
                                <span class="h4 fw-bold text-primary mb-0" id="simTotalKapasitas">{{ $kapasitas->total_kapasitas }}</span>
                                <small class="text-muted d-block text-xs">Kursi</small>
                            </div>
                            <div class="col-4">
                                <span class="text-xs text-muted d-block">Batas Waspada (90%)</span>
                                <span class="h4 fw-bold text-warning mb-0" id="simBatasWaspada">{{ (int)($kapasitas->total_kapasitas * 0.9) }}</span>
                                <small class="text-muted d-block text-xs">Siswa</small>
                            </div>
                            <div class="col-4">
                                <span class="text-xs text-muted d-block">Status Tahun 2025/2026</span>
                                <span class="h5 fw-bold mb-0 d-block" id="simStatusBadge">
                                    <span class="badge bg-danger">Overload</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div id="simRecommendationText" class="alert alert-info py-2 px-3 small mb-0">
                        Mengubah simulasi tidak mengubah database permanen. Gunakan menu <strong>Manajemen Kapasitas</strong> untuk menyimpan konfigurasi resmi.
                    </div>
                </div>
                <div class="card-footer bg-light-subtle border-top py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Proyeksi Target: {{ $prediksi->first() ? $prediksi->first()->estimasi_jumlah_siswa : 128 }} Siswa</span>
                    <a href="{{ route('kapasitas.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-sliders me-1"></i> Buka Pengaturan Kapasitas
                    </a>
                </div>
            </div>
        </div>

        <!-- EWS Standard Operating Procedure (SOP) Reference -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>Panduan Indikator EWS Kapasitas
                    </h5>
                    <small class="text-muted">Kriteria pengambilan keputusan strategis sekolah</small>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3 mb-3 p-2 rounded-2 border">
                        <div class="badge-ews-aman p-2 rounded-2 text-center" style="min-width: 42px;">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-success text-sm">Status Hijau (Aman: &lt; 80%)</div>
                            <p class="text-muted text-xs mb-0">
                                Kapasitas ruang dan jumlah tenaga pendidik sangat memadai. Manajemen dapat fokus pada peningkatan kualitas kegiatan belajar mengajar (KBM).
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3 p-2 rounded-2 border">
                        <div class="badge-ews-waspada p-2 rounded-2 text-center" style="min-width: 42px;">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-warning-emphasis text-sm">Status Kuning (Waspada: 80% – 90%)</div>
                            <p class="text-muted text-xs mb-0">
                                Pendaftaran mendekati kuota maksimal. Tim SPMB diinstruksikan memonitor formulir masuk dan menyiapkan opsi kursi cadangan.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-2 rounded-2 border">
                        <div class="badge-ews-overload p-2 rounded-2 text-center" style="min-width: 42px;">
                            <i class="bi bi-x-octagon-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-danger text-sm">Status Merah (Overload: &ge; 90%)</div>
                            <p class="text-muted text-xs mb-0">
                                Peringatan kritis! Sekolah diproyeksikan kelebihan pendaftar. Sistem otomatis merekomendasikan pembukaan 1 rombongan belajar (rombel) baru dan rekrutmen 2 guru pengajar baru ke yayasan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light-subtle border-top py-2 px-3 small text-muted">
                    Acuan: Blueprint Sistem Prediksi SPMB SD Plus Darul Ulum
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Retrain loading state
        const retrainForm = document.getElementById('retrainForm');
        const retrainBtn = document.getElementById('retrainBtn');
        const retrainIcon = document.getElementById('retrainIcon');

        if (retrainForm) {
            retrainForm.addEventListener('submit', function () {
                retrainBtn.disabled = true;
                retrainIcon.classList.add('spin-animation');
                retrainBtn.querySelector('span').textContent = 'Melatih Model XGBoost...';
            });
        }

        // Live Capacity Simulator
        const simKelas = document.getElementById('simKelas');
        const simKursi = document.getElementById('simKursi');
        const btnMinusKelas = document.getElementById('btnMinusKelas');
        const btnPlusKelas = document.getElementById('btnPlusKelas');
        const btnMinusKursi = document.getElementById('btnMinusKursi');
        const btnPlusKursi = document.getElementById('btnPlusKursi');
        const simTotalKapasitas = document.getElementById('simTotalKapasitas');
        const simBatasWaspada = document.getElementById('simBatasWaspada');
        const simStatusBadge = document.getElementById('simStatusBadge');
        const simRecommendationText = document.getElementById('simRecommendationText');

        const targetPendaftar = {{ $prediksi->first() ? $prediksi->first()->estimasi_jumlah_siswa : 128 }};

        function recalculateSim() {
            const kelas = parseInt(simKelas.value) || 1;
            const kursi = parseInt(simKursi.value) || 20;
            const total = kelas * kursi;
            const batas = Math.floor(total * 0.9);
            const ratio = (targetPendaftar / total) * 100;

            simTotalKapasitas.textContent = total;
            simBatasWaspada.textContent = batas;

            if (ratio < 80.0) {
                simStatusBadge.innerHTML = '<span class="badge bg-success">Aman (' + ratio.toFixed(1) + '%)</span>';
                simRecommendationText.className = 'alert alert-success py-2 px-3 small mb-0';
                simRecommendationText.innerHTML = '<strong>Hasil Simulasi:</strong> Kapasitas ' + total + ' kursi sangat memadai untuk menampung ' + targetPendaftar + ' pendaftar. Tidak diperlukan penambahan kelas.';
            } else if (ratio < 90.0) {
                simStatusBadge.innerHTML = '<span class="badge bg-warning text-dark">Waspada (' + ratio.toFixed(1) + '%)</span>';
                simRecommendationText.className = 'alert alert-warning py-2 px-3 small mb-0';
                simRecommendationText.innerHTML = '<strong>Hasil Simulasi:</strong> Tingkat keterisian ' + ratio.toFixed(1) + '%. Pendaftaran mendekati ambang waspada. Disarankan menyiapkan kursi tambahan.';
            } else {
                const lebih = targetPendaftar - total;
                const perluKelas = Math.ceil(lebih / kursi);
                simStatusBadge.innerHTML = '<span class="badge bg-danger">Overload (' + ratio.toFixed(1) + '%)</span>';
                simRecommendationText.className = 'alert alert-danger py-2 px-3 small mb-0';
                simRecommendationText.innerHTML = '<strong>Hasil Simulasi Overload:</strong> Pendaftar (' + targetPendaftar + ') melebihi kapasitas ' + total + ' kursi. Perlu menambah <strong>' + perluKelas + ' ruang kelas baru</strong> lagi!';
            }
        }

        btnMinusKelas.addEventListener('click', () => { if (simKelas.value > 1) { simKelas.value--; recalculateSim(); } });
        btnPlusKelas.addEventListener('click', () => { simKelas.value++; recalculateSim(); });
        btnMinusKursi.addEventListener('click', () => { if (simKursi.value > 15) { simKursi.value--; recalculateSim(); } });
        btnPlusKursi.addEventListener('click', () => { simKursi.value++; recalculateSim(); });
        simKelas.addEventListener('input', recalculateSim);
        simKursi.addEventListener('input', recalculateSim);

        // Run initial calculation
        recalculateSim();
    });
</script>

<style>
    .spin-animation {
        animation: spin 1s infinite linear;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
