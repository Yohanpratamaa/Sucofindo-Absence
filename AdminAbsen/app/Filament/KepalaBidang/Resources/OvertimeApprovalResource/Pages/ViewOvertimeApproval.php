<?php

namespace App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource;
use App\Models\OvertimeAssignment;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewOvertimeApproval extends ViewRecord
{
    protected static string $resource = OvertimeApprovalResource::class;

    public function getTitle(): string
    {
        return 'Detail Lembur - ' . $this->record->user->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Pegawai')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('user.nama')
                                    ->label('Nama Pegawai'),
                                Components\TextEntry::make('user.npp')
                                    ->label('NPP'),
                                Components\TextEntry::make('user.jabatan_nama')
                                    ->label('Jabatan'),
                                Components\TextEntry::make('user.posisi_nama')
                                    ->label('Posisi'),
                            ]),
                    ]),

                Components\Section::make('Detail Penugasan Lembur')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('overtime_id')
                                    ->label('ID Lembur')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('assigned_at')
                                    ->label('Waktu Penugasan')
                                    ->dateTime(),
                                Components\TextEntry::make('assignedBy.nama')
                                    ->label('Ditugaskan Oleh'),
                                Components\TextEntry::make('created_at')
                                    ->label('Tanggal Dibuat')
                                    ->dateTime(),
                            ]),
                    ]),

                Components\Section::make('Status Penugasan')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match($state) {
                                            'Assigned' => 'warning',
                                            'Accepted' => 'success',
                                            'Rejected' => 'danger',
                                            default => 'gray'
                                        };
                                    })
                                    ->formatStateUsing(function (string $state): string {
                                        return match($state) {
                                            'Assigned' => 'Ditugaskan',
                                            'Accepted' => 'Diterima',
                                            'Rejected' => 'Ditolak',
                                            default => ucfirst($state)
                                        };
                                    }),
                                Components\TextEntry::make('approval_info')
                                    ->label('Informasi Persetujuan')
                                    ->getStateUsing(function (OvertimeAssignment $record): string {
                                        return $record->approval_info;
                                    }),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => $this->record->status === 'Assigned'),

            Actions\Action::make('approve')
                ->label('Setujui Lembur')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Lembur')
                ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')
                ->action(function (): void {
                    $this->record->accept(Auth::id());

                    Notification::make()
                        ->success()
                        ->title('Lembur Disetujui')
                        ->body("Pengajuan lembur {$this->record->user->nama} telah disetujui.")
                        ->send();
                })
                ->visible(function (): bool {
                    $currentUser = Auth::user();
                    return $this->record->status === 'Assigned' && !$currentUser->isSuperAdmin();
                }),

            Actions\Action::make('reject')
                ->label('Tolak Lembur')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Lembur')
                ->modalDescription('Apakah Anda yakin ingin menolak pengajuan lembur ini?')
                ->action(function (): void {
                    $this->record->reject(Auth::id());

                    Notification::make()
                        ->success()
                        ->title('Lembur Ditolak')
                        ->body("Pengajuan lembur {$this->record->user->nama} telah ditolak.")
                        ->send();
                })
                ->visible(function (): bool {
                    $currentUser = Auth::user();
                    return $this->record->status === 'Assigned' && !$currentUser->isSuperAdmin();
                }),

            Actions\DeleteAction::make()
                ->visible(fn (): bool => $this->record->status === 'Assigned'),
        ];
    }
}
