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
use App\Models\Attendance;
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
                    try {
                        $startDate = Carbon::parse($data['start_date'])->format('Y-m-d');
                        $endDate = Carbon::parse($data['end_date'])->format('Y-m-d');

                        $filename = 'rekap_absensi_' . $startDate . '_to_' . $endDate . '.xlsx';

                        \Filament\Notifications\Notification::make()
                            ->title('Export Sedang Diproses')
                            ->body('File Excel sedang digenerate, mohon tunggu...')
                            ->info()
                            ->send();

                        // Gunakan Excel::download langsung daripada streamDownload
                        return Excel::download(
                            new AttendanceReportExport($data['start_date'], $data['end_date']),
                            $filename
                        );

                    } catch (\Exception $e) {
                        \Log::error('Excel export error: ' . $e->getMessage());

                        \Filament\Notifications\Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export Excel: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
                }),

            // Export Rekap Absensi ke PDF
            Actions\Action::make('export_rekap_pdf')
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
                    Forms\Components\Select::make('jabatan')
                        ->label('Filter Jabatan (Opsional)')
                        ->options(\App\Models\Pegawai::distinct()->pluck('jabatan_nama', 'jabatan_nama'))
                        ->searchable()
                        ->placeholder('Semua Jabatan'),
                ])
                ->action(function (array $data) {
                    try {
                        // Query data rekap per karyawan
                        $query = Pegawai::select([
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
                            ->where('pegawais.status', 'active');

                        if (!empty($data['jabatan'])) {
                            $query->where('pegawais.jabatan_nama', $data['jabatan']);
                        }

                        $pegawais = $query->groupBy('pegawais.id')
                                         ->orderBy('total_hadir', 'desc')
                                         ->get();

                        // Prepare data untuk PDF
                        $headers = [
                            'Nama Karyawan',
                            'NPP',
                            'Jabatan',
                            'Total Hadir',
                            'Total Terlambat',
                            'Tidak Check Out',
                            'Total Lembur (Jam)',
                            'Tingkat Kehadiran (%)',
                        ];

                        $data_rows = [];
                        $workDays = $this->getWorkDaysInPeriod($data['start_date'], $data['end_date']);

                        foreach ($pegawais as $pegawai) {
                            $tingkatKehadiran = $workDays > 0 ? round(($pegawai->total_hadir / $workDays) * 100, 1) : 0;
                            $totalOvertimeHours = $pegawai->total_overtime_minutes ?
                                                round($pegawai->total_overtime_minutes / 60, 1) : 0;

                            $data_rows[] = [
                                'nama_karyawan' => $pegawai->nama,
                                'npp' => $pegawai->npp ?? '-',
                                'jabatan' => $pegawai->jabatan_nama ?? '-',
                                'total_hadir' => $pegawai->total_hadir,
                                'total_terlambat' => $pegawai->total_terlambat,
                                'total_tidak_checkout' => $pegawai->total_tidak_checkout,
                                'total_overtime_hours' => $totalOvertimeHours . ' jam',
                                'tingkat_kehadiran' => $tingkatKehadiran . '%',
                            ];
                        }

                        $filename = 'rekap_absensi_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                        // Summary data
                        $totalEmployees = $pegawais->count();
                        $avgAttendance = $pegawais->avg('total_hadir') ?: 0;
                        $avgAttendancePercent = $workDays > 0 ? round(($avgAttendance / $workDays) * 100, 1) : 0;

                        $pdf = Pdf::loadView('exports.attendance-report-pdf', [
                            'title' => 'Rekap Absensi Karyawan',
                            'period' => Carbon::parse($data['start_date'])->format('d/m/Y') . ' - ' .
                                       Carbon::parse($data['end_date'])->format('d/m/Y'),
                            'headers' => $headers,
                            'data' => $data_rows,
                            'summary' => [
                                'total_employees' => $totalEmployees,
                                'work_days' => $workDays,
                                'avg_attendance' => $avgAttendancePercent,
                            ],
                        ])->setPaper('a4', 'landscape');

                        \Filament\Notifications\Notification::make()
                            ->title('Export Berhasil')
                            ->body('Rekap absensi berhasil digenerate ke PDF.')
                            ->success()
                            ->send();

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export rekap PDF: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
                }),

            // Quick export bulan ini
            Actions\Action::make('export_bulan_ini')
                ->label('Export Bulan Ini')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->action(function () {
                    try {
                        $startDate = now()->startOfMonth();
                        $endDate = now()->endOfMonth();

                        // Query data rekap per karyawan
                        $pegawais = Pegawai::select([
                                'pegawais.*',
                                DB::raw('COUNT(attendances.id) as total_hadir'),
                                DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                                DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                                DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                            ])
                            ->leftJoin('attendances', function($join) use ($startDate, $endDate) {
                                $join->on('pegawais.id', '=', 'attendances.user_id')
                                     ->whereBetween('attendances.created_at', [$startDate, $endDate]);
                            })
                            ->where('pegawais.status', 'active')
                            ->groupBy('pegawais.id')
                            ->orderBy('total_hadir', 'desc')
                            ->get();

                        // Prepare data untuk PDF
                        $headers = [
                            'Nama Karyawan',
                            'NPP',
                            'Jabatan',
                            'Total Hadir',
                            'Total Terlambat',
                            'Tidak Check Out',
                            'Total Lembur (Jam)',
                            'Tingkat Kehadiran (%)',
                        ];

                        $data_rows = [];
                        $workDays = $this->getWorkDaysInPeriod($startDate, $endDate);

                        foreach ($pegawais as $pegawai) {
                            $tingkatKehadiran = $workDays > 0 ? round(($pegawai->total_hadir / $workDays) * 100, 1) : 0;
                            $totalOvertimeHours = $pegawai->total_overtime_minutes ?
                                                round($pegawai->total_overtime_minutes / 60, 1) : 0;

                            $data_rows[] = [
                                'nama_karyawan' => $pegawai->nama,
                                'npp' => $pegawai->npp ?? '-',
                                'jabatan' => $pegawai->jabatan_nama ?? '-',
                                'total_hadir' => $pegawai->total_hadir,
                                'total_terlambat' => $pegawai->total_terlambat,
                                'total_tidak_checkout' => $pegawai->total_tidak_checkout,
                                'total_overtime_hours' => $totalOvertimeHours . ' jam',
                                'tingkat_kehadiran' => $tingkatKehadiran . '%',
                            ];
                        }

                        $filename = 'rekap_absensi_' . now()->format('F_Y') . '.pdf';

                        // Summary data
                        $totalEmployees = $pegawais->count();
                        $avgAttendance = $pegawais->avg('total_hadir') ?: 0;
                        $avgAttendancePercent = $workDays > 0 ? round(($avgAttendance / $workDays) * 100, 1) : 0;

                        $pdf = Pdf::loadView('exports.attendance-report-pdf', [
                            'title' => 'Rekap Absensi ' . now()->format('F Y'),
                            'period' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
                            'headers' => $headers,
                            'data' => $data_rows,
                            'summary' => [
                                'total_employees' => $totalEmployees,
                                'work_days' => $workDays,
                                'avg_attendance' => $avgAttendancePercent,
                            ],
                        ])->setPaper('a4', 'landscape');

                        \Filament\Notifications\Notification::make()
                            ->title('Export Berhasil')
                            ->body('Rekap absensi bulan ' . now()->format('F Y') . ' berhasil digenerate.')
                            ->success()
                            ->send();

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
                }),


            // PDF export temporarily disabled due to dependency issues
            // Actions\Action::make('export_report_pdf')
            //     ->label('Export Rekap ke PDF')
            //     ->icon('heroicon-o-document-text')
            //     ->color('danger')
            //     ->action(function () {
            //         \Filament\Notifications\Notification::make()
            //             ->title('Info')
            //             ->body('Fitur export PDF sementara dinonaktifkan. Silakan gunakan export Excel.')
            //             ->info()
            //             ->send();
            //     }),
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
