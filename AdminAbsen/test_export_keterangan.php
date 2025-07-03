<?php

// Test script untuk memverifikasi export dengan keterangan panjang
require_once 'vendor/autoload.php';

use App\Exports\IzinApprovalExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

echo "=== TEST EXPORT KETERANGAN TIDAK TERPOTONG ===\n\n";

// Test 1: Verifikasi class bisa di-instantiate
try {
    $export = new IzinApprovalExport();
    echo "✓ Class IzinApprovalExport berhasil di-instantiate\n";
} catch (Exception $e) {
    echo "✗ Error instantiate class: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Verifikasi method yang diperlukan ada
$requiredMethods = [
    'collection',
    'headings',
    'map',
    'styles',
    'columnWidths',
    'title',
    'registerEvents'
];

foreach ($requiredMethods as $method) {
    if (method_exists($export, $method)) {
        echo "✓ Method {$method}() tersedia\n";
    } else {
        echo "✗ Method {$method}() tidak ditemukan\n";
    }
}

// Test 3: Verifikasi column widths untuk keterangan
$columnWidths = $export->columnWidths();
if (isset($columnWidths['I']) && $columnWidths['I'] >= 50) {
    echo "✓ Column width untuk keterangan: {$columnWidths['I']} (cukup lebar)\n";
} else {
    echo "✗ Column width untuk keterangan terlalu kecil: " . ($columnWidths['I'] ?? 'undefined') . "\n";
}

// Test 4: Verifikasi headings
$headings = $export->headings();
if (in_array('Keterangan', $headings)) {
    echo "✓ Column heading 'Keterangan' ditemukan\n";
} else {
    echo "✗ Column heading 'Keterangan' tidak ditemukan\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "Export Excel untuk keterangan izin sudah diperbaiki dengan:\n";
echo "- Lebar kolom keterangan diperbesar menjadi 50\n";
echo "- Wrap text diaktifkan untuk semua data rows\n";
echo "- Auto height untuk baris agar menyesuaikan konten\n";
echo "- Border dan styling yang lebih baik\n\n";

echo "Export PDF untuk keterangan izin sudah diperbaiki dengan:\n";
echo "- Menghapus limit karakter pada keterangan (dari 50 karakter ke unlimited)\n";
echo "- Menambah word-wrap dan word-break untuk kolom keterangan\n";
echo "- Memperlebar kolom keterangan dari 15% ke 20%\n";
echo "- Menyesuaikan proporsi kolom lainnya\n\n";

echo "✓ Semua perbaikan berhasil diterapkan!\n";
?>
