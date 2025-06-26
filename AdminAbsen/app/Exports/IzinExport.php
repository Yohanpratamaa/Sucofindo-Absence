<?php

namespace App\Exports;

use App\Models\Izin;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class IzinExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $jenisIzin;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $userId = null, $jenisIzin = null, $status = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
        $this->userId = $userId;
        $this->jenisIzin = $jenisIzin;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Izin::with(['user', 'approvedBy'])
            ->whereBetween('tanggal_mulai', [$this->startDate, $this->endDate]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->jenisIzin) {
            $query->where('jenis_izin', $this->jenisIzin);
        }

        if ($this->status) {
            switch ($this->status) {
                case 'pending':
                    $query->pending();
                    break;
                case 'approved':
                    $query->approved();
                    break;
                case 'rejected':
                    $query->rejected();
                    break;
            }
        }

        return $query->orderBy('tanggal_mulai', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengajuan',
            'Nama Karyawan',
            'NPP',
            'Jabatan',
            'Jenis Izin',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Durasi (Hari)',
            'Keterangan',
            'Status',
            'Disetujui Oleh',
            'Tanggal Disetujui',
            'Info Persetujuan'
        ];
    }

    public function map($izin): array
    {
        return [
            $izin->created_at ? $izin->created_at->format('d/m/Y H:i') : '-',
            $izin->user->nama ?? '-',
            $izin->user->npp ?? '-',
            $izin->user->jabatan->nama ?? '-',
            ucfirst($izin->jenis_izin),
            $izin->tanggal_mulai ? Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') : '-',
            $izin->tanggal_akhir ? Carbon::parse($izin->tanggal_akhir)->format('d/m/Y') : '-',
            $izin->durasi_hari . ' hari',
            $izin->keterangan ?? '-',
            $izin->status_badge['label'],
            $izin->approvedBy->nama ?? '-',
            $izin->approved_at ? $izin->approved_at->format('d/m/Y H:i') : '-',
            $izin->approval_info
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as a header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2563EB'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // Tanggal Pengajuan
            'B' => 25, // Nama Karyawan
            'C' => 15, // NPP
            'D' => 20, // Jabatan
            'E' => 15, // Jenis Izin
            'F' => 15, // Tanggal Mulai
            'G' => 15, // Tanggal Akhir
            'H' => 15, // Durasi
            'I' => 30, // Keterangan
            'J' => 15, // Status
            'K' => 25, // Disetujui Oleh
            'L' => 18, // Tanggal Disetujui
            'M' => 30, // Info Persetujuan
        ];
    }

    public function title(): string
    {
        $period = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
        return "Data Izin - {$period}";
    }
}
