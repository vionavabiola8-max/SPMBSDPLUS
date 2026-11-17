# Sistem Prediksi Pendaftaran Siswa Baru (SPMB)
### SD Plus Darul Ulum - Decision Support System Berbasis Machine Learning XGBoost

Aplikasi Sistem Informasi Prediksi Pendaftaran Siswa Baru (SPMB) untuk **SD Plus Darul Ulum** yang mengintegrasikan algoritma Machine Learning **XGBoost (Extreme Gradient Boosting)** dan **Early Warning System (EWS)** Kapasitas Sekolah.

---

## 1. Arsitektur Sistem (High-Level Architecture)

- **Backend & Web Application**: Laravel 12 (PHP 8.2+) dengan struktur MVC untuk manajemen pengguna, data pipeline, dan antarmuka web.
- **Machine Learning Engine & API**: Python 3.11 + FastAPI (`ml_service/api.py` dan `ml_service/ml_engine.py`) dengan algoritma **XGBoost Regressor** dan **Baseline Moving Average**.
- **Database**: MySQL / SQLite (`tb_demografi_desa`, `tb_historis_pendaftaran`, `tb_model_evaluation`, `tb_hasil_prediksi`, `tb_pengaturan_kapasitas`).
- **Frontend / UI**: Web-based dengan Bootstrap 5.3, Chart.js untuk visualisasi tren aktual vs prediksi, dan font modern Plus Jakarta Sans.

---

## 2. Fitur Utama

### A. Modul Manajemen Data (Data Pipeline)
1. **Import/Export Data Batch**: Fitur unggah file CSV untuk memasukkan data pendaftaran tahun-tahun sebelumnya secara massal, serta ekspor file CSV.
2. **Manajemen Demografi Wilayah**: Pencatatan tren populasi anak prasekolah (5–6 tahun) dari desa binaan sekitar sekolah (*Desa Larangan Dalam, Blumbungan, Tentenan Barat, Tentenan Timur, Duko Timur, Plakpak*) serta jarak geografisnya (km).
3. **Manajemen Kapasitas (Resource Constraint)**: Pengaturan jumlah ruang kelas fisik, kapasitas kursi per kelas, jumlah guru aktif, dan ambang batas waspada (%).

### B. Modul Analisis & Pemodelan (Core Machine Learning)
1. **Training & Retraining Model Otomatis**: Melatih ulang model XGBoost secara otomatis setiap kali ada data pendaftaran atau demografi baru yang ditambahkan/diimpor.
2. **Komparasi Performa (Model Evaluation)**: Mengukur dan membandingkan metrik **RMSE** (*Root Mean Squared Error*) dan **MAPE** (*Mean Absolute Percentage Error*) antara algoritma **XGBoost** dan metode baseline konvensional **Moving Average** untuk validasi Bab 4 Skripsi.
3. **Feature Importance Display**: Grafik tingkat pengaruh variabel prediktor (tren tahunan, pendaftar lag-1, populasi desa terdekat, dll.).

### C. Modul Forecasting & Rekomendasi (Decision Support)
1. **Proyeksi Pendaftaran (1–3 Tahun ke Depan)**: Menghasilkan angka estimasi calon siswa baru secara akurat.
2. **Early Warning System (EWS) Kapasitas**:
   - Indikator visual dinamis: **Hijau (Aman: < 80%)**, **Kuning (Waspada: 80%–90%)**, **Merah (Overload: &ge; 90%)**.
   - Rekomendasi otomatis: Perhitungan kebutuhan penambahan ruang kelas baru dan kuota rekrutmen guru tambahan jika pendaftaran melebihi ambang batas kapasitas sekolah.
3. **Simulator Kapasitas Interaktif (What-If Analysis)**: Eksperimen penyesuaian ruang kelas dan kursi secara live untuk memproyeksikan status EWS.

### D. Dashboard & Pelaporan
1. **Dashboard Interaktif**: Line chart aktual vs prediksi, doughnut chart bobot demografi, dan status peringatan dini EWS.
2. **Cetak Cetak Biru Proyeksi Strategis (PDF)**: Format dokumen resmi bertandatangan Kepala Sekolah & Ketua Yayasan untuk keperluan rapat tahunan dewan pembina yayasan pendidikan.

---

## 3. Struktur Tabel Database

1. **`tb_demografi_desa`**: `id_demografi`, `tahun_pencatatan`, `nama_desa`, `jumlah_anak_tk`, `jarak_ke_sekolah_km`, `created_at`, `updated_at`.
2. **`tb_historis_pendaftaran`**: `id_historis`, `tahun_ajaran`, `jumlah_pendaftar_aktual`, `jumlah_diterima`, `kapasitas_tersedia`, `created_at`, `updated_at`.
3. **`tb_model_evaluation`**: `id_evaluasi`, `tanggal_training`, `algoritma`, `nilai_rmse`, `nilai_mape`, `catatan`, `created_at`, `updated_at`.
4. **`tb_hasil_prediksi`**: `id_prediksi`, `tahun_proyeksi`, `estimasi_jumlah_siswa`, `status_kapasitas`, `rekomendasi_sistem`, `detail_komparasi`, `created_at`, `updated_at`.
5. **`tb_pengaturan_kapasitas`**: `id`, `tahun_ajaran`, `jumlah_ruang_kelas`, `kursi_per_kelas`, `jumlah_guru_aktif`, `total_kapasitas`, `ambang_waspada_persen`, `created_at`, `updated_at`.

---

## 4. Cara Menjalankan Aplikasi

### 1. Menjalankan Server Web Laravel
Buka terminal/PowerShell di direktori proyek:
```bash
php artisan serve
```
Aplikasi web dapat diakses melalui browser di: `http://localhost:8000` atau `http://127.0.0.1:8000`.

### 2. (Opsional) Menjalankan FastAPI ML Microservice
Klik dua kali file `start_ml_api.bat` atau jalankan:
```bash
python ml_service\run_server.py
```
*Catatan: Sistem dilengkapi dengan Hybrid Native Engine sehingga Laravel dapat mengeksekusi script Python langsung atau menggunakan fallback otomatis jika microservice sedang tidak aktif.*

### 3. Akun Pengguna Bawaan (Default TU / Kepala Sekolah)
- **Pos-el (Email)**: `admin@sdplusdarululum.sch.id`
- **Kata Sandi (Password)**: `password123`
