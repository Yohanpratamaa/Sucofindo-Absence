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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class OvertimeAssignmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $userId;
    protected $status;
    protected $assignedBy;

    public function __construct($startDate = null, $endDate = null, $userId = null, $status = null, $assignedBy = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
        $this->userId = $userId;
        $this->status = $status;
        $this->assignedBy = $assignedBy;
    }

    public function collection()
    {
        $query = OvertimeAssignment::with(['user', 'assignedBy', 'approvedBy', 'assignBy'])
            ->whereBetween('assigned_at', [$this->startDate, $this->endDate]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->assignedBy) {
            $query->where('assigned_by', $this->assignedBy);
        }

        return $query->orderBy('assigned_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Waktu Penugasan',
            'Nama Karyawan',
            'NPP',
            'Jabatan',
            'ID Lembur',
            'Ditugaskan Oleh',
            'Status',
            'Durasi Assignment',
            'Disetujui Oleh',
            'Tanggal Disetujui',
            'Di-assign Ulang Oleh',
            'Info Persetujuan'
        ];
    }

    public function map($overtime): array
    {
        return [
            $overtime->assigned_at ? $overtime->assigned_at->format('d/m/Y H:i') : '-',
            $overtime->user->nama ?? '-',
            $overtime->user->npp ?? '-',
            $overtime->user->jabatan->nama ?? '-',
            $overtime->overtime_id ?? '-',
            $overtime->assignedBy->nama ?? '-',
            $overtime->status_badge['label'],
            $overtime->durasi_assignment ?? '-',
            $overtime->approvedBy->nama ?? '-',
            $overtime->approved_at ? $overtime->approved_at->format('d/m/Y H:i') : '-',
            $overtime->assignBy->nama ?? '-',
            $overtime->approval_info
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
                    'startColor' => ['argb' => '059669'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // Waktu Penugasan
            'B' => 25, // Nama Karyawan
            'C' => 15, // NPP
            'D' => 20, // Jabatan
            'E' => 15, // ID Lembur
            'F' => 25, // Ditugaskan Oleh
            'G' => 15, // Status
            'H' => 18, // Durasi Assignment
            'I' => 25, // Disetujui Oleh
            'J' => 18, // Tanggal Disetujui
            'K' => 25, // Di-assign Ulang Oleh
            'L' => 30, // Info Persetujuan
        ];
    }

    public function title(): string
    {
        $period = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
        return "Data Lembur - {$period}";
    }
}
