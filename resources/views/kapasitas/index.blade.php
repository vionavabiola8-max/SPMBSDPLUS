@extends('layouts.app')

@section('title', 'Manajemen Kapasitas')
@section('page_heading', 'Manajemen Kapasitas & Resource Constraint')

@section('content')
<div class="container-fluid p-0">

    <!-- Header Summary -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Manajemen Sumber Daya & Batas Kapasitas Fisik</h4>
            <p class="text-muted small mb-0">Parameter pembatas (Resource Constraints) untuk Early Warning System (EWS) SD Plus Darul Ulum</p>
        </div>
        <div>
            <a href="{{ route('forecasting.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up-arrow me-1"></i> Cek Dampak di Proyeksi EWS
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Capacity Setting Form -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-sliders text-primary me-2"></i>Pengaturan Kapasitas Aktif
                    </h5>
                    <small class="text-muted">Perubahan nilai akan langsung memperbarui kalkulasi ambang peringatan dini EWS</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kapasitas.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tahun Ajaran Aktif <span class="text-danger">*</span></label>
                            <input type="text" name="tahun_ajaran" class="form-control" value="{{ $kapasitas->tahun_ajaran ?? '2024/2025' }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Jumlah Ruang Kelas Fisik <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="inputKelas" name="jumlah_ruang_kelas" class="form-control fw-bold" value="{{ $kapasitas->jumlah_ruang_kelas ?? 4 }}" min="1" max="50" required>
                                    <span class="input-group-text bg-light text-muted">Ruang</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Kapasitas Kursi per Kelas <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="inputKursi" name="kursi_per_kelas" class="form-control fw-bold" value="{{ $kapasitas->kursi_per_kelas ?? 28 }}" min="10" max="50" required>
                                    <span class="input-group-text bg-light text-muted">Kursi</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Jumlah Guru Pengajar Aktif <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_guru_aktif" class="form-control fw-bold" value="{{ $kapasitas->jumlah_guru_aktif ?? 8 }}" min="1" max="100" required>
                                    <span class="input-group-text bg-light text-muted">Guru</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Ambang Batas Waspada EWS (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.1" id="inputAmbang" name="ambang_waspada_persen" class="form-control fw-bold text-danger" value="{{ $kapasitas->ambang_waspada_persen ?? 90.0 }}" min="50" max="100" required>
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="p-3 border rounded-3 bg-light mb-4">
                            <div class="fw-bold small text-muted text-uppercase mb-2">Kalkulasi Otomatis Sistem:</div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-secondary">Total Kapasitas Kursi Fisik:</span>
                                <span class="h5 fw-bold text-primary mb-0" id="previewTotal">{{ $kapasitas->total_kapasitas }} Siswa</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-secondary">Peringatan Overload EWS Dipicu Pada:</span>
                                <span class="fw-bold text-danger" id="previewThreshold">
                                    &ge; {{ (int)($kapasitas->total_kapasitas * $kapasitas->ambang_waspada_persen / 100) }} Siswa
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-xs text-muted pt-2 border-top">
                                <span>Rasio Guru : Siswa:</span>
                                <span>1 : {{ $kapasitas->jumlah_guru_aktif > 0 ? round($kapasitas->total_kapasitas / $kapasitas->jumlah_guru_aktif, 1) : '-' }} Siswa</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Konfigurasi & Sinkronisasi EWS
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History of Capacity Changes & Strategic Guidelines -->
        <div class="col-12 col-lg-6">
            <!-- Strategic Guidelines -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-lightbulb-fill text-warning me-2"></i>Ketentuan Kapasitas Standar Kemendikbud & Yayasan
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small text-secondary">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle text-success mt-1"></i>
                            <div><strong>Batas Standar Rombel SD:</strong> Maksimal 28–32 siswa per ruang kelas untuk menjaga efektivitas pembelajaran aktif dan pembinaan karakter.</div>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle text-success mt-1"></i>
                            <div><strong>Rasio Pendidik:</strong> Idealnya 2 orang guru (wali kelas + guru pendamping/mata pelajaran) per rombel di sekolah dasar unggulan.</div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle text-success mt-1"></i>
                            <div><strong>Ambang Batas 90%:</strong> Memberikan waktu 4-6 bulan sebelum tahun ajaran baru bagi yayasan untuk membangun/merenovasi ruang kelas atau memulai proses rekrutmen pengajar.</div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Configuration History Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Perubahan Kapasitas
                    </h6>
                    <span class="badge bg-light text-muted border">{{ count($history) }} Catatan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas &times; Kursi</th>
                                    <th>Total Kapasitas</th>
                                    <th>Guru</th>
                                    <th>Ambang</th>
                                    <th>Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $h)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $h->tahun_ajaran }}</td>
                                        <td>{{ $h->jumlah_ruang_kelas }} &times; {{ $h->kursi_per_kelas }}</td>
                                        <td class="fw-bold">{{ $h->total_kapasitas }} Kursi</td>
                                        <td>{{ $h->jumlah_guru_aktif }} Guru</td>
                                        <td>{{ $h->ambang_waspada_persen }}%</td>
                                        <td class="small text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Belum ada riwayat konfigurasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputKelas = document.getElementById('inputKelas');
        const inputKursi = document.getElementById('inputKursi');
        const inputAmbang = document.getElementById('inputAmbang');
        const previewTotal = document.getElementById('previewTotal');
        const previewThreshold = document.getElementById('previewThreshold');

        function updatePreview() {
            const k = parseInt(inputKelas.value) || 0;
            const c = parseInt(inputKursi.value) || 0;
            const a = parseFloat(inputAmbang.value) || 90.0;

            const total = k * c;
            const thresh = Math.floor(total * (a / 100));

            previewTotal.textContent = total + ' Siswa';
            previewThreshold.innerHTML = '&ge; ' + thresh + ' Siswa';
        }

        inputKelas.addEventListener('input', updatePreview);
        inputKursi.addEventListener('input', updatePreview);
        inputAmbang.addEventListener('input', updatePreview);
    });
</script>
@endsection
