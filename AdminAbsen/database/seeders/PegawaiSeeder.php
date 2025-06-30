<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate tabel untuk menghindari duplikasi
        Pegawai::truncate();
        $pegawaiData = [
            [
                // Tab Users
                'nama' => 'John Doe',
                'npp' => 'NPP001',
                'email' => 'john.doe@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123456',
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567890',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Kebon Jeruk No. 123, Jakarta Barat',
                // Tab Jabatan
                'jabatan_nama' => 'Senior Developer',
                'jabatan_tunjangan' => 2000000,
                // Tab Posisi
                'posisi_nama' => 'Lead Programmer',
                'posisi_tunjangan' => 1500000,
                // Tab Pendidikan (JSON)
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Indonesia',
                        'fakultas_program_studi' => 'Ilmu Komputer',
                        'jurusan' => 'Sistem Informasi',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.75',
                        'ijazah' => null
                    ],
                    [
                        'jenjang' => 'SMA',
                        'sekolah_univ' => 'SMA Negeri 1 Jakarta',
                        'fakultas_program_studi' => null,
                        'jurusan' => 'IPA',
                        'thn_masuk' => '2012-07-01',
                        'thn_lulus' => '2015-06-01',
                        'ipk_nilai' => '85.5',
                        'ijazah' => null
                    ]
                ]),
                // Tab Emergency (JSON)
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ayah', 'nama_kontak' => 'David Doe', 'no_emergency' => '081234567891'],
                    ['relationship' => 'Ibu', 'nama_kontak' => 'Jane Doe', 'no_emergency' => '081234567892']
                ]),
                // Tab Fasilitas (JSON)
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567890',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567891',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Transport',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Transport',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 500000
                    ]
                ])
            ],
            [
                'nama' => 'Jane Smith',
                'npp' => 'NPP002',
                'email' => 'jane.smith@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123457',
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567893',
                'status' => 'active',
                'role_user' => 'Kepala Bidang',
                'alamat' => 'Jl. Sudirman No. 456, Jakarta Pusat',
                'jabatan_nama' => 'Manager IT',
                'jabatan_tunjangan' => 3000000,
                'posisi_nama' => 'Head of Development',
                'posisi_tunjangan' => 2500000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S2',
                        'sekolah_univ' => 'Institut Teknologi Bandung',
                        'fakultas_program_studi' => 'Teknik Informatika',
                        'jurusan' => 'Sistem Informasi',
                        'thn_masuk' => '2019-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.85',
                        'ijazah' => null
                    ],
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Gadjah Mada',
                        'fakultas_program_studi' => 'Teknik',
                        'jurusan' => 'Teknik Informatika',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.90',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Suami', 'nama_kontak' => 'Michael Smith', 'no_emergency' => '081234567894']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567895',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Asuransi Jiwa',
                        'no_jaminan' => 'AJ001234567',
                        'jenis_fasilitas' => 'Asuransi Jiwa',
                        'provider' => 'Prudential',
                        'nilai_fasilitas' => 500000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Makan',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Makan',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 300000
                    ]
                ])
            ],
            [
                'nama' => 'Ahmad Fauzi',
                'npp' => 'NPP008', // Changed from NPP003
                'email' => 'ahmad.fauzi@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123458',
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567895',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
                'jabatan_nama' => 'Analyst',
                'jabatan_tunjangan' => 1500000,
                'posisi_nama' => 'Data Analyst',
                'posisi_tunjangan' => 1000000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Brawijaya',
                        'fakultas_program_studi' => 'Teknik Industri',
                        'jurusan' => 'Teknik Industri',
                        'thn_masuk' => '2016-08-01',
                        'thn_lulus' => '2020-07-01',
                        'ipk_nilai' => '3.65',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Istri', 'nama_kontak' => 'Siti Fauzi', 'no_emergency' => '081234567896']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567897',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ]
                ])
            ],
            [
                'nama' => 'Dewi Sartika',
                'npp' => 'NPP009', // Changed from NPP004
                'email' => 'dewi.sartika@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123459',
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567897',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. HR Rasuna Said No. 321, Jakarta Selatan',
                'jabatan_nama' => 'Admin',
                'jabatan_tunjangan' => 1200000,
                'posisi_nama' => 'Administrative Staff',
                'posisi_tunjangan' => 800000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'D3',
                        'sekolah_univ' => 'Politeknik Negeri Jakarta',
                        'fakultas_program_studi' => 'Administrasi Bisnis',
                        'jurusan' => 'Sekretaris',
                        'thn_masuk' => '2018-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.45',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ayah', 'nama_kontak' => 'Bambang Sartika', 'no_emergency' => '081234567898']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567899',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Transport',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Transport',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 400000
                    ]
                ])
            ],
            [
                'nama' => 'Rizki Pratama',
                'npp' => 'NPP010', // Changed from NPP005
                'email' => 'rizki.pratama@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123460',
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567899',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Thamrin No. 654, Jakarta Pusat',
                'jabatan_nama' => 'Junior Developer',
                'jabatan_tunjangan' => 1800000,
                'posisi_nama' => 'Frontend Developer',
                'posisi_tunjangan' => 1300000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Bina Nusantara',
                        'fakultas_program_studi' => 'Computer Science',
                        'jurusan' => 'Computer Science',
                        'thn_masuk' => '2017-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.70',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ibu', 'nama_kontak' => 'Sari Pratama', 'no_emergency' => '081234567900']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567900',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567901',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ]
                ])
            ],
            [
                'nama' => 'Sinta Maharani',
                'npp' => 'NPP011', // Changed from NPP006
                'email' => 'sinta.maharani@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123461',
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567901',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Casablanca No. 987, Jakarta Selatan',
                'jabatan_nama' => 'Marketing Staff',
                'jabatan_tunjangan' => 1600000,
                'posisi_nama' => 'Digital Marketing Specialist',
                'posisi_tunjangan' => 1100000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Padjadjaran',
                        'fakultas_program_studi' => 'Ilmu Komunikasi',
                        'jurusan' => 'Public Relations',
                        'thn_masuk' => '2016-08-01',
                        'thn_lulus' => '2020-07-01',
                        'ipk_nilai' => '3.55',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Kakak', 'nama_kontak' => 'Andi Maharani', 'no_emergency' => '081234567902']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567902',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Komunikasi',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Komunikasi',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 250000
                    ]
                ])
            ],
            [
                'nama' => 'Budi Santoso',
                'npp' => 'NPP012', // Changed from NPP007
                'email' => 'budi.santoso@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123462',
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567903',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Kuningan No. 147, Jakarta Selatan',
                'jabatan_nama' => 'Quality Assurance',
                'jabatan_tunjangan' => 1700000,
                'posisi_nama' => 'QA Engineer',
                'posisi_tunjangan' => 1200000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Institut Teknologi Sepuluh Nopember',
                        'fakultas_program_studi' => 'Teknik Elektro',
                        'jurusan' => 'Teknik Elektro',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.60',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Istri', 'nama_kontak' => 'Rina Santoso', 'no_emergency' => '081234567904'],
                    ['relationship' => 'Ayah', 'nama_kontak' => 'Sugeng Santoso', 'no_emergency' => '081234567905']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567903',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567904',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Kesehatan',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Kesehatan',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 350000
                    ]
                ])
            ],
            [
                'nama' => 'Ahmad Rahman',
                'npp' => 'NPP013', // Changed from NPP003
                'email' => 'ahmad.rahman@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123463', // Adjusted to ensure uniqueness
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567906',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Mangga Dua No. 789, Jakarta Utara',
                'jabatan_nama' => 'Software Engineer',
                'jabatan_tunjangan' => 1800000,
                'posisi_nama' => 'Backend Developer',
                'posisi_tunjangan' => 1200000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Bina Nusantara',
                        'fakultas_program_studi' => 'Fakultas Ilmu Komputer',
                        'jurusan' => 'Teknik Informatika',
                        'thn_masuk' => '2016-08-01',
                        'thn_lulus' => '2020-07-01',
                        'ipk_nilai' => '3.60',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Istri', 'nama_kontak' => 'Siti Rahman', 'no_emergency' => '081234567907'],
                    ['relationship' => 'Ibu', 'nama_kontak' => 'Aminah Rahman', 'no_emergency' => '081234567908']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567906',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567907',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ]
                ])
            ],
            [
                'nama' => 'Maria Sari',
                'npp' => 'NPP014', // Changed from NPP004
                'email' => 'maria.sari@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123464', // Adjusted to ensure uniqueness
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567909',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Tebet Raya No. 321, Jakarta Selatan',
                'jabatan_nama' => 'Quality Assurance',
                'jabatan_tunjangan' => 1600000,
                'posisi_nama' => 'QA Engineer',
                'posisi_tunjangan' => 1000000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Gunadarma',
                        'fakultas_program_studi' => 'Fakultas Ilmu Komputer',
                        'jurusan' => 'Sistem Informasi',
                        'thn_masuk' => '2017-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.65',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ayah', 'nama_kontak' => 'Budi Sari', 'no_emergency' => '081234567910']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567908',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Transport',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Transport',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 400000
                    ]
                ])
            ],
            [
                'nama' => 'Rizki Pratama',
                'npp' => 'NPP015', // Changed from NPP005
                'email' => 'rizki.pratama2@sucofindo.com', // Adjusted email to avoid duplication
                'password' => Hash::make('password123'),
                'nik' => '1234567890123465', // Adjusted to ensure uniqueness
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567911',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Kelapa Gading No. 654, Jakarta Utara',
                'jabatan_nama' => 'Frontend Developer',
                'jabatan_tunjangan' => 1700000,
                'posisi_nama' => 'UI/UX Developer',
                'posisi_tunjangan' => 1100000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'D3',
                        'sekolah_univ' => 'Politeknik Negeri Jakarta',
                        'fakultas_program_studi' => 'Teknik Informatika',
                        'jurusan' => 'Multimedia',
                        'thn_masuk' => '2018-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.45',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ayah', 'nama_kontak' => 'Agus Pratama', 'no_emergency' => '081234567912'],
                    ['relationship' => 'Ibu', 'nama_kontak' => 'Rina Pratama', 'no_emergency' => '081234567913']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567910',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Makan',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Makan',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 250000
                    ]
                ])
            ],
            [
                'nama' => 'Dewi Kartika',
                'npp' => 'NPP016', // Changed from NPP006
                'email' => 'dewi.kartika@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123466', // Adjusted to ensure uniqueness
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567914',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Cipinang No. 987, Jakarta Timur',
                'jabatan_nama' => 'Data Analyst',
                'jabatan_tunjangan' => 1900000,
                'posisi_nama' => 'Business Intelligence',
                'posisi_tunjangan' => 1300000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Padjadjaran',
                        'fakultas_program_studi' => 'MIPA',
                        'jurusan' => 'Statistika',
                        'thn_masuk' => '2016-08-01',
                        'thn_lulus' => '2020-07-01',
                        'ipk_nilai' => '3.70',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Suami', 'nama_kontak' => 'Andi Kartika', 'no_emergency' => '081234567915']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567911',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567912',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ],
                    [
                        'nama_jaminan' => 'Asuransi Kesehatan',
                        'no_jaminan' => 'AK001234567',
                        'jenis_fasilitas' => 'Asuransi Kesehatan',
                        'provider' => 'Allianz',
                        'nilai_fasilitas' => 350000
                    ]
                ])
            ],
            [
                'nama' => 'Bayu Nugroho',
                'npp' => 'NPP017', // Changed from NPP007
                'email' => 'bayu.nugroho@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123467', // Adjusted to ensure uniqueness
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567916',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Pancoran No. 147, Jakarta Selatan',
                'jabatan_nama' => 'System Administrator',
                'jabatan_tunjangan' => 1750000,
                'posisi_nama' => 'DevOps Engineer',
                'posisi_tunjangan' => 1250000,
                'pendidikan_list' => json_encode([
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Trisakti',
                        'fakultas_program_studi' => 'Fakultas Teknologi Industri',
                        'jurusan' => 'Teknik Informatika',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.55',
                        'ijazah' => null
                    ]
                ]),
                'emergency_contacts' => json_encode([
                    ['relationship' => 'Ayah', 'nama_kontak' => 'Sugeng Nugroho', 'no_emergency' => '081234567917'],
                    ['relationship' => 'Kakak', 'nama_kontak' => 'Indra Nugroho', 'no_emergency' => '081234567918']
                ]),
                'fasilitas_list' => json_encode([
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567913',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Transport',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Transport',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 450000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Komunikasi',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Komunikasi',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 200000
                    ]
                ])
            ]
        ];

        foreach ($pegawaiData as $data) {
            Pegawai::create($data);
        }
    }
}
