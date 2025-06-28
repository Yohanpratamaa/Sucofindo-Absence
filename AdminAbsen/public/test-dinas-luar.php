<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dinas Luar Images</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card { border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 5px; }
        .image-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0; }
        .image-item { text-align: center; }
        .image-item img { max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; }
        .status { padding: 5px 10px; border-radius: 3px; color: white; }
        .success { background-color: green; }
        .warning { background-color: orange; }
        .danger { background-color: red; }
        .gray { background-color: gray; }
    </style>
</head>
<body>
    <h1>Test Absensi Dinas Luar - Images Display</h1>

    <?php
    // Simple Laravel bootstrap for testing
    require_once '../bootstrap/app.php';

    use App\Models\Attendance;
    use Illuminate\Support\Facades\Storage;

    $dinasLuarAttendances = Attendance::where('attendance_type', 'Dinas Luar')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    if ($dinasLuarAttendances->isEmpty()) {
        echo '<p>❌ Tidak ada data absensi dinas luar ditemukan.</p>';

        echo '<h3>Data Attendance Yang Tersedia:</h3>';
        $allAttendances = Attendance::take(10)->get();
        foreach ($allAttendances as $att) {
            echo "<p>ID: {$att->id}, Type: {$att->attendance_type}, Date: {$att->created_at->format('d M Y')}</p>";
        }
    } else {
        echo '<p>✅ Ditemukan ' . $dinasLuarAttendances->count() . ' data absensi dinas luar</p>';

        foreach ($dinasLuarAttendances as $attendance) {
            echo '<div class="card">';
            echo '<h3>Attendance ID: ' . $attendance->id . '</h3>';
            echo '<p><strong>Tanggal:</strong> ' . $attendance->created_at->format('d F Y H:i') . '</p>';
            echo '<p><strong>User ID:</strong> ' . $attendance->user_id . '</p>';
            echo '<p><strong>Type:</strong> ' . $attendance->attendance_type . '</p>';
            echo '<p><strong>Status:</strong> <span class="status ' . $attendance->status_color . '">' . $attendance->status_kehadiran . '</span></p>';

            $kelengkapan = $attendance->kelengkapan_absensi;
            echo '<p><strong>Progress:</strong> ' . $kelengkapan['completed'] . '/' . $kelengkapan['total'] . ' (' . $kelengkapan['percentage'] . '%) - ' . $kelengkapan['status'] . '</p>';

            echo '<div class="image-grid">';

            // Check In Image
            echo '<div class="image-item">';
            echo '<h4>Foto Absen Pagi</h4>';
            echo '<p><strong>Raw Path:</strong> ' . ($attendance->picture_absen_masuk ?? 'NULL') . '</p>';
            echo '<p><strong>URL:</strong> ' . $attendance->picture_absen_masuk_url . '</p>';
            echo '<img src="' . $attendance->picture_absen_masuk_url . '" alt="Foto Absen Pagi" onerror="this.src=\'/images/no-image.png\'">';
            echo '</div>';

            // Absen Siang Image
            echo '<div class="image-item">';
            echo '<h4>Foto Absen Siang</h4>';
            echo '<p><strong>Raw Path:</strong> ' . ($attendance->picture_absen_siang ?? 'NULL') . '</p>';
            echo '<p><strong>URL:</strong> ' . $attendance->picture_absen_siang_url . '</p>';
            echo '<img src="' . $attendance->picture_absen_siang_url . '" alt="Foto Absen Siang" onerror="this.src=\'/images/no-image.png\'">';
            echo '</div>';

            // Check Out Image
            echo '<div class="image-item">';
            echo '<h4>Foto Absen Sore</h4>';
            echo '<p><strong>Raw Path:</strong> ' . ($attendance->picture_absen_pulang ?? 'NULL') . '</p>';
            echo '<p><strong>URL:</strong> ' . $attendance->picture_absen_pulang_url . '</p>';
            echo '<img src="' . $attendance->picture_absen_pulang_url . '" alt="Foto Absen Sore" onerror="this.src=\'/images/no-image.png\'">';
            echo '</div>';

            echo '</div>'; // end image-grid
            echo '</div>'; // end card
        }
    }
    ?>

    <hr>
    <h3>Storage Information</h3>
    <p><strong>Storage public path:</strong> <?= Storage::disk('public')->path('') ?></p>
    <p><strong>Public storage link exists:</strong> <?= file_exists(public_path('storage')) ? 'YES' : 'NO' ?></p>
    <p><strong>No-image placeholder exists:</strong> <?= file_exists(public_path('images/no-image.png')) ? 'YES' : 'NO' ?></p>

    <hr>
    <p><strong>Direct Test URL:</strong></p>
    <p><a href="/images/no-image.png" target="_blank">Test no-image.png</a></p>

</body>
</html>
