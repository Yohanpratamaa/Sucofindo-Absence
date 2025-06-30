<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Absensi - {{ $employee->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #366092;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #366092;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 12px;
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .employee-info .row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }

        .employee-info .label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }

        .period-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #366092;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .text-left {
            text-align: left !important;
        }

        .status-hadir {
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
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .tipe-dinas-luar {
            background-color: #ffc107;
            color: black;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .overtime-badge {
            background-color: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .summary-stats {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .summary-stats h3 {
            margin: 0 0 10px 0;
            color: #366092;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            text-align: center;
        }

        .stat-item {
            padding: 8px;
            background-color: white;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #366092;
        }

        .stat-label {
            font-size: 10px;
            color: #6c757d;
            margin-top: 2px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            margin: 50px 0;
            color: #6c757d;
        }

        .no-data h3 {
            color: #dc3545;
            margin-bottom: 10px;
        }

        .page-break {
            page-break-after: always;
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
            <span>{{ $employee->npp ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Jabatan:</span>
            <span>{{ $employee->jabatan_nama ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Posisi:</span>
            <span>{{ $employee->posisi_nama ?? '-' }}</span>
        </div>
        <div class="row">
            <span class="label">Email:</span>
            <span>{{ $employee->email ?? '-' }}</span>
        </div>
    </div>

    <div class="period-info">
        <strong>Periode: {{ $startDate }} s/d {{ $endDate }}</strong><br>
        <span style="font-size: 10px;">Total Record: {{ $total_records }} data</span>
    </div>

    @if($attendances->count() > 0)
        @php
            $totalHadir = $attendances->whereNotNull('check_in')->count();
            $totalTerlambat = $attendances->where('check_in', '>', '08:00:00')->count();
            $totalTidakCheckout = $attendances->whereNull('check_out')->whereNotNull('check_in')->count();
            $totalOvertime = $attendances->where('overtime', '>', 0)->count();
        @endphp

        <div class="summary-stats">
            <h3>Ringkasan Kehadiran</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{{ $totalHadir }}</div>
                    <div class="stat-label">Total Hadir</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $totalTerlambat }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $totalTidakCheckout }}</div>
                    <div class="stat-label">Tidak Checkout</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $totalOvertime }}</div>
                    <div class="stat-label">Lembur</div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 10%;">Check In</th>
                    <th style="width: 10%;">Check Out</th>
                    <th style="width: 12%;">Durasi Kerja</th>
                    <th style="width: 8%;">Lembur</th>
                    <th style="width: 10%;">Tipe</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 18%;">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                    @php
                        // Calculate work duration
                        $workDuration = '-';
                        if ($attendance->check_in && $attendance->check_out) {
                            $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                            $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                            $duration = $checkIn->diff($checkOut);
                            $workDuration = sprintf('%02d:%02d', $duration->h, $duration->i);
                        }

                        // Determine status
                        $status = 'Tidak Hadir';
                        $statusClass = 'status-tidak-hadir';
                        if ($attendance->check_in) {
                            if (\Carbon\Carbon::parse($attendance->check_in)->format('H:i') <= '08:00') {
                                $status = 'Tepat Waktu';
                                $statusClass = 'status-hadir';
                            } else {
                                $status = 'Terlambat';
                                $statusClass = 'status-terlambat';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                        <td>{{ $workDuration }}</td>
                        <td>
                            @if($attendance->overtime > 0)
                                <span class="overtime-badge">{{ $attendance->overtime }} min</span>
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
            <p>Karyawan <strong>{{ $employee->nama }}</strong> belum melakukan absensi pada periode {{ $startDate }} - {{ $endDate }}</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Laporan Detail Absensi - {{ $employee->nama }}</strong></p>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        <p>Digenerate pada: {{ $generated_at }}</p>
        <p>PT. Sucofindo (Persero) - Sistem Manajemen Absensi</p>
    </div>
</body>
</html>
