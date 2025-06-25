<?php

namespace App\Filament\Resources\AttendanceReportResource\Pages;

use App\Filament\Resources\AttendanceReportResource;
use App\Exports\AttendanceReportExport;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\Facade\Pdf; // Temporarily disabled
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
