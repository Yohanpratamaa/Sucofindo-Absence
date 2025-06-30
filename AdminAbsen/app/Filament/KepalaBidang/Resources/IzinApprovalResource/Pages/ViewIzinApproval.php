<?php

namespace App\Filament\KepalaBidang\Resources\IzinApprovalResource\Pages;

use App\Filament\KepalaBidang\Resources\IzinApprovalResource;
use App\Models\Izin;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class ViewIzinApproval extends ViewRecord
{
    protected static string $resource = IzinApprovalResource::class;

    public function getTitle(): string
    {
        return 'Detail Persetujuan Izin';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pegawai')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.nama')
                                    ->label('Nama Pegawai'),
                                Infolists\Components\TextEntry::make('user.npp')
                                    ->label('NPP'),
                                Infolists\Components\TextEntry::make('user.jabatan_nama')
                                    ->label('Jabatan'),
                                Infolists\Components\TextEntry::make('user.posisi_nama')
                                    ->label('Posisi'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Detail Izin')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis_izin')
                                    ->label('Jenis Izin')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'sakit' => 'danger',
                                        'cuti' => 'success',
                                        'izin' => 'warning',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('tanggal_akhir')
                                    ->label('Tanggal Akhir')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('durasi_hari')
                                    ->label('Durasi')
                                    ->getStateUsing(function (Izin $record): string {
                                        $start = \Carbon\Carbon::parse($record->tanggal_mulai);
                                        $end = \Carbon\Carbon::parse($record->tanggal_akhir);
                                        $durasi = $start->diffInDays($end) + 1;
                                        return $durasi . ' hari';
                                    })
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'pending' => 'Menunggu',
                                        default => ucfirst($state),
                                    }),
                            ]),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan/Alasan')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Infolists\Components\Section::make('Dokumen Pendukung')
                    ->schema([
                        Infolists\Components\TextEntry::make('dokumen_pendukung')
                            ->label('File Dokumen')
                            ->formatStateUsing(function (?string $state, Izin $record): string {
                                if (!$state) return 'Tidak ada dokumen pendukung';

                                $fileName = basename($state);
                                $fileExtension = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isPdf = $fileExtension === 'pdf';

                                $previewUrl = route('izin.document.preview', $record);
                                $downloadUrl = route('izin.document.download', $record);

                                $html = '<div class="dokumen-preview space-y-4">';

                                // File info
                                $html .= '<div class="file-info bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">';
                                $html .= '<div class="flex items-center gap-2 mb-2">';
                                $html .= '<span class="font-medium text-gray-900 dark:text-gray-100">' . $fileName . '</span>';
                                if ($isImage) {
                                    $html .= '<span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded">Gambar</span>';
                                } elseif ($isPdf) {
                                    $html .= '<span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs px-2 py-1 rounded">PDF</span>';
                                } else {
                                    $html .= '<span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs px-2 py-1 rounded">Dokumen</span>';
                                }
                                $html .= '</div>';

                                // Action buttons
                                $html .= '<div class="flex flex-wrap gap-2">';
                                $html .= '<a href="' . $previewUrl . '" target="_blank" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 text-sm font-medium transition-colors">';
                                $html .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                                $html .= '</svg>';
                                $html .= 'Preview</a>';

                                $html .= '<a href="' . $downloadUrl . '" class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 text-sm font-medium transition-colors">';
                                $html .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>';
                                $html .= '</svg>';
                                $html .= 'Download</a>';
                                $html .= '</div>';
                                $html .= '</div>';

                                // Inline Preview
                                if ($isImage) {
                                    $html .= '<div class="preview-container">';
                                    $html .= '<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">';
                                    $html .= '<h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Preview Gambar:</h4>';
                                    $html .= '<div class="max-w-md mx-auto">';
                                    $html .= '<img src="' . $previewUrl . '" alt="' . $fileName . '" class="w-full h-auto rounded border border-gray-200 dark:border-gray-600 shadow-sm cursor-pointer hover:shadow-md transition-shadow" onclick="window.open(\'' . $previewUrl . '\', \'_blank\')" style="max-height: 300px; object-fit: contain;" loading="lazy">';
                                    $html .= '</div>';
                                    $html .= '<p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">Klik gambar untuk melihat ukuran penuh</p>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                } elseif ($isPdf) {
                                    $html .= '<div class="preview-container">';
                                    $html .= '<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">';
                                    $html .= '<h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Preview PDF:</h4>';
                                    $html .= '<div class="border border-gray-200 dark:border-gray-600 rounded overflow-hidden bg-gray-100 dark:bg-gray-700" style="height: 400px;">';
                                    $html .= '<iframe src="' . $previewUrl . '#toolbar=0&navpanes=0&scrollbar=1&view=FitH" width="100%" height="100%" frameborder="0" class="pdf-preview">';
                                    $html .= '<div class="p-4 text-center">';
                                    $html .= '<p class="text-gray-600 dark:text-gray-300 mb-2">Browser Anda tidak mendukung preview PDF.</p>';
                                    $html .= '<a href="' . $previewUrl . '" target="_blank" class="text-blue-600 dark:text-blue-400 underline hover:text-blue-500 dark:hover:text-blue-300 transition-colors">Klik di sini untuk membuka dokumen</a>';
                                    $html .= '</div>';
                                    $html .= '</iframe>';
                                    $html .= '</div>';
                                    $html .= '<p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">Jika preview tidak muncul, klik "Preview" di atas</p>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                }

                                $html .= '</div>';

                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Izin $record): bool => !empty($record->dokumen_pendukung)),

                Infolists\Components\Section::make('Informasi Persetujuan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('approvedBy.nama')
                                    ->label('Diproses Oleh')
                                    ->placeholder('Belum diproses'),

                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Tanggal Diproses')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('Belum diproses'),

                                Infolists\Components\TextEntry::make('approval_info')
                                    ->label('Keterangan Persetujuan')
                                    ->columnSpanFull()
                                    ->getStateUsing(function (Izin $record): string {
                                        return $record->approval_info;
                                    }),
                            ]),
                    ])
                    ->visible(fn (Izin $record) => $record->approved_by !== null),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui Izin')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Izin')
                ->modalDescription(function (): string {
                    $currentUser = Auth::user();
                    return "Apakah Anda yakin ingin menyetujui izin ini?\n\nIzin akan tercatat disetujui oleh: {$currentUser->nama}";
                })
                ->action(function (): void {
                    $this->record->approve(Auth::id());

                    Notification::make()
                        ->success()
                        ->title('Izin Disetujui')
                        ->body("Izin {$this->record->user->nama} telah berhasil disetujui.")
                        ->send();
                })
                ->visible(function (): bool {
                    $currentUser = Auth::user();
                    return is_null($this->record->approved_by) && $currentUser->role_user !== 'super admin';
                }),

            Actions\Action::make('reject')
                ->label('Tolak Izin')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tolak Izin')
                ->modalDescription(function (): string {
                    $currentUser = Auth::user();
                    return "Apakah Anda yakin ingin menolak izin ini?\n\nIzin akan tercatat ditolak oleh: {$currentUser->nama}";
                })
                ->action(function (): void {
                    $this->record->reject(Auth::id());

                    Notification::make()
                        ->success()
                        ->title('Izin Ditolak')
                        ->body("Izin {$this->record->user->nama} telah ditolak.")
                        ->send();
                })
                ->visible(function (): bool {
                    $currentUser = Auth::user();
                    return is_null($this->record->approved_by) && $currentUser->role_user !== 'super admin';
                }),

            Actions\Action::make('print_document')
                ->label('Cetak Dokumen')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('izin.print', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => !is_null($this->record->approved_by)),
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
