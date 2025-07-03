<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManajemenIzin;

class ManajemenIzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $izinData = [
            [
                'nama_izin' => 'Izin Sakit',
                'kode_izin' => 'sakit',
                'deskripsi' => 'Izin tidak masuk kerja karena sakit dengan surat dokter',
                'max_hari' => 3,
                'perlu_dokumen' => true,
                'auto_approve' => false,
                'kategori' => 'sakit',
                'warna_badge' => 'danger',
                'is_active' => true,
                'urutan_tampil' => 1,
            ],
            [
                'nama_izin' => 'Cuti Tahunan',
                'kode_izin' => 'cuti',
                'deskripsi' => 'Cuti tahunan sesuai hak karyawan',
                'max_hari' => 12,
                'perlu_dokumen' => false,
                'auto_approve' => false,
                'kategori' => 'cuti',
                'warna_badge' => 'success',
                'is_active' => true,
                'urutan_tampil' => 2,
            ],
            [
                'nama_izin' => 'Izin Khusus',
                'kode_izin' => 'izin',
                'deskripsi' => 'Izin khusus untuk keperluan mendesak atau pribadi',
                'max_hari' => 2,
                'perlu_dokumen' => false,
                'auto_approve' => false,
                'kategori' => 'izin_khusus',
                'warna_badge' => 'warning',
                'is_active' => true,
                'urutan_tampil' => 3,
            ],
            [
                'nama_izin' => 'Cuti Melahirkan',
                'kode_izin' => 'cuti_melahirkan',
                'deskripsi' => 'Cuti melahirkan untuk karyawan wanita',
                'max_hari' => 90,
                'perlu_dokumen' => true,
                'auto_approve' => false,
                'kategori' => 'cuti',
                'warna_badge' => 'info',
                'is_active' => true,
                'urutan_tampil' => 4,
            ],
            [
                'nama_izin' => 'Dinas Luar',
                'kode_izin' => 'dinas_luar',
                'deskripsi' => 'Tugas dinas luar kota atau luar kantor',
                'max_hari' => 30,
                'perlu_dokumen' => true,
                'auto_approve' => false,
                'kategori' => 'dinas',
                'warna_badge' => 'primary',
                'is_active' => true,
                'urutan_tampil' => 5,
            ],
            [
                'nama_izin' => 'Izin Setengah Hari',
                'kode_izin' => 'izin_setengah_hari',
                'deskripsi' => 'Izin tidak masuk setengah hari (pagi atau siang)',
                'max_hari' => 1,
                'perlu_dokumen' => false,
                'auto_approve' => true,
                'kategori' => 'izin_khusus',
                'warna_badge' => 'secondary',
                'is_active' => true,
                'urutan_tampil' => 6,
            ]
        ];

        foreach ($izinData as $data) {
            ManajemenIzin::create($data);
        }
    }
}
