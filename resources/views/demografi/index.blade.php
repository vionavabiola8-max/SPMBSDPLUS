@extends('layouts.app')

@section('title', 'Demografi Wilayah')
@section('page_heading', 'Manajemen Demografi Wilayah Sekitar (tb_demografi_desa)')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Demografi Wilayah Desa Sekitar</h4>
            <p class="text-muted small mb-0">Tren populasi anak usia prasekolah (5–6 tahun) & jarak geografis (km) desa binaan SD Plus Darul Ulum</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('demografi.export') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> Ekspor CSV
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle me-1"></i> Tambah Data Desa
            </button>
        </div>
    </div>

    <!-- Aggregate Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="stat-card shadow-sm h-100">
                <span class="text-xs text-uppercase fw-bold text-muted tracking-wider d-block mb-1">Tahun Pencatatan Terkini</span>
                <div class="h3 fw-bold text-primary mb-0">{{ $latestYear }}</div>
                <small class="text-muted">Populasi: {{ $totalAnakLatest }} Calon Siswa TK</small>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card shadow-sm h-100">
                <span class="text-xs text-uppercase fw-bold text-muted tracking-wider d-block mb-1">Jumlah Desa Binaan</span>
                <div class="h3 fw-bold text-dark mb-0">{{ count($desaList) }} Desa</div>
                <small class="text-muted">Blumbungan, Larangan Dalam, Tentenan, dll.</small>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card shadow-sm h-100">
                <span class="text-xs text-uppercase fw-bold text-muted tracking-wider d-block mb-1">Rata-Rata Radius Jarak</span>
                <div class="h3 fw-bold text-success mb-0">{{ number_format($avgJarak, 1) }} Km</div>
                <small class="text-muted">Aksesibilitas ke SD Plus Darul Ulum</small>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('demografi.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar"></i></span>
                        <select name="tahun" class="form-select">
                            <option value="">-- Semua Tahun Pencatatan --</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="desa" class="form-control" placeholder="Cari nama desa..." value="{{ request('desa') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('demografi.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Demography Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>Daftar Demografi Desa (tb_demografi_desa)
            </h5>
            <span class="badge bg-primary-subtle text-primary border">Total Data: {{ count($demografi) }} Baris</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th>Tahun</th>
                            <th class="text-start">Nama Desa</th>
                            <th>Populasi Anak TK (Usia 5–6 Thn)</th>
                            <th>Jarak ke Sekolah</th>
                            <th>Kategori Jarak</th>
                            <th class="text-end" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demografi as $row)
                            <tr>
                                <td class="text-muted small">#{{ $row->id_demografi }}</td>
                                <td class="fw-bold">{{ $row->tahun_pencatatan }}</td>
                                <td class="text-start fw-semibold text-dark">
                                    <i class="bi bi-geo-alt text-danger me-1"></i> Desa {{ $row->nama_desa }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1 fs-6">
                                        {{ $row->jumlah_anak_tk }} Anak
                                    </span>
                                </td>
                                <td class="fw-bold text-secondary">
                                    {{ $row->jarak_ke_sekolah_km }} Km
                                </td>
                                <td>
                                    @if($row->jarak_ke_sekolah_km <= 2.0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            Sangat Dekat (&le;2 km)
                                        </span>
                                    @elseif($row->jarak_ke_sekolah_km <= 4.0)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            Radius Sedang (2-4 km)
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            Luar Radius (&gt;4 km)
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary py-1 px-2 btn-edit"
                                            data-id="{{ $row->id_demografi }}"
                                            data-tahun="{{ $row->tahun_pencatatan }}"
                                            data-nama="{{ $row->nama_desa }}"
                                            data-anak="{{ $row->jumlah_anak_tk }}"
                                            data-jarak="{{ $row->jarak_ke_sekolah_km }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('demografi.destroy', $row->id_demografi) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus data demografi desa {{ $row->nama_desa }}?')">
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
                                <td colspan="7" class="py-4 text-center text-muted">Belum ada data demografi desa yang sesuai dengan filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Demografi -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('demografi.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Data Demografi Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tahun Pencatatan <span class="text-danger">*</span></label>
                    <input type="number" name="tahun_pencatatan" class="form-control" value="{{ date('Y') }}" min="2010" max="2035" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Desa <span class="text-danger">*</span></label>
                    <input type="text" name="nama_desa" class="form-control" placeholder="Contoh: Blumbungan / Larangan Dalam" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Anak Usia TK (5–6 Tahun) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_anak_tk" class="form-control" placeholder="Estimasi populasi anak usia prasekolah" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jarak Geografis ke Sekolah (Km) <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="jarak_ke_sekolah_km" class="form-control" placeholder="Contoh: 1.5" min="0.1" required>
                    <small class="text-muted text-xs">Jarak tempuh fisik dari balai desa/pusat pemukiman ke SD Plus Darul Ulum</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Sinkron Model</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Demografi -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Data Demografi Desa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tahun Pencatatan <span class="text-danger">*</span></label>
                    <input type="number" id="editTahun" name="tahun_pencatatan" class="form-control" min="2010" max="2035" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Desa <span class="text-danger">*</span></label>
                    <input type="text" id="editNama" name="nama_desa" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jumlah Anak Usia TK (5–6 Tahun) <span class="text-danger">*</span></label>
                    <input type="number" id="editAnak" name="jumlah_anak_tk" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jarak Geografis ke Sekolah (Km) <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" id="editJarak" name="jarak_ke_sekolah_km" class="form-control" min="0.1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Data</button>
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
        const editNama = document.getElementById('editNama');
        const editAnak = document.getElementById('editAnak');
        const editJarak = document.getElementById('editJarak');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                editTahun.value = this.getAttribute('data-tahun');
                editNama.value = this.getAttribute('data-nama');
                editAnak.value = this.getAttribute('data-anak');
                editJarak.value = this.getAttribute('data-jarak');

                formEdit.action = `/demografi/update/${id}`;
                modalEdit.show();
            });
        });
    });
</script>
@endsection
