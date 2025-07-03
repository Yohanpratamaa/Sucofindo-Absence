<?php

namespace App\Exports;

use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class OvertimeApprovalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $userId = null, $status = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
        $this->userId = $userId;
        $this->status = $status;
    }

    public function collection()
    {
        // Query data lembur tim yang sama seperti di resource
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        $query = OvertimeAssignment::with(['user', 'assignedBy', 'approvedBy'])
            ->whereIn('user_id', $teamMembers)
            ->whereBetween('assigned_at', [$this->startDate, $this->endDate]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('assigned_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Penugasan',
            'Nama Pegawai',
            'NPP',
            'Jabatan',
            'ID Lembur',
            'Keterangan Lembur',
            'Ditugaskan Oleh',
            'Status',
            'Tanggal Disetujui',
            'Disetujui Oleh',
            'Info Persetujuan',
            'Dibuat Pada'
        ];
    }

    public function map($overtime): array
    {
        // Format status
        $statusFormatted = match ($overtime->status) {
            'Assigned' => 'Ditugaskan',
            'Accepted' => 'Diterima',
            'Rejected' => 'Ditolak',
            default => ucfirst($overtime->status),
        };

        return [
            $overtime->assigned_at ? $overtime->assigned_at->format('d/m/Y H:i') : '-',
            $overtime->user->nama ?? '-',
            $overtime->user->npp ?? '-',
            $overtime->user->jabatan_nama ?? '-',
            $overtime->overtime_id ?? '-',
            $overtime->keterangan ?? '-',
            $overtime->assignedBy->nama ?? '-',
            $statusFormatted,
            $overtime->approved_at ? $overtime->approved_at->format('d/m/Y H:i') : '-',
            $overtime->approvedBy->nama ?? '-',
            $overtime->approval_info ?? '-',
            $overtime->created_at ? $overtime->created_at->format('d/m/Y H:i') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->collection()) + 1;

        return [
            // Style the first row as a header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF8B5A2B'], // Brown color untuk overtime
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Style untuk data rows
            'A2:L' . $lastRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // Tanggal Penugasan
            'B' => 25, // Nama Pegawai
            'C' => 15, // NPP
            'D' => 25, // Jabatan
            'E' => 20, // ID Lembur
            'F' => 35, // Keterangan Lembur - lebar untuk menampilkan teks lengkap
            'G' => 25, // Ditugaskan Oleh
            'H' => 15, // Status
            'I' => 18, // Tanggal Disetujui
            'J' => 25, // Disetujui Oleh
            'K' => 40, // Info Persetujuan - lebar untuk menampilkan teks lengkap
            'L' => 18, // Dibuat Pada
        ];
    }

    public function title(): string
    {
        $period = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
        return "Pengajuan Lembur - {$period}";
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = count($this->collection()) + 1;

                // Set auto height untuk semua baris data
                for ($i = 2; $i <= $lastRow; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(-1); // Auto height
                }

                // Set specific styling untuk kolom keterangan dan info persetujuan agar wrap text benar-benar bekerja
                $event->sheet->getDelegate()
                    ->getStyle('F2:F' . $lastRow)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_TOP);

                $event->sheet->getDelegate()
                    ->getStyle('K2:K' . $lastRow)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_TOP);

                // Set border untuk semua data
                $event->sheet->getDelegate()
                    ->getStyle('A1:L' . $lastRow)
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);
            },
        ];
    }
}
