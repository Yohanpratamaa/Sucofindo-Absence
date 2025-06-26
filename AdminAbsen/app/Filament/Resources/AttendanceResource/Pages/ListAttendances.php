<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Exports\AttendanceExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
// use Barryvdh\DomPDF\Facade\Pdf; // Temporarily disabled
use App\Models\Attendance;
use Carbon\Carbon;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_all_excel')
                ->label('Export Semua ke Excel')
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
                    Forms\Components\Select::make('user_id')
                        ->label('Karyawan (Opsional)')
                        ->options(\App\Models\Pegawai::pluck('nama', 'id'))
                        ->searchable()
                        ->placeholder('Semua Karyawan'),
                ])
                ->action(function (array $data) {
                    try {
                        $filename = 'laporan_absensi_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                        $export = new AttendanceExport($data['start_date'], $data['end_date'], $data['user_id'] ?? null);

                        // Use temporary file approach
                        $tempFile = tempnam(sys_get_temp_dir(), 'attendance_export_');
                        $content = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
                        file_put_contents($tempFile, $content);

                        return response()->download($tempFile, $filename, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])->deleteFileAfterSend(true);

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
            // Actions\Action::make('export_all_pdf')
            //     ->label('Export Semua ke PDF')
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

    private function getDurationWork($attendance)
    {
        if (!$attendance->check_in || !$attendance->check_out) {
            return '-';
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($attendance->check_out);

        // Hitung total durasi dalam menit (pastikan urutan parameter benar)
        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        // Jika ada absen siang, kurangi 1 jam untuk istirahat
        if ($attendance->absen_siang) {
            $totalMinutes = max(0, $totalMinutes - 60);
        }

        if ($totalMinutes <= 0) {
            return '0 jam 0 menit';
        }

        $hours = intval($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }

    private function getAttendanceStatus($attendance)
    {
        if (!$attendance->check_in) {
            return 'Tidak Hadir';
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $jamMasukStandar = Carbon::parse('08:00');

        if ($checkIn->greaterThan($jamMasukStandar)) {
            return 'Terlambat';
        }

        return 'Tepat Waktu';
    }
}
