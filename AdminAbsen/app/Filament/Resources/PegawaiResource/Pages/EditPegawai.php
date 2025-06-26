<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Data Pegawai: ' . $this->record->nama;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pegawai berhasil diupdate';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password hanya jika diisi
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Hapus password dari data jika kosong (tidak diubah)
            unset($data['password']);
        }

        // Pastikan data JSON kosong jika tidak ada
        $data['pendidikan_list'] = $data['pendidikan_list'] ?? [];
        $data['emergency_contacts'] = $data['emergency_contacts'] ?? [];
        $data['fasilitas_list'] = $data['fasilitas_list'] ?? [];

        // Set default values untuk tunjangan jika kosong
        $data['jabatan_tunjangan'] = $data['jabatan_tunjangan'] ?? 0;
        $data['posisi_tunjangan'] = $data['posisi_tunjangan'] ?? 0;

        // Proses fasilitas list - Set nilai BPJS ke 0
        if (isset($data['fasilitas_list']) && is_array($data['fasilitas_list'])) {
            foreach ($data['fasilitas_list'] as $key => $fasilitas) {
                // Jika jenis fasilitas adalah BPJS, set nominal ke 0
                if (isset($fasilitas['jenis_fasilitas']) &&
                    in_array($fasilitas['jenis_fasilitas'], ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'])) {
                    $data['fasilitas_list'][$key]['nilai_fasilitas'] = 0;
                }
                // Pastikan nilai fasilitas lain memiliki default 0 jika kosong
                else {
                    $data['fasilitas_list'][$key]['nilai_fasilitas'] = $fasilitas['nilai_fasilitas'] ?? 0;
                }
            }
        }

        return $data;
    }

    // Override form actions untuk Edit page juga
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Save'), // Label tombol save untuk edit
            $this->getCancelFormAction()
                ->label('Cancel'), // Label tombol cancel
        ];
    }
}
