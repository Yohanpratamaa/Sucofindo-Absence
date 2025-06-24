<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

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

        return $data;
    }
}
