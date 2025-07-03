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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class IzinApprovalExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithEvents
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
        // Gunakan query yang sama seperti di resource
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        $query = Izin::with(['user', 'approvedBy'])
            ->whereIn('user_id', $teamMembers)
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->jenisIzin) {
            $query->where('jenis_izin', $this->jenisIzin);
        }

        if ($this->status) {
            switch ($this->status) {
                case 'pending':
                    $query->whereNull('approved_by');
                    break;
                case 'approved':
                    $query->whereNotNull('approved_by')->whereNotNull('approved_at');
                    break;
                case 'rejected':
                    $query->whereNotNull('approved_by')->whereNull('approved_at');
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengajuan',
            'Nama Pegawai',
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
            'Dokumen Pendukung'
        ];
    }

    public function map($izin): array
    {
        // Hitung durasi
        $durasi = '-';
        if ($izin->tanggal_mulai && $izin->tanggal_akhir) {
            $start = Carbon::parse($izin->tanggal_mulai);
            $end = Carbon::parse($izin->tanggal_akhir);
            $durasi = $start->diffInDays($end) + 1 . ' hari';
        }

        // Status approval
        $status = match (true) {
            is_null($izin->approved_by) => 'Menunggu',
            !is_null($izin->approved_at) => 'Disetujui',
            default => 'Ditolak',
        };

        return [
            $izin->created_at ? $izin->created_at->format('d/m/Y H:i') : '-',
            $izin->user->nama ?? '-',
            $izin->user->npp ?? '-',
            $izin->user->jabatan_nama ?? '-',
            ucfirst($izin->jenis_izin),
            $izin->tanggal_mulai ? Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') : '-',
            $izin->tanggal_akhir ? Carbon::parse($izin->tanggal_akhir)->format('d/m/Y') : '-',
            $durasi,
            $izin->keterangan ?? '-',
            $status,
            $izin->approvedBy->nama ?? '-',
            $izin->approved_at ? $izin->approved_at->format('d/m/Y H:i') : '-',
            $izin->dokumen_pendukung ? 'Ada' : 'Tidak Ada'
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
                    'startColor' => ['argb' => '2563EB'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // Style untuk data rows
            'A2:M' . $lastRow => [
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
            'A' => 18, // Tanggal Pengajuan
            'B' => 25, // Nama Pegawai
            'C' => 15, // NPP
            'D' => 25, // Jabatan
            'E' => 15, // Jenis Izin
            'F' => 15, // Tanggal Mulai
            'G' => 15, // Tanggal Akhir
            'H' => 15, // Durasi
            'I' => 50, // Keterangan - diperlebar untuk menampilkan teks lengkap
            'J' => 15, // Status
            'K' => 25, // Disetujui Oleh
            'L' => 18, // Tanggal Disetujui
            'M' => 20, // Dokumen Pendukung
        ];
    }

    public function title(): string
    {
        $period = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
        return "Persetujuan Izin Tim - {$period}";
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

                // Set specific styling untuk kolom keterangan agar wrap text benar-benar bekerja
                $event->sheet->getDelegate()
                    ->getStyle('I2:I' . $lastRow)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_TOP);

                // Set border untuk semua data
                $event->sheet->getDelegate()
                    ->getStyle('A1:M' . $lastRow)
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
