<?php

require_once 'vendor/autoload.php';

echo "=== DEBUGGING FILAMENT NAVIGATION ===\n\n";

// Check all Pegawai Resources and Pages
$pegawaiFiles = [
    'DinaslLuarAttendance.php' => 'app/Filament/Pegawai/Pages/DinaslLuarAttendance.php',
    'WfoAttendance.php' => 'app/Filament/Pegawai/Pages/WfoAttendance.php',
    'MyAttendanceResource.php' => 'app/Filament/Pegawai/Resources/MyAttendanceResource.php',
    'MyDinasLuarResource.php' => 'app/Filament/Pegawai/Resources/MyDinasLuarResource.php',
    'MyIzinResource.php' => 'app/Filament/Pegawai/Resources/MyIzinResource.php',
    'MyOvertimeRequestResource.php' => 'app/Filament/Pegawai/Resources/MyOvertimeRequestResource.php',
];

foreach ($pegawaiFiles as $name => $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Extract navigation details
        preg_match('/navigationGroup\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $groupMatch);
        preg_match('/navigationSort\s*=\s*(\d+)/', $content, $sortMatch);
        preg_match('/navigationLabel\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $labelMatch);

        $group = $groupMatch[1] ?? 'No Group';
        $sort = $sortMatch[1] ?? 'No Sort';
        $label = $labelMatch[1] ?? 'No Label';

        echo sprintf("%-30s | Group: %-15s | Sort: %-3s | Label: %s\n",
            $name, $group, $sort, $label);
    } else {
        echo sprintf("%-30s | FILE NOT FOUND!\n", $name);
    }
}

echo "\n=== EXPECTED ORDER ===\n";
echo "1. Absensi Pegawai (Sort: 1-2)\n";
echo "2. Data Absensi (Sort: 11-12)\n";
echo "3. Izin (Sort: 21)\n";
echo "4. Lembur (Sort: 31)\n";
