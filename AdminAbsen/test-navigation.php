<?php

// Test navigation configuration
echo "=== TESTING NAVIGATION CONFIGURATION ===\n\n";

$files = [
    'DinaslLuarAttendance' => 'app/Filament/Pegawai/Pages/DinaslLuarAttendance.php',
    'WfoAttendance' => 'app/Filament/Pegawai/Pages/WfoAttendance.php',
    'MyAttendanceResource' => 'app/Filament/Pegawai/Resources/MyAttendanceResource.php',
    'MyDinasLuarResource' => 'app/Filament/Pegawai/Resources/MyDinasLuarResource.php',
    'MyIzinResource' => 'app/Filament/Pegawai/Resources/MyIzinResource.php',
    'MyOvertimeRequestResource' => 'app/Filament/Pegawai/Resources/MyOvertimeRequestResource.php',
];

foreach ($files as $name => $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);

        // Check if using getNavigationGroup method
        if (preg_match('/getNavigationGroup.*?return\s*[\'"]([^\'"]*)[\'"]/', $content, $groupMatch)) {
            $group = $groupMatch[1];
            echo "✅ $name: Using getNavigationGroup() = '$group'\n";
        } else if (preg_match('/navigationGroup\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $groupMatch)) {
            $group = $groupMatch[1];
            echo "❌ $name: Still using static property = '$group'\n";
        } else {
            echo "⚠️  $name: No navigation group found\n";
        }

        // Check if using getNavigationSort method
        if (preg_match('/getNavigationSort.*?return\s*(\d+)/', $content, $sortMatch)) {
            $sort = $sortMatch[1];
            echo "   Sort: getNavigationSort() = $sort\n";
        } else if (preg_match('/navigationSort\s*=\s*(\d+)/', $content, $sortMatch)) {
            $sort = $sortMatch[1];
            echo "   Sort: static property = $sort\n";
        } else {
            echo "   Sort: Not found\n";
        }
        echo "\n";
    }
}

echo "=== EXPECTED RESULT ===\n";
echo "1. Absensi Pegawai (should be first)\n";
echo "2. Data Absensi\n";
echo "3. Izin\n";
echo "4. Lembur\n";
