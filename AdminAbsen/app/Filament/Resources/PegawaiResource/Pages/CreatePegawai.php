<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Services\UserRoleService;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    public function getTitle(): string
    {
        return 'Tambah Data Pegawai';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data pegawai berhasil ditambahkan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password jika ada
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
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

    protected function getCreatedNotification(): ?Notification
    {
        $pegawai = $this->getRecord();
        $roleMessage = match($pegawai->role_user) {
            'employee' => 'Akun pegawai telah dibuat. Pegawai dapat login di /pegawai',
            'Kepala Bidang' => 'Akun kepala bidang telah dibuat. Kepala bidang dapat login di /kepala-bidang',
            'super admin' => 'Akun admin telah dibuat. Admin dapat login di /admin',
            default => 'Akun telah berhasil dibuat.'
        };

        return Notification::make()
            ->success()
            ->title('Pegawai Berhasil Ditambahkan')
            ->body($roleMessage . ' Email: ' . $pegawai->email . ' | Password default: password123');
    }

    // Override form actions untuk hanya menampilkan Create dan Cancel
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Tambah Data'), // Label tombol sesuai gambar
            $this->getCancelFormAction()
                ->label('Tidak Jadi'), // Label tombol cancel
        ];
    }

    // Override untuk menghilangkan tombol "Create & create another"
    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return Actions\Action::make('createAnother')
            ->visible(false); // Sembunyikan tombol create another
    }
}
