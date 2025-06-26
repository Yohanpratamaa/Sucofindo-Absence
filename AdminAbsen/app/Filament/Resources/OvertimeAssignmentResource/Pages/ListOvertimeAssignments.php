<?php

namespace App\Filament\Resources\OvertimeAssignmentResource\Pages;

use App\Filament\Resources\OvertimeAssignmentResource;
use App\Exports\OvertimeAssignmentExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OvertimeAssignment;
use Carbon\Carbon;

class ListOvertimeAssignments extends ListRecords
{
    protected static string $resource = OvertimeAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export ke Excel')
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
                    Forms\Components\Select::make('status')
                        ->label('Status (Opsional)')
                        ->options([
                            'Assigned' => 'Ditugaskan',
                            'Accepted' => 'Diterima',
                            'Rejected' => 'Ditolak',
                        ])
                        ->placeholder('Semua Status'),
                    Forms\Components\Select::make('assigned_by')
                        ->label('Ditugaskan Oleh (Opsional)')
                        ->options(\App\Models\Pegawai::pluck('nama', 'id'))
                        ->searchable()
                        ->placeholder('Semua'),
                ])
                ->action(function (array $data) {
                    try {
                        $filename = 'laporan_lembur_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                        $export = new OvertimeAssignmentExport(
                            $data['start_date'],
                            $data['end_date'],
                            $data['user_id'] ?? null,
                            $data['status'] ?? null,
                            $data['assigned_by'] ?? null
                        );

                        return Excel::download($export, $filename);

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Export Error')
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->danger()
                            ->send();

                        return null;
                    }
                }),

            Actions\Action::make('export_pdf')
                ->label('Export ke PDF')
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
                    Forms\Components\Select::make('status')
                        ->label('Status (Opsional)')
                        ->options([
                            'Assigned' => 'Ditugaskan',
                            'Accepted' => 'Diterima',
                            'Rejected' => 'Ditolak',
                        ])
                        ->placeholder('Semua Status'),
                    Forms\Components\Select::make('assigned_by')
                        ->label('Ditugaskan Oleh (Opsional)')
                        ->options(\App\Models\Pegawai::pluck('nama', 'id'))
                        ->searchable()
                        ->placeholder('Semua'),
                ])
                ->action(function (array $data) {
                    try {
                        $query = OvertimeAssignment::with(['user', 'assignedBy', 'approvedBy', 'assignBy'])
                            ->whereBetween('assigned_at', [$data['start_date'], $data['end_date']]);

                        if (!empty($data['user_id'])) {
                            $query->where('user_id', $data['user_id']);
                        }

                        if (!empty($data['status'])) {
                            $query->where('status', $data['status']);
                        }

                        if (!empty($data['assigned_by'])) {
                            $query->where('assigned_by', $data['assigned_by']);
                        }

                        $overtimes = $query->orderBy('assigned_at', 'desc')->get();

                        $filename = 'laporan_lembur_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                        $pdf = Pdf::loadView('exports.overtime-pdf', [
                            'overtimes' => $overtimes,
                            'startDate' => Carbon::parse($data['start_date'])->format('d/m/Y'),
                            'endDate' => Carbon::parse($data['end_date'])->format('d/m/Y'),
                        ]);

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
}
