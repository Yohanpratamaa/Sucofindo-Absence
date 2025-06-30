<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function print(Izin $izin)
    {
        // Load izin with relationships
        $izin->load(['user', 'approvedBy']);

        // Generate PDF
        $pdf = Pdf::loadView('exports.izin-detail-pdf', [
            'izin' => $izin,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ]);

        $filename = 'izin_' .
                   str_replace(' ', '_', $izin->user->nama) . '_' .
                   $izin->created_at->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadDocument(Izin $izin)
    {
        if (!$izin->dokumen_pendukung) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $izin->dokumen_pendukung);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath);
    }

    public function previewDocument(Izin $izin)
    {
        if (!$izin->dokumen_pendukung) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $izin->dokumen_pendukung);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $fileExtension = strtolower(pathinfo($izin->dokumen_pendukung, PATHINFO_EXTENSION));

        // Set appropriate content type
        $contentType = match($fileExtension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream'
        };

        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . basename($izin->dokumen_pendukung) . '"'
        ]);
    }
}
