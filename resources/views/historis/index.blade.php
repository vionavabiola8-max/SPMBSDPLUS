@extends('layouts.app')

@section('title', 'Historis Pendaftaran')
@section('page_heading', 'Manajemen Historis Pendaftaran Siswa (tb_historis_pendaftaran)')

@section('content')
<div class="container-fluid p-0">

    <!-- Action Bar & Batch Import / Export -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Historis Pendaftaran</h4>
            <p class="text-muted small mb-0">Rekam jejak pendaftaran aktual sebagai target label prediksi tren deret waktu (Time Series)</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah File Batch (CSV)
            </button>
            <a href="{{ route('historis.export') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Ekspor CSV
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle me-1"></i> Tambah Data Tahun Ajaran
            </button>
        </div>
    </div>

    <!-- Informational Auto-Retraining Note -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-4 py-2">
        <i class="bi bi-info-circle-fill fs-5 text-info"></i>
        <div class="small">
            <strong>Fitur Auto-Retraining Aktif:</strong> Setiap kali Anda menambahkan, mengimpor batch, atau mengedit data historis, model Machine Learning XGBoost akan <em>otomatis dilatih ulang</em> di latar belakang untuk memperbarui proyeksi pendaftaran.
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-table text-primary me-2"></i>Daftar Pendaftaran Historis
            </h5>
            <span class="badge bg-primary-subtle text-primary border">Total: {{ count($historis) }} Tahun Ajaran</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th class="text-start">Tahun Ajaran</th>
                            <th>Pendaftar Aktual</th>
                            <th>Jumlah Diterima</th>
                            <th>Kapasitas Tersedia</th>
                            <th>Tingkat Keterisian</th>
                            <th class="text-end" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historis as $row)
                            @php
                                $fillRatio = $row->kapasitas_tersedia > 0 ? round(($row->jumlah_pendaftar_aktual / $row->kapasitas_tersedia) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="text-muted small">#{{ $row->id_historis }}</td>
                                <td class="text-start fw-bold text-primary">{{ $row->tahun_ajaran }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1 fs-6">
                                        {{ $row->jumlah_pendaftar_aktual }} Siswa
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-success">{{ $row->jumlah_diterima }} Siswa</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $row->kapasitas_tersedia }} Kursi</span>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2" style="min-width: 120px;">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar @if($fillRatio >= 100) bg-danger @elseif($fillRatio >= 90) bg-warning @else bg-success @endif" 
                                                 style="width: {{ min(100, $fillRatio) }}%"></div>
                                        </div>
                                        <span class="text-xs fw-bold @if($fillRatio >= 100) text-danger @elseif($fillRatio >= 90) text-warning @else text-success @endif">
                                            {{ $fillRatio }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary py-1 px-2 btn-edit"
                                            data-id="{{ $row->id_historis }}"
                                            data-tahun="{{ $row->tahun_ajaran }}"
                                            data-pendaftar="{{ $row->jumlah_pendaftar_aktual }}"
                                            data-diterima="{{ $row->jumlah_diterima }}"
                                            data-kapasitas="{{ $row->kapasitas_tersedia }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('historis.destroy', $row->id_historis) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tahun {{ $row->tahun_ajaran }}? Model XGBoost akan disinkronkan kembali.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-muted">Belum ada data historis pendaftaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('historis.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Data Pendaftaran Historis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026" required>
                    <small class="text-muted text-xs">Gunakan format empat digit per tahun (Contoh: 2024/2025)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Pendaftar Aktual <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_pendaftar_aktual" class="form-control" placeholder="Jumlah total pendaftar masuk" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Diterima <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_diterima" class="form-control" placeholder="Jumlah murid baru yang diterima" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kapasitas Tersedia <span class="text-danger">*</span></label>
                    <input type="number" name="kapasitas_tersedia" class="form-control" value="112" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Retrain Model</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data Pendaftaran Historis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                    <input type="text" id="editTahun" name="tahun_ajaran" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Pendaftar Aktual <span class="text-danger">*</span></label>
                    <input type="number" id="editPendaftar" name="jumlah_pendaftar_aktual" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Diterima <span class="text-danger">*</span></label>
                    <input type="number" id="editDiterima" name="jumlah_diterima" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Kapasitas Tersedia <span class="text-danger">*</span></label>
                    <input type="number" id="editKapasitas" name="kapasitas_tersedia" class="form-control" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui & Retrain Model</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import CSV Batch -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('historis.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Data Pendaftaran Batch (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">
                    Unggah file CSV untuk memasukkan data pendaftaran tahun-tahun sebelumnya secara massal. Sistem akan otomatis memperbarui database dan menjalankan training ulang model.
                </p>
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pilih Berkas CSV <span class="text-danger">*</span></label>
                    <input type="file" name="file_csv" class="form-control" accept=".csv, .txt" required>
                </div>

                <div class="p-3 bg-light rounded-3 border text-xs text-secondary">
                    <div class="fw-bold mb-1">Format Kolom Header CSV:</div>
                    <code>tahun_ajaran,jumlah_pendaftar_aktual,jumlah_diterima,kapasitas_tersedia</code>
                    <div class="mt-2 text-muted">Contoh baris data:</div>
                    <code>2023/2024,114,108,112</code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Mulai Unggah & Proses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'));
        const formEdit = document.getElementById('formEdit');
        const editTahun = document.getElementById('editTahun');
        const editPendaftar = document.getElementById('editPendaftar');
        const editDiterima = document.getElementById('editDiterima');
        const editKapasitas = document.getElementById('editKapasitas');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                editTahun.value = this.getAttribute('data-tahun');
                editPendaftar.value = this.getAttribute('data-pendaftar');
                editDiterima.value = this.getAttribute('data-diterima');
                editKapasitas.value = this.getAttribute('data-kapasitas');

                formEdit.action = `/historis/update/${id}`;
                modalEdit.show();
            });
        });
    });
</script>
@endsection
