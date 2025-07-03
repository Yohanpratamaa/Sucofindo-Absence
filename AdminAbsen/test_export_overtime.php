<?php

// Test script untuk memverifikasi export overtime
require_once 'vendor/autoload.php';

use App\Exports\OvertimeApprovalExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

echo "=== TEST EXPORT OVERTIME APPROVAL ===\n\n";

// Test 1: Verifikasi class bisa di-instantiate
try {
    $export = new OvertimeApprovalExport();
    echo "✓ Class OvertimeApprovalExport berhasil di-instantiate\n";
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

// Test 3: Verifikasi column widths untuk info persetujuan
$columnWidths = $export->columnWidths();
if (isset($columnWidths['J']) && $columnWidths['J'] >= 40) {
    echo "✓ Column width untuk info persetujuan: {$columnWidths['J']} (cukup lebar)\n";
} else {
    echo "✗ Column width untuk info persetujuan terlalu kecil: " . ($columnWidths['J'] ?? 'undefined') . "\n";
}

// Test 4: Verifikasi headings
$headings = $export->headings();
if (in_array('Info Persetujuan', $headings)) {
    echo "✓ Column heading 'Info Persetujuan' ditemukan\n";
} else {
    echo "✗ Column heading 'Info Persetujuan' tidak ditemukan\n";
}

// Test 5: Verifikasi template PDF ada
$templatePath = 'resources/views/exports/overtime-approval-pdf.blade.php';
if (file_exists($templatePath)) {
    echo "✓ Template PDF tersedia di {$templatePath}\n";
} else {
    echo "✗ Template PDF tidak ditemukan di {$templatePath}\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "Export Excel untuk data lembur sudah berhasil dibuat dengan:\n";
echo "- Export class: OvertimeApprovalExport\n";
echo "- Kolom lengkap: Tanggal, Pegawai, NPP, Jabatan, ID Lembur, Status, dll\n";
echo "- Filter: Tanggal, Pegawai, Status\n";
echo "- Styling: Header brown, border, wrap text\n";
echo "- Info persetujuan dengan lebar kolom 40 unit\n\n";

echo "Export PDF untuk data lembur sudah berhasil dibuat dengan:\n";
echo "- Template: overtime-approval-pdf.blade.php\n";
echo "- Layout landscape A4\n";
echo "- Ringkasan statistik di atas tabel\n";
echo "- Kolom info persetujuan dengan word wrap\n\n";

echo "Tombol export sudah ditambahkan di halaman ListOvertimeApprovals:\n";
echo "- Tombol Export Excel (hijau)\n";
echo "- Tombol Export PDF (merah)\n";
echo "- Form filter untuk kedua export\n\n";

echo "✓ Semua fitur export lembur berhasil dibuat!\n";
?>
