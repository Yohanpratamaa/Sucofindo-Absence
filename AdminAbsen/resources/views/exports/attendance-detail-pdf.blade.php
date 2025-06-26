<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #366092;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #366092;
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #366092;
        }

        .employee-info .row {
            display: flex;
            margin-bottom: 5px;
        }

        .employee-info .label {
            font-weight: bold;
            width: 120px;
            color: #366092;
        }

        .period-info {
            text-align: center;
            margin-bottom: 20px;
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 5px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #366092;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #366092;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
        }

        .text-left {
            text-align: left !important;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-tepat-waktu {
            color: #28a745;
            font-weight: bold;
        }

        .status-terlambat {
            color: #ffc107;
            font-weight: bold;
        }

        .status-tidak-hadir {
            color: #dc3545;
            font-weight: bold;
        }

        .tipe-wfo {
            background-color: #007bff;
            color: white;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }

        .tipe-dinas-luar {
            background-color: #ffc107;
            color: black;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }

        .overtime-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            color: #666;
            font-size: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            margin-top: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL ABSENSI KARYAWAN</h1>
        <h2>PT. Sucofindo (Persero)</h2>
    </div>

    <div class="company-info">
        <strong>Laporan Detail Absensi Individu</strong>
    </div>

    <div class="employee-info">
        <div class="row">
            <span class="label">Nama:</span>
            <span>{{ $employee->nama }}</span>
        </div>
        <div class="row">
            <span class="label">NPP:</span>
            <span>{{ $employee->npp }}</span>
        </div>
        <div class="row">
            <span class="label">Jabatan:</span>
            <span>{{ $employee->jabatan_nama ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Departemen:</span>
            <span>{{ $employee->departemen ?? '-' }}</span>
        </div>
    </div>

    <div class="period-info">
        <strong>Periode:</strong> {{ $startDate }} s/d {{ $endDate }}<br>
        <strong>Total Record:</strong> {{ $totalAttendance }} hari<br>
        <strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i:s') }}
    </div>

    @php
        $totalTerlambat = $attendances->where('check_in', '>', '08:00:00')->count();
        $totalTidakCheckout = $attendances->whereNull('check_out')->count();
        $totalOvertime = $attendances->sum('overtime');
        $avgWorkTime = $attendances->filter(function($attendance) {
            return $attendance->check_in && $attendance->check_out;
        })->average(function($attendance) {
            return \Carbon\Carbon::parse($attendance->check_out)->diffInMinutes(\Carbon\Carbon::parse($attendance->check_in)) - 60;
        });
    @endphp

    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $totalAttendance }}</div>
            <div class="stat-label">Total Hadir</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $totalTerlambat }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $totalTidakCheckout }}</div>
            <div class="stat-label">Tidak Checkout</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ round($totalOvertime / 60, 1) }}j</div>
            <div class="stat-label">Total Lembur</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ round($avgWorkTime / 60, 1) }}j</div>
            <div class="stat-label">Rata-rata Kerja</div>
        </div>
    </div>

    @if($attendances->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 8%;">Hari</th>
                    <th style="width: 10%;">Check In</th>
                    <th style="width: 10%;">Check Out</th>
                    <th style="width: 10%;">Durasi Kerja</th>
                    <th style="width: 8%;">Lembur</th>
                    <th style="width: 12%;">Tipe Absensi</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                    @php
                        $date = \Carbon\Carbon::parse($attendance->created_at);
                        $checkIn = $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in) : null;
                        $checkOut = $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out) : null;

                        // Calculate work duration
                        $workDuration = '-';
                        if ($checkIn && $checkOut) {
                            $minutes = $checkOut->diffInMinutes($checkIn) - 60; // Minus 1 hour break
                            $hours = intval($minutes / 60);
                            $mins = $minutes % 60;
                            $workDuration = $hours . 'j ' . $mins . 'm';
                        }

                        // Format overtime
                        $overtimeFormatted = '-';
                        if ($attendance->overtime > 0) {
                            $hours = intval($attendance->overtime / 60);
                            $minutes = $attendance->overtime % 60;
                            $overtimeFormatted = $hours . 'j ' . $minutes . 'm';
                        }

                        // Determine status
                        $status = 'Tidak Hadir';
                        $statusClass = 'status-tidak-hadir';
                        if ($checkIn) {
                            if ($checkIn->format('H:i:s') <= '08:00:00') {
                                $status = 'Tepat Waktu';
                                $statusClass = 'status-tepat-waktu';
                            } else {
                                $status = 'Terlambat';
                                $statusClass = 'status-terlambat';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $date->format('d/m/Y') }}</td>
                        <td>{{ $date->locale('id')->dayName }}</td>
                        <td>{{ $checkIn ? $checkIn->format('H:i:s') : '-' }}</td>
                        <td>{{ $checkOut ? $checkOut->format('H:i:s') : '-' }}</td>
                        <td>{{ $workDuration }}</td>
                        <td>
                            @if($attendance->overtime > 0)
                                <span class="overtime-badge">{{ $overtimeFormatted }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($attendance->tipe_absensi == 'WFO')
                                <span class="tipe-wfo">WFO</span>
                            @elseif($attendance->tipe_absensi == 'Dinas Luar')
                                <span class="tipe-dinas-luar">DINAS LUAR</span>
                            @else
                                {{ $attendance->tipe_absensi ?? 'WFO' }}
                            @endif
                        </td>
                        <td class="{{ $statusClass }}">{{ $status }}</td>
                        <td class="text-left">{{ $attendance->lokasi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak ada data absensi untuk periode yang dipilih</h3>
            <p>Karyawan belum melakukan absensi pada periode {{ $startDate }} - {{ $endDate }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Sucofindo Absen<br>
        Dicetak pada: {{ date('d/m/Y H:i:s') }} | Karyawan: {{ $employee->nama }}</p>
    </div>
</body>
</html>
