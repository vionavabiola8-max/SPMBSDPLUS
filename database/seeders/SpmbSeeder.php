<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DemografiDesa;
use App\Models\HistorisPendaftaran;
use App\Models\PengaturanKapasitas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SpmbSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Admin TU / Kepala Sekolah
        User::firstOrCreate(
            ['email' => 'admin@sdplusdarululum.sch.id'],
            [
                'name' => 'Staf Tata Usaha SD Plus Darul Ulum',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Pengaturan Kapasitas Sekolah
        PengaturanKapasitas::firstOrCreate(
            ['tahun_ajaran' => '2024/2025'],
            [
                'jumlah_ruang_kelas' => 4,
                'kursi_per_kelas' => 28,
                'jumlah_guru_aktif' => 8,
                'total_kapasitas' => 112,
                'ambang_waspada_persen' => 90.0,
            ]
        );

        // 3. Historis Pendaftaran Tahun-Tahun Sebelumnya
        $historis = [
            ['tahun_ajaran' => '2019/2020', 'jumlah_pendaftar_aktual' => 86, 'jumlah_diterima' => 82, 'kapasitas_tersedia' => 100],
            ['tahun_ajaran' => '2020/2021', 'jumlah_pendaftar_aktual' => 92, 'jumlah_diterima' => 88, 'kapasitas_tersedia' => 100],
            ['tahun_ajaran' => '2021/2022', 'jumlah_pendaftar_aktual' => 99, 'jumlah_diterima' => 95, 'kapasitas_tersedia' => 112],
            ['tahun_ajaran' => '2022/2023', 'jumlah_pendaftar_aktual' => 106, 'jumlah_diterima' => 100, 'kapasitas_tersedia' => 112],
            ['tahun_ajaran' => '2023/2024', 'jumlah_pendaftar_aktual' => 114, 'jumlah_diterima' => 108, 'kapasitas_tersedia' => 112],
            ['tahun_ajaran' => '2024/2025', 'jumlah_pendaftar_aktual' => 121, 'jumlah_diterima' => 112, 'kapasitas_tersedia' => 112],
        ];

        foreach ($historis as $row) {
            HistorisPendaftaran::firstOrCreate(
                ['tahun_ajaran' => $row['tahun_ajaran']],
                $row
            );
        }

        // 4. Data Demografi Wilayah Desa Sekitar SD Plus Darul Ulum
        $desaList = [
            ['nama' => 'Blumbungan', 'jarak' => 1.2, 'base' => 78],
            ['nama' => 'Larangan Dalam', 'jarak' => 2.5, 'base' => 65],
            ['nama' => 'Tentenan Barat', 'jarak' => 3.1, 'base' => 54],
            ['nama' => 'Tentenan Timur', 'jarak' => 3.8, 'base' => 48],
            ['nama' => 'Duko Timur', 'jarak' => 4.5, 'base' => 42],
            ['nama' => 'Plakpak', 'jarak' => 5.2, 'base' => 36],
        ];

        $years = [2019, 2020, 2021, 2022, 2023, 2024, 2025];
        foreach ($years as $index => $year) {
            foreach ($desaList as $d) {
                // Pertumbuhan alami tren anak TK sedikit fluktuatif naik
                $trenGrowth = (int) ($index * 2.5);
                $noise = (($year + strlen($d['nama'])) % 5) - 2;
                $jumlahAnak = max(20, $d['base'] + $trenGrowth + $noise);

                DemografiDesa::firstOrCreate(
                    [
                        'tahun_pencatatan' => $year,
                        'nama_desa' => $d['nama'],
                    ],
                    [
                        'jumlah_anak_tk' => $jumlahAnak,
                        'jarak_ke_sekolah_km' => $d['jarak'],
                    ]
                );
            }
        }
    }
}
