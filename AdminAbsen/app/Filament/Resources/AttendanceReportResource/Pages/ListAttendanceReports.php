<?php

namespace App\Filament\Resources\AttendanceReportResource\Pages;

use App\Filament\Resources\AttendanceReportResource;
use App\Exports\AttendanceReportExport;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ListAttendanceReports extends ListRecords
{
    protected static string $resource = AttendanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_report_excel')
                ->label('Export Rekap ke Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth())
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now()->endOfMonth())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filename = 'rekap_absensi_' . 
                               Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . 
                               Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';
                    
                    return response()->streamDownload(function () use ($data) {
                        return Excel::raw(
                            new AttendanceReportExport($data['start_date'], $data['end_date']),
                            \Maatwebsite\Excel\Excel::XLSX
                        );
                    }, $filename);
                }),
                
            Actions\Action::make('export_report_pdf')
                ->label('Export Rekap ke PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth())
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now()->endOfMonth())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $pegawaiData = Pegawai::select([
                            'pegawais.*',
                            DB::raw('COUNT(attendances.id) as total_hadir'),
                            DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                            DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                            DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                            DB::raw('AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL 
                                           THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60 
                                           ELSE NULL END) as avg_work_minutes'),
                        ])
                        ->leftJoin('attendances', function($join) use ($data) {
                            $join->on('pegawais.id', '=', 'attendances.user_id')
                                 ->whereBetween('attendances.created_at', [$data['start_date'], $data['end_date']]);
                        })
                        ->where('pegawais.status', 'active')
                        ->groupBy('pegawais.id')
                        ->orderBy('total_hadir', 'desc')
                        ->get();

                    $workDays = $this->getWorkDaysInPeriod($data['start_date'], $data['end_date']);
                    
                    $mappedData = $pegawaiData->map(function ($pegawai) use ($workDays) {
                        $tingkatKehadiran = $workDays > 0 ? round(($pegawai->total_hadir / $workDays) * 100, 1) : 0;
                        
                        return [
                            'nama' => $pegawai->nama,
                            'npp' => $pegawai->npp,
                            'jabatan' => $pegawai->jabatan_nama ?? '-',
                            'total_hadir' => $pegawai->total_hadir ?? 0,
                            'total_terlambat' => $pegawai->total_terlambat ?? 0,
                            'total_lembur' => $pegawai->total_overtime_minutes ? 
                                number_format($pegawai->total_overtime_minutes / 60, 1) . ' jam' : '0 jam',
                            'tingkat_kehadiran' => $tingkatKehadiran . '%',
                            'status' => ucfirst($pegawai->status),
                        ];
                    });

                    $avgAttendance = $pegawaiData->avg('total_hadir');
                    $avgPercentage = $workDays > 0 ? round(($avgAttendance / $workDays) * 100, 1) : 0;

                    $pdf = Pdf::loadView('exports.attendance-report-pdf', [
                        'title' => 'Rekap Absensi Karyawan',
                        'period' => Carbon::parse($data['start_date'])->format('d M Y') . ' - ' . Carbon::parse($data['end_date'])->format('d M Y'),
                        'headers' => [
                            'Nama', 'NPP', 'Jabatan', 'Total Hadir', 'Total Terlambat', 
                            'Total Lembur', 'Tingkat Kehadiran', 'Status'
                        ],
                        'data' => $mappedData,
                        'summary' => [
                            'total_employees' => $pegawaiData->count(),
                            'work_days' => $workDays,
                            'avg_attendance' => $avgPercentage . '%',
                        ]
                    ]);

                    $filename = 'rekap_absensi_' . 
                               Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . 
                               Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, $filename);
                }),
        ];
    }
    
    private function getWorkDaysInPeriod($startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $workDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }
        
        return $workDays;
    }
}
