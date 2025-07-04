# PENGHAPUSAN BUTTON TAMBAH PEGAWAI BARU - KEPALA BIDANG

## Deskripsi

Menghilangkan button "Tambah Pegawai Baru" di menu Manajemen Pegawai untuk role Kepala Bidang, tanpa mengubah tampilan lainnya.

## File yang Dimodifikasi

-   `app/Filament/KepalaBidang/Resources/PegawaiResource.php`

## Perubahan yang Dilakukan

### 1. Menghapus Route Create

```php
// SEBELUM
public static function getPages(): array
{
    return [
        'index' => Pages\ListPegawais::route('/'),
        'create' => Pages\CreatePegawai::route('/create'), // ← DIHAPUS
        'view' => Pages\ViewPegawai::route('/{record}'),
        'edit' => Pages\EditPegawai::route('/{record}/edit'),
    ];
}

// SESUDAH
public static function getPages(): array
{
    return [
        'index' => Pages\ListPegawais::route('/'),
        'view' => Pages\ViewPegawai::route('/{record}'),
        'edit' => Pages\EditPegawai::route('/{record}/edit'),
    ];
}
```

### 2. Menambahkan Method canCreate()

```php
public static function canCreate(): bool
{
    return false;
}
```

## Hasil Implementasi

### Sebelum:

-   Button "Tambah Pegawai Baru" tersedia di halaman Manajemen Pegawai
-   Kepala Bidang dapat menambah pegawai baru

### Sesudah:

-   ✅ Button "Tambah Pegawai Baru" HILANG dari halaman Manajemen Pegawai
-   ✅ Kepala Bidang tidak dapat menambah pegawai baru
-   ✅ Fitur lain (View, Edit, Reset Password, Toggle Status) tetap berfungsi
-   ✅ Tampilan tabel dan data pegawai tidak berubah

## Keamanan

-   Route `/create` tidak lagi dapat diakses oleh Kepala Bidang
-   Method `canCreate()` memastikan button tidak muncul di UI
-   Fitur management pegawai lainnya tetap tersedia sesuai kebutuhan

## Catatan

-   Perubahan ini hanya berlaku untuk **Kepala Bidang**
-   Role lain (jika ada) tidak terpengaruh
-   Jika di masa depan ingin mengembalikan fitur tambah pegawai, cukup:
    1. Tambahkan kembali route 'create'
    2. Ubah `canCreate()` return menjadi `true` atau hapus method tersebut
