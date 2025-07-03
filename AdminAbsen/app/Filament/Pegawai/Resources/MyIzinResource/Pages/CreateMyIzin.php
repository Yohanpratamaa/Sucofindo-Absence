<?php

namespace App\Filament\Pegawai\Resources\MyIzinResource\Pages;

use App\Filament\Pegawai\Resources\MyIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CreateMyIzin extends CreateRecord
{
    protected static string $resource = MyIzinResource::class;

    public function getTitle(): string
    {
        return 'Ajukan Izin Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengajuan Izin Berhasil')
            ->body('Pengajuan izin telah dikirim dan menunggu persetujuan dari atasan.')
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan user_id diisi dengan ID user yang sedang login
        $data['user_id'] = Auth::id();

        // Handle file upload with better error handling
        if (isset($data['dokumen_pendukung']) && $data['dokumen_pendukung']) {
            try {
                // Validate file exists in temporary storage
                $tempPath = $data['dokumen_pendukung'];
                if (is_string($tempPath) && Storage::disk('public')->exists($tempPath)) {
                    // File is already processed by Filament, keep the path
                    Log::info('File uploaded successfully', ['path' => $tempPath]);
                } else {
                    // Handle case where file might not be properly uploaded
                    Log::warning('File upload issue detected', ['data' => $data['dokumen_pendukung']]);
                }
            } catch (\Exception $e) {
                Log::error('File upload error in CreateMyIzin', [
                    'error' => $e->getMessage(),
                    'file_data' => $data['dokumen_pendukung'] ?? 'null'
                ]);

                // Remove problematic file data to prevent creation failure
                unset($data['dokumen_pendukung']);

                Notification::make()
                    ->warning()
                    ->title('Peringatan Upload File')
                    ->body('Terjadi masalah saat upload file. Anda dapat melengkapi dokumen pendukung nanti.')
                    ->send();
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Additional logging or processing after successful creation
        Log::info('Izin created successfully', [
            'user_id' => Auth::id(),
            'izin_id' => $this->getRecord()->id
        ]);
    }

        protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Ajukan Izin'), // Ubah label tombol submit
            $this->getCancelFormAction()
                ->label('Cancel'), // Tambah tombol cancel
        ];
    }
}
