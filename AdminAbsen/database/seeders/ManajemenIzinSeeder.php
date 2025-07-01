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
                'syarat_pengajuan' => [
                    'Surat keterangan dokter wajib untuk izin lebih dari 1 hari',
                    'Lapor ke atasan langsung maksimal H+1',
                    'Upload foto obat atau resep dokter jika ada'
                ]
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
                'syarat_pengajuan' => [
                    'Pengajuan minimal 3 hari sebelum tanggal cuti',
                    'Pastikan tidak ada meeting atau deadline penting',
                    'Koordinasi dengan tim untuk backup pekerjaan'
                ]
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
                'syarat_pengajuan' => [
                    'Jelaskan alasan izin dengan detail',
                    'Koordinasi dengan atasan langsung',
                    'Pastikan tugas harian sudah selesai atau didelegasikan'
                ]
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
                'syarat_pengajuan' => [
                    'Surat keterangan dokter kandungan',
                    'Pengajuan maksimal 1 bulan sebelum HPL',
                    'Koordinasi dengan HRD untuk proses administrasi'
                ]
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
                'syarat_pengajuan' => [
                    'Surat tugas dari atasan',
                    'Detail agenda dan tujuan dinas',
                    'Estimasi biaya perjalanan dinas'
                ]
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
                'syarat_pengajuan' => [
                    'Informasi ke atasan langsung',
                    'Pastikan tidak ada meeting penting',
                    'Selesaikan tugas urgent sebelum izin'
                ]
            ]
        ];

        foreach ($izinData as $data) {
            ManajemenIzin::create($data);
        }
    }
}
