<?php

namespace App\Filament\KepalaBidang\Resources\AttendanceReportResource\Pages;

use App\Filament\KepalaBidang\Resources\AttendanceReportResource;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceReportExport;
use App\Models\Pegawai;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ListAttendanceReports extends ListRecords
{
    protected static string $resource = AttendanceReportResource::class;

    public function getTitle(): string
    {
        return 'Laporan Absensi Tim';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Dropdown Export Rekap Tim
            Actions\ActionGroup::make([
                // Export Excel Tim
                Actions\Action::make('export_team_excel')
                    ->label('Export Excel')
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
                            $filename = 'rekap_absensi_tim_' .
                                       Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                       Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                            $export = new AttendanceReportExport($data['start_date'], $data['end_date']);

                            return Excel::download($export, $filename);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Error')
                                ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    }),

                // Export PDF Tim
                Actions\Action::make('export_team_pdf')
                    ->label('Export PDF')
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
                        try {
                            $startDate = Carbon::parse($data['start_date']);
                            $endDate = Carbon::parse($data['end_date']);

                            $attendanceData = $this->getAttendanceSummaryData($startDate, $endDate);

                            $filename = 'rekap_absensi_tim_' .
                                       $startDate->format('Y-m-d') . '_to_' .
                                       $endDate->format('Y-m-d') . '.pdf';

                            $pdf = Pdf::loadView('exports.attendance-summary-pdf', [
                                'attendanceData' => $attendanceData,
                                'startDate' => $startDate->format('d/m/Y'),
                                'endDate' => $endDate->format('d/m/Y'),
                                'totalEmployees' => $attendanceData->count(),
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Error')
                                ->body('Terjadi kesalahan saat export PDF: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    }),
            ])
            ->label('📊 Export Rekap Tim')
            ->icon('heroicon-o-users')
            ->color('primary')
            ->button(),

            // Dropdown Export Detail Karyawan
            Actions\ActionGroup::make([
                // Export Detail Excel
                Actions\Action::make('export_detail_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth())
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth())
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Pilih Karyawan')
                            ->options(function () {
                                return Pegawai::where('role_user', 'employee')
                                    ->where('status', 'active')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $employee = Pegawai::find($data['employee_id']);
                            $filename = 'detail_absensi_' .
                                       str_replace(' ', '_', $employee->nama) . '_' .
                                       Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                       Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                            $export = new AttendanceExport($data['start_date'], $data['end_date'], $data['employee_id']);

                            return Excel::download($export, $filename);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Error')
                                ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    }),

                // Export Detail PDF
                Actions\Action::make('export_detail_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth())
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth())
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Pilih Karyawan')
                            ->options(function () {
                                return Pegawai::where('role_user', 'employee')
                                    ->where('status', 'active')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $startDate = Carbon::parse($data['start_date']);
                            $endDate = Carbon::parse($data['end_date']);
                            $employee = Pegawai::find($data['employee_id']);

                            $attendances = Attendance::where('user_id', $data['employee_id'])
                                ->whereBetween('created_at', [$startDate, $endDate])
                                ->orderBy('created_at', 'desc')
                                ->get();

                            $filename = 'detail_absensi_' .
                                       str_replace(' ', '_', $employee->nama) . '_' .
                                       $startDate->format('Y-m-d') . '_to_' .
                                       $endDate->format('Y-m-d') . '.pdf';

                            $pdf = Pdf::loadView('exports.attendance-detail-pdf', [
                                'attendances' => $attendances,
                                'employee' => $employee,
                                'startDate' => $startDate->format('d/m/Y'),
                                'endDate' => $endDate->format('d/m/Y'),
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $filename);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Error')
                                ->body('Terjadi kesalahan saat export PDF: ' . $e->getMessage())
                                ->danger()
                                ->send();

                            return null;
                        }
                    }),
            ])
            ->label('👤 Export Detail Karyawan')
            ->icon('heroicon-o-user')
            ->color('info')
            ->button(),

            // Quick Export Bulan Ini
            Actions\Action::make('export_bulan_ini')
                ->label('⚡ Quick Export Bulan Ini')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Export Rekap Bulan Ini')
                ->modalDescription('Export rekap absensi semua karyawan untuk bulan ini dalam format Excel.')
                ->modalSubmitActionLabel('Export Sekarang')
                ->action(function () {
                    try {
                        $startDate = now()->startOfMonth();
                        $endDate = now()->endOfMonth();

                        $filename = 'rekap_absensi_' . now()->format('F_Y') . '.xlsx';

                        $export = new AttendanceReportExport($startDate, $endDate);

                        Notification::make()
                            ->title('Export Berhasil')
                            ->body('Rekap absensi bulan ' . now()->format('F Y') . ' berhasil diexport.')
                            ->success()
                            ->send();

                        return Excel::download($export, $filename);

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget statistik bisa ditambahkan di sini
        ];
    }

    private function getAttendanceSummaryData($startDate, $endDate)
    {
        return Pegawai::select([
                'pegawais.*',
                \DB::raw('COUNT(attendances.id) as total_hadir'),
                \DB::raw('COUNT(CASE WHEN TIME(attendances.check_in) > "08:00:00" THEN 1 END) as total_terlambat'),
                \DB::raw('COUNT(CASE WHEN attendances.check_out IS NULL THEN 1 END) as total_tidak_checkout'),
                \DB::raw('SUM(attendances.overtime) as total_overtime_minutes'),
                \DB::raw('AVG(CASE WHEN attendances.check_in IS NOT NULL AND attendances.check_out IS NOT NULL
                               THEN TIMESTAMPDIFF(MINUTE, attendances.check_in, attendances.check_out) - 60
                               ELSE NULL END) as avg_work_minutes'),
            ])
            ->leftJoin('attendances', function ($join) use ($startDate, $endDate) {
                $join->on('pegawais.id', '=', 'attendances.user_id')
                     ->whereBetween('attendances.created_at', [$startDate, $endDate]);
            })
            ->where('pegawais.role_user', 'employee')
            ->where('pegawais.status', 'active')
            ->groupBy('pegawais.id')
            ->orderBy('total_hadir', 'desc')
            ->get();
    }
}
