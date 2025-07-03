<?php

namespace App\Filament\KepalaBidang\Resources\AttendanceResource\Pages;

use App\Filament\KepalaBidang\Resources\AttendanceResource;
use App\Exports\KepalaBidangAttendanceExport;
use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Absensi')
                ->icon('heroicon-o-plus'),

            // Export Actions Group
            Actions\ActionGroup::make([
                // Export to Excel
                Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->default(now()->startOfMonth())
                                    ->required(),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Tanggal Akhir')
                                    ->default(now()->endOfMonth())
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('employee_id')
                            ->label('Pilih Karyawan (Opsional)')
                            ->placeholder('Semua Karyawan')
                            ->options(function () {
                                return Pegawai::where('role_user', 'employee')
                                    ->where('status', 'active')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date'])->startOfDay();
                        $endDate = Carbon::parse($data['end_date'])->endOfDay();
                        $employeeId = $data['employee_id'] ?? null;

                        $filename = 'laporan-absensi-' . $startDate->format('Y-m-d') . '-sampai-' . $endDate->format('Y-m-d');

                        if ($employeeId) {
                            $employee = Pegawai::find($employeeId);
                            $filename .= '-' . str_replace(' ', '-', $employee->nama ?? 'unknown');
                        }

                        $filename .= '.xlsx';

                        return Excel::download(
                            new KepalaBidangAttendanceExport($startDate, $endDate, $employeeId),
                            $filename
                        );
                    }),

                // Export to PDF
                Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->default(now()->startOfMonth())
                                    ->required(),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Tanggal Akhir')
                                    ->default(now()->endOfMonth())
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('employee_id')
                            ->label('Pilih Karyawan (Opsional)')
                            ->placeholder('Semua Karyawan')
                            ->options(function () {
                                return Pegawai::where('role_user', 'employee')
                                    ->where('status', 'active')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        $startDate = Carbon::parse($data['start_date'])->startOfDay();
                        $endDate = Carbon::parse($data['end_date'])->endOfDay();
                        $employeeId = $data['employee_id'] ?? null;

                        // Query data attendance - hanya employee yang aktif
                        $teamMembers = Pegawai::where('role_user', 'employee')
                            ->where('status', 'active')
                            ->pluck('id');

                        $query = Attendance::with(['user'])
                            ->whereIn('user_id', $teamMembers)
                            ->whereBetween('created_at', [$startDate, $endDate]);

                        if ($employeeId) {
                            $query->where('user_id', $employeeId);
                        }                        $attendances = $query->orderBy('created_at', 'desc')->get();

                        // Prepare headers for PDF table
                        $headers = [
                            'Tanggal',
                            'Nama Pegawai',
                            'NPP',
                            'Tipe Absensi',
                            'Check In',
                            'Absen Siang',
                            'Check Out',
                            'Durasi Kerja',
                            'Status',
                            'Lembur'
                        ];

                        // Transform attendance data for PDF table
                        $data = $attendances->map(function ($attendance) {
                            return [
                                'tanggal' => $attendance->created_at->format('d M Y'),
                                'nama_pegawai' => $attendance->user->nama ?? '-',
                                'npp' => $attendance->user->npp ?? '-',
                                'tipe_absensi' => $attendance->attendance_type ?? '-',
                                'check_in' => $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '-',
                                'absen_siang' => $attendance->absen_siang ? Carbon::parse($attendance->absen_siang)->format('H:i') : '-',
                                'check_out' => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-',
                                'durasi_kerja' => $attendance->durasi_kerja ?? '-',
                                'status' => $attendance->status_kehadiran ?? '-',
                                'lembur' => $attendance->overtime ? $attendance->overtime . ' menit' : '-'
                            ];
                        })->toArray();

                        // Generate title
                        $title = 'Laporan Absensi Pegawai';
                        $period = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

                        if ($employeeId) {
                            $employee = Pegawai::find($employeeId);
                            $employeeName = $employee->nama ?? 'Unknown';
                        } else {
                            $employeeName = 'Semua Karyawan';
                        }

                        // Generate statistics for summary
                        $summary = [
                            'total_employees' => $employeeId ? 1 : $teamMembers->count(),
                            'work_days' => $startDate->diffInDays($endDate) + 1,
                            'avg_attendance' => $attendances->count() > 0 ?
                                round(($attendances->whereNotNull('check_in')->count() / $attendances->count()) * 100, 1) : 0
                        ];

                        // Generate statistics
                        $stats = [
                            'total_records' => $attendances->count(),
                            'present_count' => $attendances->whereNotNull('check_in')->count(),
                            'late_count' => $attendances->filter(function ($attendance) {
                                if (!$attendance->check_in) return false;
                                $checkIn = Carbon::parse($attendance->check_in);
                                $jamMasukStandar = Carbon::parse('08:00');
                                return $checkIn->greaterThan($jamMasukStandar);
                            })->count(),
                            'absent_count' => $attendances->whereNull('check_in')->count(),
                            'wfo_count' => $attendances->where('attendance_type', 'WFO')->count(),
                            'dinas_luar_count' => $attendances->where('attendance_type', 'Dinas Luar')->count(),
                        ];

                        $pdf = Pdf::loadView('exports.attendance-report-pdf', [
                            'attendances' => $attendances,
                            'headers' => $headers,
                            'data' => $data,
                            'title' => $title,
                            'period' => $period,
                            'employee_name' => $employeeName,
                            'summary' => $summary,
                            'stats' => $stats,
                            'generated_at' => now()->format('d M Y H:i:s'),
                        ]);

                        $filename = 'laporan-absensi-' . $startDate->format('Y-m-d') . '-sampai-' . $endDate->format('d M Y');

                        if ($employeeId) {
                            $employee = Pegawai::find($employeeId);
                            $filename .= '-' . str_replace(' ', '-', $employee->nama ?? 'unknown');
                        }

                        $filename .= '.pdf';

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $filename, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            ->label('Export Laporan')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('info')
            ->button(),
        ];
    }

    public function getTitle(): string
    {
        return 'Data Absensi Pegawai';
    }
}
