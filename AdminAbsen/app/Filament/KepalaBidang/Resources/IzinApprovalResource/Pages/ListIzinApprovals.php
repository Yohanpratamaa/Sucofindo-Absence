<?php

namespace App\Filament\KepalaBidang\Resources\IzinApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\IzinApprovalResource;
use App\Exports\IzinApprovalExport;
use App\Models\Izin;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ListIzinApprovals extends ListRecords
{
    protected static string $resource = IzinApprovalResource::class;

    public function getTitle(): string
    {
        return 'Persetujuan Izin Tim';
    }

    protected function getHeaderActions(): array
    {
        return [
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

                                    Forms\Components\Select::make('jenis_izin')
                                        ->label('Jenis Izin')
                                        ->options([
                                            'sakit' => 'Sakit',
                                            'cuti' => 'Cuti',
                                            'izin' => 'Izin',
                                        ])
                                        ->placeholder('Pilih jenis izin atau kosongkan untuk semua'),
                                ]),

                            Forms\Components\Select::make('status')
                                ->label('Status Persetujuan')
                                ->options([
                                    'pending' => 'Menunggu Persetujuan',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                ])
                                ->placeholder('Pilih status atau kosongkan untuk semua'),
                        ]),
                ])
                ->action(function (array $data): \Symfony\Component\HttpFoundation\BinaryFileResponse {
                    $filename = 'Persetujuan_Izin_Tim_' . Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . Carbon::parse($data['end_date'])->format('Y-m-d') . '.xlsx';

                    return Excel::download(
                        new IzinApprovalExport(
                            $data['start_date'],
                            $data['end_date'],
                            $data['user_id'] ?? null,
                            $data['jenis_izin'] ?? null,
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

                                    Forms\Components\Select::make('jenis_izin')
                                        ->label('Jenis Izin')
                                        ->options([
                                            'sakit' => 'Sakit',
                                            'cuti' => 'Cuti',
                                            'izin' => 'Izin',
                                        ])
                                        ->placeholder('Pilih jenis izin atau kosongkan untuk semua'),
                                ]),

                            Forms\Components\Select::make('status')
                                ->label('Status Persetujuan')
                                ->options([
                                    'pending' => 'Menunggu Persetujuan',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                ])
                                ->placeholder('Pilih status atau kosongkan untuk semua'),
                        ]),
                ])
                ->action(function (array $data) {
                    // Query data dengan filter yang sama
                    $teamMembers = Pegawai::where('role_user', 'employee')
                        ->where('status', 'active')
                        ->pluck('id');

                    $query = Izin::with(['user', 'approvedBy'])
                        ->whereIn('user_id', $teamMembers)
                        ->whereBetween('created_at', [$data['start_date'], $data['end_date']]);

                    if (isset($data['user_id'])) {
                        $query->where('user_id', $data['user_id']);
                    }

                    if (isset($data['jenis_izin'])) {
                        $query->where('jenis_izin', $data['jenis_izin']);
                    }

                    if (isset($data['status'])) {
                        switch ($data['status']) {
                            case 'pending':
                                $query->whereNull('approved_by');
                                break;
                            case 'approved':
                                $query->whereNotNull('approved_by')->whereNotNull('approved_at');
                                break;
                            case 'rejected':
                                $query->whereNotNull('approved_by')->whereNull('approved_at');
                                break;
                        }
                    }

                    $izinData = $query->orderBy('created_at', 'desc')->get();

                    // Hitung statistik
                    $total_izin = $izinData->count();
                    $pending = $izinData->where('approved_by', null)->count();
                    $approved = $izinData->where('approved_by', '!=', null)->where('approved_at', '!=', null)->count();
                    $rejected = $izinData->where('approved_by', '!=', null)->where('approved_at', null)->count();

                    $period = Carbon::parse($data['start_date'])->format('d M Y') . ' - ' . Carbon::parse($data['end_date'])->format('d M Y');
                    $filename = 'Persetujuan_Izin_Tim_' . Carbon::parse($data['start_date'])->format('Y-m-d') . '_to_' . Carbon::parse($data['end_date'])->format('Y-m-d') . '.pdf';

                    $pdf = Pdf::loadView('exports.izin-approval-pdf', [
                        'data' => $izinData,
                        'period' => $period,
                        'total_izin' => $total_izin,
                        'pending' => $pending,
                        'approved' => $approved,
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
}
