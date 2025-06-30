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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        try {
            $this->startDate = $startDate ?
                Carbon::parse($startDate)->format('Y-m-d H:i:s') :
                Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');

            $this->endDate = $endDate ?
                Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s') :
                Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            \Log::error('AttendanceReportExport constructor error: ' . $e->getMessage());
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }
    }

    public function collection()
    {
        try {
            return Pegawai::select([
                    'pegawais.id',
                    'pegawais.nama',
                    'pegawais.npp',
                    'pegawais.jabatan_nama',
                    'pegawais.posisi_nama',
                    'pegawais.status',
                    DB::raw('COALESCE(COUNT(attendances.id), 0) as total_hadir'),
                    DB::raw('COALESCE(COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END), 0) as total_terlambat'),
                    DB::raw('COALESCE(COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END), 0) as total_tidak_checkout'),
                    DB::raw('COALESCE(SUM(attendances.overtime), 0) as total_overtime_minutes'),
                    DB::raw('COALESCE(AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL
                                   THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60
                                   ELSE NULL END), 0) as avg_work_minutes'),
                ])
                ->leftJoin('attendances', function($join) {
                    $join->on('pegawais.id', '=', 'attendances.user_id')
                         ->whereBetween('attendances.created_at', [$this->startDate, $this->endDate]);
                })
                ->where('pegawais.status', 'active')
                ->groupBy('pegawais.id', 'pegawais.nama', 'pegawais.npp', 'pegawais.jabatan_nama', 'pegawais.posisi_nama', 'pegawais.status')
                ->orderBy('total_hadir', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::error('AttendanceReportExport collection error: ' . $e->getMessage());
            return collect([]);
        }
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
        try {
            $workDays = $this->getWorkDaysInPeriod();
            $totalHadir = (int) ($pegawai->total_hadir ?? 0);
            $tingkatKehadiran = $workDays > 0 ? round(($totalHadir / $workDays) * 100, 1) : 0;

            $totalOvertimeMinutes = (float) ($pegawai->total_overtime_minutes ?? 0);
            $totalOvertimeHours = $totalOvertimeMinutes > 0 ?
                number_format($totalOvertimeMinutes / 60, 1) : '0.0';

            $avgWorkMinutes = (float) ($pegawai->avg_work_minutes ?? 0);
            $avgWorkHours = $avgWorkMinutes > 0 ?
                number_format($avgWorkMinutes / 60, 1) : '0.0';

            return [
                $pegawai->nama ?? '-',
                $pegawai->npp ?? '-',
                $pegawai->jabatan_nama ?? '-',
                $pegawai->posisi_nama ?? '-',
                $totalHadir,
                (int) ($pegawai->total_terlambat ?? 0),
                (int) ($pegawai->total_tidak_checkout ?? 0),
                $totalOvertimeHours,
                $avgWorkHours,
                $tingkatKehadiran . '%',
                ucfirst($pegawai->status ?? 'inactive'),
            ];
        } catch (\Exception $e) {
            \Log::error('AttendanceReportExport map error: ' . $e->getMessage());
            return [
                $pegawai->nama ?? '-',
                $pegawai->npp ?? '-',
                '-',
                '-',
                0,
                0,
                0,
                '0.0',
                '0.0',
                '0%',
                'inactive',
            ];
        }
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
