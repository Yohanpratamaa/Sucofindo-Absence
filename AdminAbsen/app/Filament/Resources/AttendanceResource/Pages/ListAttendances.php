<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Exports\AttendanceExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
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

            // Export PDF untuk Detail Absensi
            Actions\Action::make('export_detail_pdf')
                ->label('Export Detail ke PDF')
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
                    Forms\Components\Select::make('user_id')
                        ->label('Karyawan (Opsional)')
                        ->options(\App\Models\Pegawai::pluck('nama', 'id'))
                        ->searchable()
                        ->placeholder('Semua Karyawan'),
                    Forms\Components\Select::make('attendance_type')
                        ->label('Tipe Absensi (Opsional)')
                        ->options([
                            'WFO' => 'Work From Office',
                            'Dinas Luar' => 'Dinas Luar',
                        ])
                        ->placeholder('Semua Tipe'),
                ])
                ->action(function (array $data) {
                    try {
                        $query = Attendance::with(['user'])
                            ->whereBetween('created_at', [$data['start_date'], $data['end_date']]);

                        if (!empty($data['user_id'])) {
                            $query->where('user_id', $data['user_id']);
                        }

                        if (!empty($data['attendance_type'])) {
                            $query->where('attendance_type', $data['attendance_type']);
                        }

                        $attendances = $query->orderBy('created_at', 'desc')->get();

                        // Prepare data for PDF template
                        $headers = [
                            'Tanggal',
                            'Nama Karyawan', 
                            'NPP',
                            'Jabatan',
                            'Check In',
                            'Check Out',
                            'Durasi Kerja',
                            'Status Kehadiran',
                            'Tipe Absensi',
                        ];

                        $data_rows = [];
                        foreach ($attendances as $attendance) {
                            $data_rows[] = [
                                'tanggal' => $attendance->created_at->format('d M Y'),
                                'nama_karyawan' => $attendance->user->nama ?? '-',
                                'npp' => $attendance->user->npp ?? '-',
                                'jabatan' => $attendance->user->jabatan_nama ?? '-',
                                'check_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : '-',
                                'check_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : '-',
                                'durasi_kerja' => $this->getDurationWork($attendance),
                                'status_kehadiran' => $this->getAttendanceStatus($attendance),
                                'attendance_type' => $attendance->attendance_type ?? '-',
                            ];
                        }

                        $filename = 'detail_absensi_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                        $workDays = $this->getWorkDaysInPeriod($data['start_date'], $data['end_date']);
                        $totalRecords = $attendances->count();
                        $employeeName = null;
                        if (!empty($data['user_id'])) {
                            $employee = \App\Models\Pegawai::find($data['user_id']);
                            $employeeName = $employee->nama ?? null;
                        }

                        $pdf = Pdf::loadView('exports.attendance-pdf', [
                            'title' => 'Detail Data Absensi',
                            'period' => Carbon::parse($data['start_date'])->format('d/m/Y') . ' - ' . 
                                       Carbon::parse($data['end_date'])->format('d/m/Y'),
                            'headers' => $headers,
                            'data' => $data_rows,
                            'summary' => [
                                'total_records' => $totalRecords,
                                'work_days' => $workDays,
                                'employee_name' => $employeeName,
                                'total_attendance' => $totalRecords,
                            ],
                        ])->setPaper('a4', 'landscape');

                        \Filament\Notifications\Notification::make()
                            ->title('Export Berhasil')
                            ->body('Detail absensi berhasil digenerate ke PDF.')
                            ->success()
                            ->send();

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename);

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export PDF: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
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
