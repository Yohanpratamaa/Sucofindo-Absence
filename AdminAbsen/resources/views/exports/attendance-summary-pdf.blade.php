<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Tim</title>
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

        .period-info {
            text-align: center;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #366092;
        }

        .stat-label {
            font-size: 12px;
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
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
        }

        .text-left {
            text-align: left !important;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP ABSENSI TIM</h1>
        <h2>PT. Sucofindo (Persero)</h2>
    </div>

    <div class="company-info">
        <strong>Laporan Absensi Karyawan</strong>
    </div>

    <div class="period-info">
        <strong>Periode:</strong> {{ $startDate }} s/d {{ $endDate }}<br>
        <strong>Total Karyawan:</strong> {{ $totalEmployees }} orang<br>
        <strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i:s') }}
    </div>

    @php
        $totalHadir = $attendanceData->sum('total_hadir');
        $totalTerlambat = $attendanceData->sum('total_terlambat');
        $totalTidakCheckout = $attendanceData->sum('total_tidak_checkout');
        $totalOvertimeMinutes = $attendanceData->sum('total_overtime_minutes');
    @endphp

    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $totalHadir }}</div>
            <div class="stat-label">Total Kehadiran</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $totalTerlambat }}</div>
            <div class="stat-label">Total Terlambat</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $totalTidakCheckout }}</div>
            <div class="stat-label">Tidak Checkout</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ round($totalOvertimeMinutes / 60, 1) }}j</div>
            <div class="stat-label">Total Lembur</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NPP</th>
                <th style="width: 25%;">Nama Karyawan</th>
                <th style="width: 15%;">Jabatan</th>
                <th style="width: 8%;">Hadir</th>
                <th style="width: 8%;">Terlambat</th>
                <th style="width: 8%;">T. Checkout</th>
                <th style="width: 8%;">Lembur</th>
                <th style="width: 8%;">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $index => $employee)
                @php
                    // Calculate attendance percentage
                    $workDays = 22; // Default work days in a month
                    $attendancePercentage = $workDays > 0 ? round(($employee->total_hadir / $workDays) * 100, 1) : 0;

                    // Format overtime
                    $overtimeFormatted = '-';
                    if ($employee->total_overtime_minutes > 0) {
                        $hours = intval($employee->total_overtime_minutes / 60);
                        $minutes = $employee->total_overtime_minutes % 60;
                        $overtimeFormatted = $hours . 'j ' . $minutes . 'm';
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $employee->npp }}</td>
                    <td class="text-left">{{ $employee->nama }}</td>
                    <td class="text-left">{{ $employee->jabatan_nama ?? '-' }}</td>
                    <td>
                        <span class="badge-success">{{ $employee->total_hadir ?? 0 }}</span>
                    </td>
                    <td>
                        @if($employee->total_terlambat > 0)
                            <span class="badge-warning">{{ $employee->total_terlambat }}</span>
                        @else
                            <span class="badge-success">0</span>
                        @endif
                    </td>
                    <td>
                        @if($employee->total_tidak_checkout > 0)
                            <span class="badge-danger">{{ $employee->total_tidak_checkout }}</span>
                        @else
                            <span class="badge-success">0</span>
                        @endif
                    </td>
                    <td>{{ $overtimeFormatted }}</td>
                    <td>
                        @if($attendancePercentage >= 90)
                            <span class="badge-success">{{ $attendancePercentage }}%</span>
                        @elseif($attendancePercentage >= 75)
                            <span class="badge-warning">{{ $attendancePercentage }}%</span>
                        @else
                            <span class="badge-danger">{{ $attendancePercentage }}%</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($attendanceData->isEmpty())
        <div style="text-align: center; margin-top: 50px; color: #666;">
            <h3>Tidak ada data absensi untuk periode yang dipilih</h3>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Sucofindo Absen<br>
        Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
