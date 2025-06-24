<?php

namespace Database\Seeders;

use App\Models\Izin;
use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class IzinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa pegawai untuk contoh data
        $pegawais = Pegawai::limit(5)->get();

        if ($pegawais->isEmpty()) {
            $this->command->warn('Tidak ada data pegawai. Jalankan PegawaiSeeder terlebih dahulu.');
            return;
        }

        $izins = [
            [
                'user_id' => $pegawais->get(0)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(1),
                'tanggal_akhir' => Carbon::now()->addDays(1),
                'jenis_izin' => 'sakit',
                'keterangan' => 'Demam tinggi dan perlu istirahat total. Sudah memeriksakan diri ke dokter.',
                'dokumen_pendukung' => 'surat-dokter-001.pdf',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $pegawais->get(1)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(3),
                'tanggal_akhir' => Carbon::now()->addDays(5),
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti tahunan untuk liburan keluarga ke Bali. Sudah direncanakan dari 3 bulan yang lalu.',
                'dokumen_pendukung' => null,
                'approved_by' => $pegawais->get(0)?->id ?? 1,
                'approved_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => $pegawais->get(2)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->subDays(1),
                'tanggal_akhir' => Carbon::now()->subDays(1),
                'jenis_izin' => 'izin',
                'keterangan' => 'Menghadiri pernikahan saudara kandung yang tidak bisa ditunda.',
                'dokumen_pendukung' => 'undangan-pernikahan.jpg',
                'approved_by' => $pegawais->get(0)?->id ?? 1,
                'approved_at' => null, // Ditolak
            ],
            [
                'user_id' => $pegawais->get(3)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(7),
                'tanggal_akhir' => Carbon::now()->addDays(9),
                'jenis_izin' => 'sakit',
                'keterangan' => 'Operasi usus buntu yang sudah dijadwalkan oleh rumah sakit.',
                'dokumen_pendukung' => 'surat-rs-operasi.pdf',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $pegawais->get(4)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(14),
                'tanggal_akhir' => Carbon::now()->addDays(18),
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti melahirkan untuk istri. Perlu mendampingi selama proses persalinan dan pasca melahirkan.',
                'dokumen_pendukung' => 'surat-dokter-kandungan.pdf',
                'approved_by' => $pegawais->get(0)?->id ?? 1,
                'approved_at' => Carbon::now()->subHours(5),
            ],
            [
                'user_id' => $pegawais->get(0)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(21),
                'tanggal_akhir' => Carbon::now()->addDays(21),
                'jenis_izin' => 'izin',
                'keterangan' => 'Mengurus dokumen kependudukan di kantor catatan sipil yang hanya buka hari kerja.',
                'dokumen_pendukung' => null,
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $pegawais->get(1)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->subDays(5),
                'tanggal_akhir' => Carbon::now()->subDays(3),
                'jenis_izin' => 'sakit',
                'keterangan' => 'Terkena COVID-19 dan harus menjalani isolasi mandiri sesuai protokol kesehatan.',
                'dokumen_pendukung' => 'hasil-pcr-test.pdf',
                'approved_by' => $pegawais->get(0)?->id ?? 1,
                'approved_at' => Carbon::now()->subDays(6),
            ],
            [
                'user_id' => $pegawais->get(2)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(30),
                'tanggal_akhir' => Carbon::now()->addDays(35),
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti untuk umroh bersama keluarga. Sudah ada konfirmasi dari travel agent.',
                'dokumen_pendukung' => 'itinerary-umroh.pdf',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $pegawais->get(3)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->subDays(10),
                'tanggal_akhir' => Carbon::now()->subDays(10),
                'jenis_izin' => 'izin',
                'keterangan' => 'Menghadiri wisuda anak di universitas.',
                'dokumen_pendukung' => 'undangan-wisuda.jpg',
                'approved_by' => $pegawais->get(0)?->id ?? 1,
                'approved_at' => null, // Ditolak
            ],
            [
                'user_id' => $pegawais->get(4)?->id ?? 1,
                'tanggal_mulai' => Carbon::now()->addDays(45),
                'tanggal_akhir' => Carbon::now()->addDays(47),
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti untuk menghadiri reuni sekolah yang diadakan 25 tahun sekali.',
                'dokumen_pendukung' => null,
                'approved_by' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($izins as $izin) {
            Izin::create($izin);
        }

        $this->command->info('✅ Data izin contoh berhasil dibuat:');
        $this->command->info('   📝 Total: ' . count($izins) . ' data izin');
        $this->command->info('   ⏳ Pending: ' . collect($izins)->whereNull('approved_by')->count() . ' izin');
        $this->command->info('   ✅ Approved: ' . collect($izins)->where('approved_by', '!=', null)->where('approved_at', '!=', null)->count() . ' izin');
        $this->command->info('   ❌ Rejected: ' . collect($izins)->where('approved_by', '!=', null)->whereNull('approved_at')->count() . ' izin');
    }
}
