<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use App\Models\Jabatan;
use App\Models\Posisi;
use App\Models\Pendidikan;
use App\Models\NomorEmergency;
use App\Models\Fasilitas;
use App\Models\Pegawai;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    // Track current step
    public $currentStep = 0;
    public $maxSteps = 6; // Users, Jabatan, Posisi, Pendidikan, Emergency, Fasilitas

    // Store created record IDs for relationship
    protected $createdJabatanId = null;
    protected $createdPosisiId = null;
    protected $createdPendidikanIds = [];
    protected $createdEmergencyIds = [];
    protected $createdFasilitasIds = [];

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

    // Custom form actions (buttons)
    protected function getFormActions(): array
    {
        return [
            // Previous Button
            Actions\Action::make('previous')
                ->label('Sebelumnya')
                ->icon('heroicon-o-chevron-left')
                ->color('gray')
                ->visible($this->currentStep > 0)
                ->action(function () {
                    $this->previousStep();
                }),

            // Next/Save Button
            Actions\Action::make('next')
                ->label($this->currentStep >= ($this->maxSteps - 1) ? 'Simpan Data' : 'Selanjutnya')
                ->icon($this->currentStep >= ($this->maxSteps - 1) ? 'heroicon-o-check' : 'heroicon-o-chevron-right')
                ->color('primary')
                ->action(function () {
                    if ($this->currentStep >= ($this->maxSteps - 1)) {
                        // Final step - save all data
                        $this->saveAllData();
                    } else {
                        // Validate and save current step, then move to next
                        $this->validateSaveAndNext();
                    }
                }),

            // Cancel Button
            Actions\Action::make('cancel')
                ->label('Batal')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->action(function () {
                    $this->cleanupPartialData();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->outlined(),
        ];
    }

    // Method untuk validasi, simpan step saat ini, dan lanjut ke step berikutnya
    public function validateSaveAndNext()
    {
        DB::beginTransaction();

        try {
            // Validate current step data
            $this->validateCurrentStep();

            // Save current step data to respective table
            $this->saveCurrentStepData();

            // Save partial data to session
            $this->savePartialData();

            // Move to next step
            $this->nextStep();

            DB::commit();

            // Show success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Data step ' . ($this->currentStep) . ' berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            // Show error notification
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Method untuk menyimpan data step saat ini ke tabel yang sesuai
    protected function saveCurrentStepData()
    {
        $data = $this->form->getState();

        switch ($this->currentStep) {
            case 0: // Users tab - save basic pegawai data
                $this->saveUsersData($data);
                break;
            case 1: // Jabatan tab
                $this->saveJabatanData($data);
                break;
            case 2: // Posisi tab
                $this->savePosisiData($data);
                break;
            case 3: // Pendidikan tab
                $this->savePendidikanData($data);
                break;
            case 4: // Emergency tab
                $this->saveEmergencyData($data);
                break;
            case 5: // Fasilitas tab
                $this->saveFasilitasData($data);
                break;
        }
    }

    // Save Users data (Pegawai table)
    protected function saveUsersData($data)
    {
        // Create pegawai record if not exists
        if (!$this->record) {
            $pegawaiData = [
                'nama' => $data['nama'],
                'npp' => $data['npp'],
                'email' => $data['email'],
                'password' => $data['password'],
                'nik' => $data['nik'],
                'alamat' => $data['alamat'] ?? null,
                'status_pegawai' => $data['status_pegawai'],
                'status' => $data['status'],
                'role_user' => $data['role_user'],
            ];

            $this->record = Pegawai::create($pegawaiData);
        } else {
            // Update existing record
            $this->record->update([
                'nama' => $data['nama'],
                'npp' => $data['npp'],
                'email' => $data['email'],
                'nik' => $data['nik'],
                'alamat' => $data['alamat'] ?? null,
                'status_pegawai' => $data['status_pegawai'],
                'status' => $data['status'],
                'role_user' => $data['role_user'],
            ]);

            // Update password only if provided
            if (!empty($data['password'])) {
                $this->record->update(['password' => $data['password']]);
            }
        }
    }

    // Save Jabatan data
    protected function saveJabatanData($data)
    {
        if (isset($data['create_new_jabatan']) && $data['create_new_jabatan']) {
            // Create new jabatan
            $jabatan = Jabatan::create([
                'nama' => $data['jabatan_nama'],
                'tunjangan' => $data['jabatan_tunjangan'],
            ]);

            $this->createdJabatanId = $jabatan->id;
        } else if (isset($data['id_jabatan']) && $data['id_jabatan']) {
            // Use existing jabatan
            $this->createdJabatanId = $data['id_jabatan'];
        }

        // Update pegawai with jabatan_id
        if ($this->record && $this->createdJabatanId) {
            $this->record->update(['id_jabatan' => $this->createdJabatanId]);
        }
    }

    // Save Posisi data
    protected function savePosisiData($data)
    {
        if (isset($data['create_new_posisi']) && $data['create_new_posisi']) {
            // Create new posisi
            $posisi = Posisi::create([
                'nama' => $data['posisi_nama'],
                'tunjangan' => $data['posisi_tunjangan'],
            ]);

            $this->createdPosisiId = $posisi->id;
        } else if (isset($data['id_posisi']) && $data['id_posisi']) {
            // Use existing posisi
            $this->createdPosisiId = $data['id_posisi'];
        }

        // Update pegawai with posisi_id
        if ($this->record && $this->createdPosisiId) {
            $this->record->update(['id_posisi' => $this->createdPosisiId]);
        }
    }

    // Save Pendidikan data
    protected function savePendidikanData($data)
    {
        if (isset($data['pendidikan_list']) && !empty($data['pendidikan_list'])) {
            foreach ($data['pendidikan_list'] as $pendidikanData) {
                if (!empty($pendidikanData['jenjang']) && !empty($pendidikanData['sekolah_univ'])) {
                    $pendidikan = Pendidikan::create([
                        'user_id' => $this->record->id,
                        'jenjang' => $pendidikanData['jenjang'],
                        'nama_univ' => $pendidikanData['sekolah_univ'],
                        'jurusan' => $pendidikanData['jurusan'] ?? null,
                        'fakultas_program_studi' => $pendidikanData['fakultas_program_studi'] ?? null,
                        'thn_masuk' => $pendidikanData['thn_masuk'] ?? null,
                        'thn_lulus' => $pendidikanData['thn_lulus'] ?? null,
                        'ipk' => $pendidikanData['ipk_nilai'] ?? null,
                        'gelar' => $pendidikanData['gelar'] ?? null,
                        'ijazah_path' => $pendidikanData['ijazah'] ?? null,
                    ]);

                    $this->createdPendidikanIds[] = $pendidikan->id;
                }
            }
        }

        // Also save as JSON in pegawai table for backup
        if ($this->record) {
            $this->record->update(['pendidikan_list' => $data['pendidikan_list'] ?? []]);
        }
    }

    // Save Emergency Contact data
    protected function saveEmergencyData($data)
    {
        if (isset($data['emergency_contacts']) && !empty($data['emergency_contacts'])) {
            foreach ($data['emergency_contacts'] as $contactData) {
                if (!empty($contactData['nama_kontak']) && !empty($contactData['relationship'])) {
                    $emergency = NomorEmergency::create([
                        'user_id' => $this->record->id,
                        'relationship' => $contactData['relationship'],
                        'nama' => $contactData['nama_kontak'],
                        'no_emergency' => $contactData['no_emergency'],
                        'alamat' => $contactData['alamat_kontak'] ?? null,
                    ]);

                    $this->createdEmergencyIds[] = $emergency->id;
                }
            }
        }

        // Also save as JSON in pegawai table for backup
        if ($this->record) {
            $this->record->update(['emergency_contacts' => $data['emergency_contacts'] ?? []]);
        }
    }

    // Save Fasilitas data
    protected function saveFasilitasData($data)
    {
        if (isset($data['fasilitas_list']) && !empty($data['fasilitas_list'])) {
            foreach ($data['fasilitas_list'] as $fasilitasData) {
                if (!empty($fasilitasData['nama_jaminan'])) {
                    $fasilitas = Fasilitas::create([
                        'user_id' => $this->record->id,
                        'nama_jaminan' => $fasilitasData['nama_jaminan'],
                        'no_jaminan' => $fasilitasData['no_jaminan'],
                        'jenis_fasilitas' => $fasilitasData['jenis_fasilitas'] ?? null,
                        'provider' => $fasilitasData['provider'] ?? null,
                        'nilai_fasilitas' => $fasilitasData['nilai_fasilitas'] ?? 0,
                        'tanggal_mulai' => $fasilitasData['tanggal_mulai'] ?? null,
                        'tanggal_berakhir' => $fasilitasData['tanggal_berakhir'] ?? null,
                        'status_fasilitas' => $fasilitasData['status_fasilitas'] ?? 'aktif',
                        'keterangan' => $fasilitasData['keterangan'] ?? null,
                        'dokumen_path' => $fasilitasData['dokumen_fasilitas'] ?? null,
                        'transport' => $fasilitasData['transport'] ?? 0,
                        'overtime_id' => $fasilitasData['overtime_rate'] ?? 0,
                        'payroll' => $fasilitasData['payroll'] ?? 0,
                    ]);

                    $this->createdFasilitasIds[] = $fasilitas->id;
                }
            }
        }

        // Also save as JSON in pegawai table for backup
        if ($this->record) {
            $this->record->update(['fasilitas_list' => $data['fasilitas_list'] ?? []]);
        }
    }

    // Method untuk validasi step saat ini
    protected function validateCurrentStep()
    {
        $data = $this->form->getState();

        switch ($this->currentStep) {
            case 0: // Users tab
                $this->validateUsersStep($data);
                break;
            case 1: // Jabatan tab
                $this->validateJabatanStep($data);
                break;
            case 2: // Posisi tab
                $this->validatePosisiStep($data);
                break;
            case 3: // Pendidikan tab
                $this->validatePendidikanStep($data);
                break;
            case 4: // Emergency tab
                $this->validateEmergencyStep($data);
                break;
            case 5: // Fasilitas tab
                $this->validateFasilitasStep($data);
                break;
        }
    }

    // Validation methods untuk setiap step
    protected function validateUsersStep($data)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'npp' => 'required|unique:pegawais,npp' . ($this->record ? ',' . $this->record->id : ''),
            'email' => 'required|email|unique:pegawais,email' . ($this->record ? ',' . $this->record->id : ''),
            'nik' => 'required|unique:pegawais,nik' . ($this->record ? ',' . $this->record->id : ''),
            'status_pegawai' => 'required|in:PTT,LS',
            'status' => 'required|in:active,resign',
            'role_user' => 'required|in:super admin,employee,Kepala Bidang',
        ];

        // Password required only on create
        if (!$this->record) {
            $rules['password'] = 'required|min:8';
        }

        validator($data, $rules)->validate();
    }

    protected function validateJabatanStep($data)
    {
        if (isset($data['create_new_jabatan']) && $data['create_new_jabatan']) {
            $rules = [
                'jabatan_nama' => 'required|string|max:255',
                'jabatan_tunjangan' => 'required|numeric|min:0',
            ];
            validator($data, $rules)->validate();
        }
    }

    protected function validatePosisiStep($data)
    {
        if (isset($data['create_new_posisi']) && $data['create_new_posisi']) {
            $rules = [
                'posisi_nama' => 'required|string|max:255',
                'posisi_tunjangan' => 'required|numeric|min:0',
            ];
            validator($data, $rules)->validate();
        }
    }

    protected function validatePendidikanStep($data)
    {
        if (isset($data['pendidikan_list']) && !empty($data['pendidikan_list'])) {
            foreach ($data['pendidikan_list'] as $index => $pendidikan) {
                if (!empty($pendidikan['jenjang'])) {
                    $rules = [
                        "pendidikan_list.$index.jenjang" => 'required',
                        "pendidikan_list.$index.sekolah_univ" => 'required',
                    ];
                    validator($data, $rules)->validate();
                }
            }
        }
    }

    protected function validateEmergencyStep($data)
    {
        if (isset($data['emergency_contacts']) && !empty($data['emergency_contacts'])) {
            foreach ($data['emergency_contacts'] as $index => $contact) {
                if (!empty($contact['nama_kontak'])) {
                    $rules = [
                        "emergency_contacts.$index.relationship" => 'required',
                        "emergency_contacts.$index.nama_kontak" => 'required',
                        "emergency_contacts.$index.no_emergency" => 'required',
                    ];
                    validator($data, $rules)->validate();
                }
            }
        }
    }

    protected function validateFasilitasStep($data)
    {
        if (isset($data['fasilitas_list']) && !empty($data['fasilitas_list'])) {
            foreach ($data['fasilitas_list'] as $index => $fasilitas) {
                if (!empty($fasilitas['nama_jaminan'])) {
                    $rules = [
                        "fasilitas_list.$index.nama_jaminan" => 'required',
                        "fasilitas_list.$index.no_jaminan" => 'required',
                    ];
                    validator($data, $rules)->validate();
                }
            }
        }
    }

    // Method untuk menyimpan data sementara ke session
    protected function savePartialData()
    {
        $data = $this->form->getState();
        $sessionKey = 'pegawai_form_data_' . session()->getId();

        // Get existing session data
        $sessionData = session($sessionKey, []);

        // Add step-specific data
        $sessionData['step_' . $this->currentStep] = $data;
        $sessionData['current_step'] = $this->currentStep + 1;
        $sessionData['pegawai_id'] = $this->record?->id;
        $sessionData['created_ids'] = [
            'jabatan_id' => $this->createdJabatanId,
            'posisi_id' => $this->createdPosisiId,
            'pendidikan_ids' => $this->createdPendidikanIds,
            'emergency_ids' => $this->createdEmergencyIds,
            'fasilitas_ids' => $this->createdFasilitasIds,
        ];

        // Save to session
        session([$sessionKey => $sessionData]);
    }

    // Method untuk load data dari session
    protected function loadPartialData()
    {
        $sessionKey = 'pegawai_form_data_' . session()->getId();
        return session($sessionKey, []);
    }

    // Navigation methods
    public function nextStep()
    {
        if ($this->currentStep < ($this->maxSteps - 1)) {
            $this->currentStep++;
            $this->switchToTab($this->currentStep);
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->switchToTab($this->currentStep);
        }
    }

    // Method untuk switch tab dengan JavaScript
    protected function switchToTab($tabIndex)
    {
        $tabNames = ['users', 'jabatan', 'posisi', 'pendidikan', 'nomor-emergency', 'fasilitas'];

        if (isset($tabNames[$tabIndex])) {
            $this->dispatch('switchTab', $tabNames[$tabIndex]);
        }
    }

    // Final save method - untuk finalisasi relasi dan cleanup
    public function saveAllData()
    {
        DB::beginTransaction();

        try {
            // Validate final step
            $this->validateCurrentStep();

            // Save final step data
            $this->saveCurrentStepData();

            // Update final relationships if needed
            $this->updateFinalRelationships();

            // Clear session
            $this->cleanupPartialData();

            DB::commit();

            // Show success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $this->getCreatedNotificationTitle()
            ]);

            // Redirect
            $this->redirect($this->getRedirectUrl());

        } catch (\Exception $e) {
            DB::rollback();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error saving final data: ' . $e->getMessage()
            ]);
        }
    }

    // Update final relationships
    protected function updateFinalRelationships()
    {
        if ($this->record) {
            $updateData = [];

            // Set primary pendidikan if exists
            if (!empty($this->createdPendidikanIds)) {
                $updateData['id_pendidikan'] = $this->createdPendidikanIds[0];
            }

            // Set primary emergency contact if exists
            if (!empty($this->createdEmergencyIds)) {
                $updateData['id_nomor_emergency'] = $this->createdEmergencyIds[0];
            }

            // Set primary fasilitas if exists
            if (!empty($this->createdFasilitasIds)) {
                $updateData['id_fasilitas'] = $this->createdFasilitasIds[0];
            }

            if (!empty($updateData)) {
                $this->record->update($updateData);
            }
        }
    }

    // Cleanup partial data from session
    protected function cleanupPartialData()
    {
        $sessionKey = 'pegawai_form_data_' . session()->getId();
        session()->forget($sessionKey);
    }

    // Cleanup method for rollback scenarios
    protected function rollbackCreatedData()
    {
        try {
            // Delete created records in reverse order
            if (!empty($this->createdFasilitasIds)) {
                Fasilitas::whereIn('id', $this->createdFasilitasIds)->delete();
            }

            if (!empty($this->createdEmergencyIds)) {
                NomorEmergency::whereIn('id', $this->createdEmergencyIds)->delete();
            }

            if (!empty($this->createdPendidikanIds)) {
                Pendidikan::whereIn('id', $this->createdPendidikanIds)->delete();
            }

            if ($this->createdPosisiId) {
                Posisi::find($this->createdPosisiId)?->delete();
            }

            if ($this->createdJabatanId) {
                Jabatan::find($this->createdJabatanId)?->delete();
            }

            if ($this->record) {
                $this->record->delete();
            }

        } catch (\Exception $e) {
            // Log error but don't throw to avoid masking original error
            logger()->error('Error during rollback: ' . $e->getMessage());
        }
    }

    // Override mount to load session data and restore state
    public function mount(): void
    {
        parent::mount();

        // Load partial data from session if exists
        $sessionData = $this->loadPartialData();
        if (!empty($sessionData)) {
            // Restore current step
            $this->currentStep = $sessionData['current_step'] ?? 0;

            // Restore pegawai record if exists
            if (isset($sessionData['pegawai_id'])) {
                $this->record = Pegawai::find($sessionData['pegawai_id']);
            }

            // Restore created IDs
            if (isset($sessionData['created_ids'])) {
                $createdIds = $sessionData['created_ids'];
                $this->createdJabatanId = $createdIds['jabatan_id'] ?? null;
                $this->createdPosisiId = $createdIds['posisi_id'] ?? null;
                $this->createdPendidikanIds = $createdIds['pendidikan_ids'] ?? [];
                $this->createdEmergencyIds = $createdIds['emergency_ids'] ?? [];
                $this->createdFasilitasIds = $createdIds['fasilitas_ids'] ?? [];
            }

            // Fill form with merged session data
            $formData = [];
            foreach ($sessionData as $key => $value) {
                if (strpos($key, 'step_') === 0) {
                    $formData = array_merge($formData, $value);
                }
            }

            if (!empty($formData)) {
                $this->form->fill($formData);
            }
        }
    }

    protected function getViewData(): array
    {
        return [
            'currentStep' => $this->currentStep,
            'maxSteps' => $this->maxSteps,
            'stepProgress' => (($this->currentStep + 1) / $this->maxSteps) * 100,
        ];
    }

    // Method untuk render progress bar
    public function getProgressBarHtml(): string
    {
        $progress = (($this->currentStep + 1) / $this->maxSteps) * 100;
        $stepNames = ['Data Pribadi', 'Jabatan', 'Posisi', 'Pendidikan', 'Kontak Darurat', 'Fasilitas'];

        $html = '<div class="mb-6">';
        $html .= '<div class="flex justify-between items-center mb-2">';
        $html .= '<span class="text-sm font-medium text-gray-700">Step ' . ($this->currentStep + 1) . ' dari ' . $this->maxSteps . '</span>';
        $html .= '<span class="text-sm font-medium text-gray-700">' . round($progress) . '%</span>';
        $html .= '</div>';
        $html .= '<div class="w-full bg-gray-200 rounded-full h-2">';
        $html .= '<div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: ' . $progress . '%"></div>';
        $html .= '</div>';
        $html .= '<div class="mt-2 text-sm text-gray-600">' . ($stepNames[$this->currentStep] ?? 'Unknown Step') . '</div>';
        $html .= '</div>';

        return $html;
    }

    // Handle page leave/refresh
    public function dehydrate()
    {
        // Save current state when component is dehydrated
        $this->savePartialData();
    }
}
