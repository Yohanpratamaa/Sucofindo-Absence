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
    
    // Store original password for notification
    protected ?string $originalPassword = null;

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
        // Store original password for notification
        if (isset($data['password']) && !empty($data['password'])) {
            $this->originalPassword = $data['password'];
            $data['password'] = Hash::make($data['password']);
        } else {
            // Set default password jika tidak diisi
            $this->originalPassword = 'password123';
            $data['password'] = Hash::make('password123');
        }

        // Pastikan email ada jika kosong
        if (empty($data['email'])) {
            $data['email'] = strtolower($data['npp']) . '@sucofindo.com';
        }

        // Pastikan status active untuk login
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        // Pastikan data JSON kosong jika tidak ada
        $data['pendidikan_list'] = $data['pendidikan_list'] ?? [];
        $data['emergency_contacts'] = $data['emergency_contacts'] ?? [];
        $data['fasilitas_list'] = $data['fasilitas_list'] ?? [];

        // Set default values untuk tunjangan jika kosong
        $data['jabatan_tunjangan'] = $data['jabatan_tunjangan'] ?? 0;
        $data['posisi_tunjangan'] = $data['posisi_tunjangan'] ?? 0;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Setup account after creation
        $pegawai = $this->getRecord();
        
        // Ensure we have a Pegawai instance
        if ($pegawai instanceof Pegawai) {
            // Call UserRoleService to setup account based on role
            UserRoleService::createUserBasedOnRole($pegawai);
            
            // Log account creation with password verification
            \Illuminate\Support\Facades\Log::info("New account created: {$pegawai->nama} ({$pegawai->email}) with role: {$pegawai->role_user}");
            
            // Test password verification for debugging
            if ($this->originalPassword) {
                $passwordWorks = Hash::check($this->originalPassword, $pegawai->password);
                \Illuminate\Support\Facades\Log::info("Password verification test: " . ($passwordWorks ? 'SUCCESS' : 'FAILED') . " for user: {$pegawai->email}");
            }
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        $pegawai = $this->getRecord();
        
        // Get login URL based on role
        $loginUrl = match($pegawai->role_user) {
            'employee' => '/pegawai',
            'Kepala Bidang' => '/kepala-bidang', 
            'super admin' => '/admin',
            default => '/login'
        };
        
        $roleMessage = match($pegawai->role_user) {
            'employee' => "Akun pegawai telah dibuat dan SIAP LOGIN.\nURL: {$loginUrl}",
            'Kepala Bidang' => "Akun kepala bidang telah dibuat dan SIAP LOGIN.\nURL: {$loginUrl}",
            'super admin' => "Akun admin telah dibuat dan SIAP LOGIN.\nURL: {$loginUrl}",
            default => 'Akun telah berhasil dibuat dan SIAP LOGIN.'
        };

        // Get password info - use stored original password
        $passwordInfo = $this->originalPassword 
            ? 'Password: ' . $this->originalPassword
            : 'Password default: password123';

        return Notification::make()
            ->success()
            ->title('✅ Pegawai Berhasil Ditambahkan')
            ->body($roleMessage . "\n\n📧 Email: {$pegawai->email}\n🔐 {$passwordInfo}\n\n⚡ Akun langsung dapat digunakan untuk login!")
            ->duration(8000); // Show longer to read all info
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
