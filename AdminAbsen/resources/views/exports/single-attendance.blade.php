<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi - {{ $attendance->user->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }

        .company-logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 12px;
            color: #666;
        }

        .employee-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
            display: inline-block;
            width: 120px;
        }

        .info-value {
            color: #1f2937;
        }

        .attendance-details {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #d1d5db;
        }

        .time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .time-card {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px;
            background-color: #ffffff;
        }

        .time-card h4 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #374151;
        }

        .time-card.check-in h4 {
            color: #059669;
        }

        .time-card.absen-siang h4 {
            color: #d97706;
        }

        .time-card.check-out h4 {
            color: #2563eb;
        }

        .time-value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .time-status {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .status-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-gray {
            background-color: #f3f4f6;
            color: #374151;
        }

        .location-section {
            margin-bottom: 25px;
        }

        .location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        .location-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            background-color: #fafafa;
        }

        .location-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            color: #374151;
        }

        .coordinate {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #4b5563;
            margin-bottom: 2px;
        }

        .summary-section {
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .summary-item {
            margin-bottom: 10px;
        }

        .summary-label {
            font-weight: bold;
            color: #475569;
            display: block;
            margin-bottom: 3px;
        }

        .summary-value {
            color: #1e293b;
            font-size: 13px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-wfo {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-dinas {
            background-color: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }

        .requirements-section {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 6px;
        }

        .requirements-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
        }

        .requirements-text {
            font-size: 11px;
            color: #78350f;
            line-height: 1.5;
        }

        @media print {
            body {
                margin: 0;
                padding: 20px;
            }

            .time-grid,
            .location-grid,
            .summary-grid,
            .info-grid {
                display: block;
            }

            .time-card,
            .location-card {
                margin-bottom: 10px;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-logo">SUCOFINDO</div>
        <div class="report-title">LAPORAN DATA ABSENSI KARYAWAN</div>
        <div class="report-subtitle">Sistem Informasi Absensi Digital</div>
    </div>

    <!-- Employee Information -->
    <div class="employee-info">
        <div class="section-title">👤 Informasi Karyawan</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Nama Lengkap:</span>
                    <span class="info-value">{{ $attendance->user->nama }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">NPP:</span>
                    <span class="info-value">{{ $attendance->user->npp }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $attendance->user->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jabatan:</span>
                    <span class="info-value">{{ $attendance->user->jabatan ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Tanggal Absensi:</span>
                    <span class="info-value">{{ $attendance->created_at->format('d F Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipe Absensi:</span>
                    <span class="badge {{ $attendance->attendance_type === 'WFO' ? 'badge-wfo' : 'badge-dinas' }}">
                        {{ $attendance->attendance_type }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Kehadiran:</span>
                    <span class="info-value">{{ $attendance->status_kehadiran }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Absensi:</span>
                    <span class="info-value">#{{ $attendance->id }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements Section -->
    @if($attendance->absensi_requirement)
    <div class="requirements-section">
        <div class="requirements-title">📋 Requirement Absensi</div>
        <div class="requirements-text">{{ $attendance->absensi_requirement }}</div>
    </div>
    @endif

    <!-- Attendance Details -->
    <div class="attendance-details">
        <div class="section-title">⏰ Detail Waktu Absensi</div>
        <div class="time-grid">
            <!-- Check In -->
            <div class="time-card check-in">
                <h4>🌅 Check In</h4>
                <div class="time-value">
                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : 'Belum Check In' }}
                </div>
                @if($attendance->check_in)
                    @php
                        $deadline = \Carbon\Carbon::parse($attendance->jam_masuk_standar ?? '08:00:00');
                        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                        $isLate = $checkInTime->gt($deadline);
                    @endphp
                    <span class="time-status {{ $isLate ? 'status-danger' : 'status-success' }}">
                        {{ $isLate ? 'Terlambat' : 'Tepat Waktu' }}
                    </span>
                    <div style="margin-top: 5px; font-size: 10px; color: #6b7280;">
                        Standar: {{ $attendance->jam_masuk_standar ?? '08:00:00' }}
                    </div>
                @else
                    <span class="time-status status-gray">Belum Dilakukan</span>
                @endif
            </div>

            <!-- Absen Siang -->
            <div class="time-card absen-siang">
                <h4>☀️ Absen Siang</h4>
                @if($attendance->attendance_type === 'WFO')
                    <div class="time-value">Tidak Diperlukan</div>
                    <span class="time-status status-gray">WFO</span>
                @else
                    <div class="time-value">
                        {{ $attendance->absen_siang ? \Carbon\Carbon::parse($attendance->absen_siang)->format('H:i:s') : 'Belum Absen' }}
                    </div>
                    <span class="time-status {{ $attendance->absen_siang ? 'status-warning' : 'status-danger' }}">
                        {{ $attendance->absen_siang ? 'Selesai' : 'Belum Dilakukan' }}
                    </span>
                @endif
            </div>

            <!-- Check Out -->
            <div class="time-card check-out">
                <h4>🌆 Check Out</h4>
                <div class="time-value">
                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : 'Belum Check Out' }}
                </div>
                @if($attendance->check_out)
                    @php
                        $standardOut = \Carbon\Carbon::parse($attendance->jam_keluar_standar ?? '17:00:00');
                        $checkOutTime = \Carbon\Carbon::parse($attendance->check_out);
                        $isEarly = $checkOutTime->lt($standardOut);
                    @endphp
                    <span class="time-status {{ $isEarly ? 'status-warning' : 'status-success' }}">
                        {{ $isEarly ? 'Pulang Cepat' : 'Normal' }}
                    </span>
                    <div style="margin-top: 5px; font-size: 10px; color: #6b7280;">
                        Standar: {{ $attendance->jam_keluar_standar ?? '17:00:00' }}
                    </div>
                @else
                    <span class="time-status status-gray">Belum Dilakukan</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Location Information -->
    <div class="location-section">
        <div class="section-title">📍 Informasi Lokasi</div>
        <div class="location-grid">
            <!-- Check In Location -->
            <div class="location-card">
                <div class="location-title">Lokasi Check In</div>
                @if($attendance->latitude_absen_masuk && $attendance->longitude_absen_masuk)
                    <div class="coordinate">Lat: {{ number_format($attendance->latitude_absen_masuk, 6) }}</div>
                    <div class="coordinate">Lng: {{ number_format($attendance->longitude_absen_masuk, 6) }}</div>
                    @if($attendance->attendance_type === 'WFO' && isset($attendance->officeSchedule->office))
                        @php
                            $office = $attendance->officeSchedule->office;
                            $distance = calculateDistance(
                                $office->latitude,
                                $office->longitude,
                                $attendance->latitude_absen_masuk,
                                $attendance->longitude_absen_masuk
                            );
                        @endphp
                        <div style="margin-top: 5px; font-size: 10px;">
                            <strong>Jarak dari kantor: {{ $distance }}m</strong>
                        </div>
                    @endif
                @else
                    <div style="color: #9ca3af; font-size: 10px;">Lokasi tidak tersedia</div>
                @endif
            </div>

            <!-- Absen Siang Location -->
            <div class="location-card">
                <div class="location-title">Lokasi Absen Siang</div>
                @if($attendance->attendance_type === 'WFO')
                    <div style="color: #9ca3af; font-size: 10px;">Tidak diperlukan untuk WFO</div>
                @elseif($attendance->latitude_absen_siang && $attendance->longitude_absen_siang)
                    <div class="coordinate">Lat: {{ number_format($attendance->latitude_absen_siang, 6) }}</div>
                    <div class="coordinate">Lng: {{ number_format($attendance->longitude_absen_siang, 6) }}</div>
                    <div style="margin-top: 5px; font-size: 10px;">
                        <strong>Lokasi Dinas Luar</strong>
                    </div>
                @else
                    <div style="color: #9ca3af; font-size: 10px;">Lokasi tidak tersedia</div>
                @endif
            </div>

            <!-- Check Out Location -->
            <div class="location-card">
                <div class="location-title">Lokasi Check Out</div>
                @if($attendance->latitude_absen_pulang && $attendance->longitude_absen_pulang)
                    <div class="coordinate">Lat: {{ number_format($attendance->latitude_absen_pulang, 6) }}</div>
                    <div class="coordinate">Lng: {{ number_format($attendance->longitude_absen_pulang, 6) }}</div>
                    @if($attendance->attendance_type === 'WFO' && isset($attendance->officeSchedule->office))
                        @php
                            $office = $attendance->officeSchedule->office;
                            $distance = calculateDistance(
                                $office->latitude,
                                $office->longitude,
                                $attendance->latitude_absen_pulang,
                                $attendance->longitude_absen_pulang
                            );
                        @endphp
                        <div style="margin-top: 5px; font-size: 10px;">
                            <strong>Jarak dari kantor: {{ $distance }}m</strong>
                        </div>
                    @endif
                @else
                    <div style="color: #9ca3af; font-size: 10px;">Lokasi tidak tersedia</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Information -->
    <div class="summary-section">
        <div class="section-title">📊 Ringkasan Absensi</div>
        <div class="summary-grid">
            <div>
                <div class="summary-item">
                    <span class="summary-label">Durasi Kerja:</span>
                    <span class="summary-value">{{ $attendance->durasi_kerja ?? 'Belum selesai' }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Overtime:</span>
                    <span class="summary-value">{{ $attendance->overtime_formatted ?? 'Tidak ada' }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Detail Keterlambatan:</span>
                    <span class="summary-value">{{ $attendance->keterlambatan_detail ?? 'Tidak ada' }}</span>
                </div>
            </div>
            <div>
                <div class="summary-item">
                    <span class="summary-label">Kelengkapan Absensi:</span>
                    @php $kelengkapan = $attendance->kelengkapan_absensi; @endphp
                    <span class="summary-value">{{ $kelengkapan['completed'] }}/{{ $kelengkapan['total'] }} - {{ $kelengkapan['status'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Foto Tersedia:</span>
                    <span class="summary-value">
                        @php
                            $photos = [];
                            if($attendance->picture_absen_masuk) $photos[] = 'Check In';
                            if($attendance->picture_absen_siang && $attendance->attendance_type === 'Dinas Luar') $photos[] = 'Siang';
                            if($attendance->picture_absen_pulang) $photos[] = 'Check Out';
                        @endphp
                        {{ count($photos) > 0 ? implode(', ', $photos) : 'Tidak ada' }}
                    </span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Jadwal ID:</span>
                    <span class="summary-value">{{ $attendance->office_working_hours_id ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh Sistem Absensi SUCOFINDO</p>
        <p>Tanggal cetak: {{ now()->format('d F Y H:i:s') }} WIB</p>
    </div>
</body>
</html>

@php
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
        return 0;
    }

    $earthRadius = 6371000; // Earth radius in meters

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return round($earthRadius * $c);
}
@endphp
