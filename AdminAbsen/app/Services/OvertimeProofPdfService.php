<?php

namespace App\Services;

use App\Models\OvertimeAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OvertimeProofPdfService
{
    public function generateOvertimeProof(OvertimeAssignment $overtime)
    {
        $qrCodeData = $this->generateQrCodeData($overtime);

        $data = [
            'overtime' => $overtime,
            'generatedAt' => Carbon::now('Asia/Jakarta'),
            'qrCodeData' => $qrCodeData,
            'qrCodeImage' => null, // Disable QR image for now, use text fallback
        ];

        $pdf = Pdf::loadView('pdf.overtime-proof', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    public function generateOvertimeProofPdf(OvertimeAssignment $overtime)
    {
        $pdf = $this->generateOvertimeProof($overtime);
        return $pdf->output();
    }

    public function downloadOvertimeProofPdf(OvertimeAssignment $overtime, $filename = null)
    {
        $pdf = $this->generateOvertimeProof($overtime);
        $filename = $filename ?? "bukti-lembur-{$overtime->overtime_id}.pdf";

        return $pdf->download($filename);
    }

    private function generateQrCodeData(OvertimeAssignment $overtime)
    {
        // Generate QR code data for verification
        return "OVERTIME-ID:{$overtime->overtime_id}|USER:{$overtime->user->nama}|DATE:{$overtime->tanggal_lembur->format('Y-m-d')}|STATUS:{$overtime->status}";
    }

    private function generateQrCodeImage($data)
    {
        try {
            // Try to generate QR code as SVG (doesn't require imagick)
            $qrCode = QrCode::format('svg')
                ->size(100)
                ->margin(0)
                ->generate($data);

            // Return SVG as data URI for embedding in PDF
            return 'data:image/svg+xml;charset=utf-8,' . urlencode($qrCode);
        } catch (\Exception $e) {
            // If QR code generation fails, return null to use fallback
            return null;
        }
    }
}
