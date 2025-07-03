<?php

namespace App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource;
use App\Exports\OvertimeApprovalExport;
use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ListOvertimeApprovals extends ListRecords
{
    protected static string $resource = OvertimeApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Assign Lembur Baru')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->tooltip('Assign lembur kepada pegawai (langsung disetujui)')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['assigned_by'] = auth()->id();

                    // Auto-generate overtime ID dengan format: OT-YYYYMMDD-XXXX
                    $date = now()->format('Ymd');
                    $lastRecord = OvertimeAssignment::whereDate('created_at', now())
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequence = $lastRecord ? (int)substr($lastRecord->overtime_id, -4) + 1 : 1;
                    $data['overtime_id'] = 'OT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                    $data['status'] = 'Accepted'; // Langsung disetujui
                    $data['approved_by'] = auth()->id(); // Kepala bidang yang assign sekaligus approve
                    $data['approved_at'] = now(); // Set waktu approval saat ini
                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Lembur Berhasil Di-assign dan Disetujui')
                        ->body('Penugasan lembur telah berhasil dibuat dan langsung disetujui.')
                ),

            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->tooltip('Export data ke file Excel dengan filter')
                ->form([
                    Forms\Components\Section::make('Filter Export Data')
                        ->description('Pilih filter untuk mengekspor data sesuai kebutuhan')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label('Tanggal Mulai')
                                        ->default(now()->startOfMonth())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    Forms\Components\DatePicker::make('end_date')
                                        ->label('Tanggal Akhir')
                                        ->default(now()->endOfMonth())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Pegawai')
                                        ->options(function () {
                                            return Pegawai::where('role_user', 'employee')
                                                ->where('status', 'active')
                                                ->pluck('nama', 'id');
                                        })
                                        ->searchable()
                                        ->placeholder('Pilih pegawai atau kosongkan untuk semua'),

                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Assigned' => 'Ditugaskan',
                                            'Accepted' => 'Diterima',
                                            'Rejected' => 'Ditolak',
                                        ])
                                        ->placeholder('Pilih status atau kosongkan untuk semua'),
                                ]),
                        ]),
                ])
                ->action(function (array $data): \Symfony\Component\HttpFoundation\BinaryFileResponse {
                    $filename = 'Pengajuan_Lembur_' . Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                    return Excel::download(
                        new OvertimeApprovalExport(
                            $data['start_date'],
                            $data['end_date'],
                            $data['user_id'] ?? null,
                            $data['status'] ?? null
                        ),
                        $filename
                    );
                }),

            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->tooltip('Export data ke file PDF dengan filter')
                ->form([
                    Forms\Components\Section::make('Filter Export Data')
                        ->description('Pilih filter untuk mengekspor data sesuai kebutuhan')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label('Tanggal Mulai')
                                        ->default(now()->startOfMonth())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),

                                    Forms\Components\DatePicker::make('end_date')
                                        ->label('Tanggal Akhir')
                                        ->default(now()->endOfMonth())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y'),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('user_id')
                                        ->label('Pegawai')
                                        ->options(function () {
                                            return Pegawai::where('role_user', 'employee')
                                                ->where('status', 'active')
                                                ->pluck('nama', 'id');
                                        })
                                        ->searchable()
                                        ->placeholder('Pilih pegawai atau kosongkan untuk semua'),

                                    Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Assigned' => 'Ditugaskan',
                                            'Accepted' => 'Diterima',
                                            'Rejected' => 'Ditolak',
                                        ])
                                        ->placeholder('Pilih status atau kosongkan untuk semua'),
                                ]),
                        ]),
                ])
                ->action(function (array $data) {
                    // Query data dengan filter yang sama
                    $teamMembers = Pegawai::where('role_user', 'employee')
                        ->where('status', 'active')
                        ->pluck('id');

                    $query = OvertimeAssignment::with(['user', 'assignedBy', 'approvedBy'])
                        ->whereIn('user_id', $teamMembers)
                        ->whereBetween('assigned_at', [$data['start_date'], $data['end_date']]);

                    if (isset($data['user_id'])) {
                        $query->where('user_id', $data['user_id']);
                    }

                    if (isset($data['status'])) {
                        $query->where('status', $data['status']);
                    }

                    $overtimeData = $query->orderBy('assigned_at', 'desc')->get();

                    // Hitung statistik
                    $total_overtime = $overtimeData->count();
                    $assigned = $overtimeData->where('status', 'Assigned')->count();
                    $accepted = $overtimeData->where('status', 'Accepted')->count();
                    $rejected = $overtimeData->where('status', 'Rejected')->count();

                    $period = Carbon::parse($data['start_date'])->format('d M Y') . ' - ' . Carbon::parse($data['end_date'])->format('d M Y');
                    $filename = 'Pengajuan_Lembur_' . Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                    $pdf = Pdf::loadView('exports.overtime-approval-pdf', [
                        'data' => $overtimeData,
                        'period' => $period,
                        'total_overtime' => $total_overtime,
                        'assigned' => $assigned,
                        'accepted' => $accepted,
                        'rejected' => $rejected,
                        'generated_at' => now()->format('d M Y H:i:s'),
                    ])
                    ->setPaper('a4', 'landscape')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                        'defaultFont' => 'DejaVu Sans'
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $filename
                    );
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Pengajuan Lembur';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget statistik lembur di sini
        ];
    }
}
