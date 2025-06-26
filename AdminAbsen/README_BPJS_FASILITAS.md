# Implementasi Aturan Fasilitas BPJS - Sistem Pegawai Sucofindo

## Overview
Sistem manajemen pegawai telah diimplementasikan dengan aturan khusus untuk fasilitas BPJS yang tidak memerlukan nominal, sedangkan fasilitas lainnya wajib memiliki nominal.

## Aturan Fasilitas

### BPJS (Tidak Perlu Nominal)
- **BPJS Kesehatan**: Nominal otomatis 0, field disabled
- **BPJS Ketenagakerjaan**: Nominal otomatis 0, field disabled
- **Visual Indicator**: ❌ BPJS tidak perlu nominal (otomatis Rp 0)
- **Validation**: Field nominal tidak required

### Fasilitas Lainnya (Wajib Nominal)
- **Asuransi Jiwa**: Wajib nominal
- **Asuransi Kesehatan**: Wajib nominal  
- **Tunjangan Transport**: Wajib nominal
- **Tunjangan Makan**: Wajib nominal
- **Tunjangan Komunikasi**: Wajib nominal
- **Lainnya**: Wajib nominal
- **Visual Indicator**: ✅ Nilai dalam rupiah per bulan (wajib diisi)
- **Validation**: Field nominal required

## Implementasi Technical

### 1. Form Logic (PegawaiResource.php)

#### Dynamic Field Behavior
```php
Forms\Components\Select::make('jenis_fasilitas')
    ->live() // Enable real-time updates
    ->afterStateUpdated(function ($state, callable $set) {
        // Reset nominal if BPJS is selected
        if (in_array($state, ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'])) {
            $set('nilai_fasilitas', 0);
        }
    })
```

#### Conditional Nominal Field
```php
Forms\Components\TextInput::make('nilai_fasilitas')
    ->disabled(function (callable $get): bool {
        $jenisFasilitas = $get('jenis_fasilitas');
        return in_array($jenisFasilitas, ['BPJS Kesehatan', 'BPJS Ketenagakerjaan']);
    })
    ->required(function (callable $get): bool {
        $jenisFasilitas = $get('jenis_fasilitas');
        return !in_array($jenisFasilitas, ['BPJS Kesehatan', 'BPJS Ketenagakerjaan']);
    })
    ->helperText(function (callable $get): string {
        $jenisFasilitas = $get('jenis_fasilitas');
        if (in_array($jenisFasilitas, ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'])) {
            return '❌ BPJS tidak perlu nominal (otomatis Rp 0)';
        }
        return '✅ Nilai dalam rupiah per bulan (wajib diisi)';
    })
```

#### Data Dehydration
```php
->dehydrateStateUsing(function ($state, callable $get) {
    // For BPJS, always return 0
    $jenisFasilitas = $get('jenis_fasilitas');
    if (in_array($jenisFasilitas, ['BPJS Kesehatan', 'BPJS Ketenagakerjaan'])) {
        return 0;
    }
    // Remove dots and convert to integer for other facilities
    return $state ? (int) str_replace('.', '', $state) : 0;
})
```

### 2. Create Logic (CreatePegawai.php)

#### Data Processing Before Create
```php
protected function mutateFormDataBeforeCreate(array $data): array
{
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
```

### 3. Edit Logic (EditPegawai.php)

#### Data Processing Before Save
```php
protected function mutateFormDataBeforeSave(array $data): array
{
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
```

### 4. Model Logic (Pegawai.php)

#### Total Nilai Fasilitas (Excluding BPJS)
```php
public function getTotalNilaiFasilitasAttribute()
{
    if (!$this->fasilitas_list) {
        return 0;
    }

    return collect($this->fasilitas_list)
        ->filter(function ($fasilitas) {
            // Exclude BPJS facilities from total calculation
            return !in_array($fasilitas['jenis_fasilitas'] ?? '', ['BPJS Kesehatan', 'BPJS Ketenagakerjaan']);
        })
        ->sum('nilai_fasilitas') ?? 0;
}
```

#### Total BPJS Count
```php
public function getTotalNilaiBpjsAttribute()
{
    if (!$this->fasilitas_list) {
        return 0;
    }

    return collect($this->fasilitas_list)
        ->filter(function ($fasilitas) {
            // Only BPJS facilities
            return in_array($fasilitas['jenis_fasilitas'] ?? '', ['BPJS Kesehatan', 'BPJS Ketenagakerjaan']);
        })
        ->count(); // Count BPJS entries (since they don't have nominal value)
}
```

## UI/UX Features

### Visual Indicators
- **BPJS**: Field nominal disabled, helper text dengan ❌ icon
- **Non-BPJS**: Field nominal aktif, helper text dengan ✅ icon
- **Real-time**: Changes langsung terlihat saat memilih jenis fasilitas

### Form Validation
- **BPJS**: Field nominal tidak required, otomatis set ke 0
- **Non-BPJS**: Field nominal required, harus diisi

### Helper Text Dynamic
- **BPJS**: "❌ BPJS tidak perlu nominal (otomatis Rp 0)"
- **Non-BPJS**: "✅ Nilai dalam rupiah per bulan (wajib diisi)"

## Data Structure

### Fasilitas List JSON Structure
```json
[
    {
        "nama_jaminan": "BPJS Kesehatan",
        "no_jaminan": "0001234567890",
        "jenis_fasilitas": "BPJS Kesehatan",
        "provider": "BPJS",
        "nilai_fasilitas": 0
    },
    {
        "nama_jaminan": "Asuransi Jiwa",
        "no_jaminan": "POL-001234",
        "jenis_fasilitas": "Asuransi Jiwa",
        "provider": "Prudential",
        "nilai_fasilitas": 500000
    }
]
```

## Business Logic Flow

### 1. User Selects Facility Type
- **If BPJS**: Nominal field disabled, value auto-set to 0
- **If Non-BPJS**: Nominal field enabled, required validation

### 2. Form Submission
- **BPJS**: Backend forces value to 0 regardless of input
- **Non-BPJS**: Normal validation and processing

### 3. Data Storage
- **BPJS**: Always stored with `nilai_fasilitas = 0`
- **Non-BPJS**: Stored with actual nominal value

### 4. Data Display
- **Total Calculation**: Excludes BPJS from total nilai fasilitas
- **BPJS Count**: Separate accessor for counting BPJS entries

## Testing Scenarios

### Test Case 1: Create Pegawai with BPJS
1. Go to Create Pegawai page
2. Navigate to Fasilitas tab
3. Add BPJS Kesehatan
4. Verify nominal field is disabled
5. Submit form
6. Verify BPJS stored with nilai_fasilitas = 0

### Test Case 2: Create Pegawai with Non-BPJS
1. Go to Create Pegawai page
2. Navigate to Fasilitas tab
3. Add Tunjangan Transport
4. Enter nominal value
5. Submit form
6. Verify nominal value stored correctly

### Test Case 3: Switch Facility Type
1. Start with Non-BPJS facility
2. Enter nominal value
3. Switch to BPJS type
4. Verify nominal field becomes disabled and resets to 0

### Test Case 4: Edit Existing Pegawai
1. Edit pegawai with existing facilities
2. Change Non-BPJS to BPJS
3. Verify nominal resets to 0
4. Change BPJS to Non-BPJS
5. Verify nominal field becomes required

## Status Implementation

✅ **COMPLETED:**
- Dynamic form fields based on facility type
- BPJS nominal auto-set to 0
- Non-BPJS nominal validation required
- Visual indicators with emoji
- Real-time form updates
- Backend data processing (Create & Edit)
- Model accessors for proper calculation
- Total fasilitas excludes BPJS values
- Separate BPJS count accessor

**Ready for Production Use!**
