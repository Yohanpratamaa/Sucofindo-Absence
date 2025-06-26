<?php

namespace App\Filament\Resources\IzinResource\Pages;

use App\Filament\Resources\IzinResource;
use App\Exports\IzinExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Izin;
use Carbon\Carbon;

class ListIzins extends ListRecords
{
    protected static string $resource = IzinResource::class;

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
                    Forms\Components\Select::make('jenis_izin')
                        ->label('Jenis Izin (Opsional)')
                        ->options([
                            'sakit' => 'Sakit',
                            'cuti' => 'Cuti',
                            'izin' => 'Izin Khusus',
                        ])
                        ->placeholder('Semua Jenis'),
                    Forms\Components\Select::make('status')
                        ->label('Status (Opsional)')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->placeholder('Semua Status'),
                ])
                ->action(function (array $data) {
                    try {
                        $filename = 'laporan_izin_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                        $export = new IzinExport(
                            $data['start_date'],
                            $data['end_date'],
                            $data['user_id'] ?? null,
                            $data['jenis_izin'] ?? null,
                            $data['status'] ?? null
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
                    Forms\Components\Select::make('jenis_izin')
                        ->label('Jenis Izin (Opsional)')
                        ->options([
                            'sakit' => 'Sakit',
                            'cuti' => 'Cuti',
                            'izin' => 'Izin Khusus',
                        ])
                        ->placeholder('Semua Jenis'),
                    Forms\Components\Select::make('status')
                        ->label('Status (Opsional)')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->placeholder('Semua Status'),
                ])
                ->action(function (array $data) {
                    try {
                        $query = Izin::with(['user', 'approvedBy'])
                            ->whereBetween('tanggal_mulai', [$data['start_date'], $data['end_date']]);

                        if (!empty($data['user_id'])) {
                            $query->where('user_id', $data['user_id']);
                        }

                        if (!empty($data['jenis_izin'])) {
                            $query->where('jenis_izin', $data['jenis_izin']);
                        }

                        if (!empty($data['status'])) {
                            switch ($data['status']) {
                                case 'pending':
                                    $query->pending();
                                    break;
                                case 'approved':
                                    $query->approved();
                                    break;
                                case 'rejected':
                                    $query->rejected();
                                    break;
                            }
                        }

                        $izins = $query->orderBy('tanggal_mulai', 'desc')->get();

                        $filename = 'laporan_izin_' .
                                   Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' .
                                   Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                        $pdf = Pdf::loadView('exports.izin-pdf', [
                            'izins' => $izins,
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
