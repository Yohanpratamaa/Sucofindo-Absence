<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DocumentSampleSeeder extends Seeder
{
    public function run()
    {
        // Create sample directories if they don't exist
        if (!Storage::disk('public')->exists('izin-documents')) {
            Storage::disk('public')->makeDirectory('izin-documents');
        }

        // Create sample dummy files for testing
        $sampleDocuments = [
            'izin-documents/surat-dokter-sample.pdf',
            'izin-documents/surat-cuti-sample.pdf',
            'izin-documents/undangan-sample.jpg',
            'izin-documents/sertifikat-sample.pdf',
        ];

        foreach ($sampleDocuments as $document) {
            if (!Storage::disk('public')->exists($document)) {
                // Create a simple placeholder file
                Storage::disk('public')->put($document, 'Sample document content for testing purposes.');
            }
        }

        // Update existing izin records to have document references
        $izins = Izin::take(4)->get();

        if ($izins->count() >= 4) {
            $izins[0]->update(['dokumen_pendukung' => 'izin-documents/surat-dokter-sample.pdf']);
            $izins[1]->update(['dokumen_pendukung' => 'izin-documents/surat-cuti-sample.pdf']);
            $izins[2]->update(['dokumen_pendukung' => 'izin-documents/undangan-sample.jpg']);
            $izins[3]->update(['dokumen_pendukung' => 'izin-documents/sertifikat-sample.pdf']);
        }

        $this->command->info('Sample documents created and linked to izin records!');
    }
}
