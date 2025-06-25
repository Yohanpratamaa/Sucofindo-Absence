<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
    }

    public function collection()
    {
        return Pegawai::select([
                'pegawais.*',
                DB::raw('COUNT(attendances.id) as total_hadir'),
                DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                DB::raw('AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL
                               THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60
                               ELSE NULL END) as avg_work_minutes'),
            ])
            ->leftJoin('attendances', function($join) {
                $join->on('pegawais.id', '=', 'attendances.user_id')
                     ->whereBetween('attendances.created_at', [$this->startDate, $this->endDate]);
            })
            ->where('pegawais.status', 'active')
            ->groupBy('pegawais.id')
            ->orderBy('total_hadir', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'NPP',
            'Jabatan',
            'Posisi',
            'Total Hadir',
            'Total Terlambat',
            'Total Tidak Check Out',
            'Total Lembur (Jam)',
            'Rata-rata Kerja/Hari (Jam)',
            'Tingkat Kehadiran (%)',
            'Status Karyawan',
        ];
    }

    public function map($pegawai): array
    {
        $workDays = $this->getWorkDaysInPeriod();
        $tingkatKehadiran = $workDays > 0 ? round(($pegawai->total_hadir / $workDays) * 100, 1) : 0;

        $totalOvertimeHours = $pegawai->total_overtime_minutes ?
            number_format($pegawai->total_overtime_minutes / 60, 1) : '0';

        $avgWorkHours = $pegawai->avg_work_minutes ?
            number_format($pegawai->avg_work_minutes / 60, 1) : '0';

        return [
            $pegawai->nama,
            $pegawai->npp,
            $pegawai->jabatan_nama ?? '-',
            $pegawai->posisi_nama ?? '-',
            $pegawai->total_hadir ?? 0,
            $pegawai->total_terlambat ?? 0,
            $pegawai->total_tidak_checkout ?? 0,
            $totalOvertimeHours,
            $avgWorkHours,
            $tingkatKehadiran,
            ucfirst($pegawai->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '366092'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Nama Karyawan
            'B' => 12, // NPP
            'C' => 15, // Jabatan
            'D' => 15, // Posisi
            'E' => 12, // Total Hadir
            'F' => 15, // Total Terlambat
            'G' => 18, // Total Tidak Check Out
            'H' => 15, // Total Lembur
            'I' => 20, // Rata-rata Kerja/Hari
            'J' => 18, // Tingkat Kehadiran
            'K' => 15, // Status Karyawan
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi ' . Carbon::parse($this->startDate)->format('d M Y') .
               ' - ' . Carbon::parse($this->endDate)->format('d M Y');
    }

    private function getWorkDaysInPeriod(): int
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        $workDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }

        return $workDays;
    }
}
