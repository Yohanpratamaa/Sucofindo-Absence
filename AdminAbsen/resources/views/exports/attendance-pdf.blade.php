<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $period }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .company-logo {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 12px;
            color: #666;
        }

        .summary-section {
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            text-align: center;
        }

        .summary-item {
            padding: 8px;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }

        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            display: block;
        }

        .summary-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        .table-container {
            width: 100%;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #d1d5db;
            font-size: 8px;
        }

        td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .status-tepat-waktu {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .status-terlambat {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .status-tidak-hadir {
            background-color: #f3f4f6;
            color: #374151;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .type-wfo {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .type-dinas {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 8px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }

        .employee-filter {
            background-color: #eff6ff;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #3b82f6;
        }

        .employee-filter-label {
            font-weight: bold;
            color: #1e40af;
            font-size: 10px;
        }

        /* Column widths */
        .col-tanggal { width: 8%; }
        .col-nama { width: 15%; }
        .col-npp { width: 10%; }
        .col-jabatan { width: 12%; }
        .col-checkin { width: 8%; }
        .col-checkout { width: 8%; }
        .col-durasi { width: 12%; }
        .col-status { width: 12%; }
        .col-tipe { width: 10%; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-logo">SUCOFINDO</div>
        <div class="report-title">{{ $title }}</div>
        <div class="report-period">Periode: {{ $period }}</div>
    </div>

    <!-- Employee Filter -->
    @if(isset($summary['employee_name']) && $summary['employee_name'])
    <div class="employee-filter">
        <span class="employee-filter-label">📋 Filter Karyawan: {{ $summary['employee_name'] }}</span>
    </div>
    @endif

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-number">{{ $summary['total_records'] }}</span>
                <div class="summary-label">Total Records</div>
            </div>
            <div class="summary-item">
                <span class="summary-number">{{ $summary['work_days'] }}</span>
                <div class="summary-label">Hari Kerja</div>
            </div>
            <div class="summary-item">
                <span class="summary-number">{{ $summary['total_attendance'] }}</span>
                <div class="summary-label">Total Absensi</div>
            </div>
            <div class="summary-item">
                <span class="summary-number">{{ number_format(($summary['total_attendance'] / max($summary['work_days'], 1)) * 100, 1) }}%</span>
                <div class="summary-label">Tingkat Kehadiran</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container">
        @if(count($data) > 0)
        <table>
            <thead>
                <tr>
                    @foreach($headers as $index => $header)
                    <th class="{{ $index == 0 ? 'col-tanggal' : ($index == 1 ? 'col-nama' : ($index == 2 ? 'col-npp' : ($index == 3 ? 'col-jabatan' : ($index == 4 ? 'col-checkin' : ($index == 5 ? 'col-checkout' : ($index == 6 ? 'col-durasi' : ($index == 7 ? 'col-status' : 'col-tipe'))))))) }}">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td class="col-tanggal">{{ $row['tanggal'] }}</td>
                    <td class="col-nama">{{ $row['nama_karyawan'] }}</td>
                    <td class="col-npp">{{ $row['npp'] }}</td>
                    <td class="col-jabatan">{{ $row['jabatan'] }}</td>
                    <td class="col-checkin">{{ $row['check_in'] }}</td>
                    <td class="col-checkout">{{ $row['check_out'] }}</td>
                    <td class="col-durasi">{{ $row['durasi_kerja'] }}</td>
                    <td class="col-status">
                        <span class="{{ $row['status_kehadiran'] == 'Tepat Waktu' ? 'status-tepat-waktu' : ($row['status_kehadiran'] == 'Terlambat' ? 'status-terlambat' : 'status-tidak-hadir') }}">
                            {{ $row['status_kehadiran'] }}
                        </span>
                    </td>
                    <td class="col-tipe">
                        <span class="{{ $row['attendance_type'] == 'WFO' ? 'type-wfo' : 'type-dinas' }}">
                            {{ $row['attendance_type'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-data">
            <p>📋 Tidak ada data absensi untuk periode yang dipilih</p>
            <p>Periode: {{ $period }}</p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh Sistem Absensi SUCOFINDO</p>
        <p>Tanggal cetak: {{ now()->format('d F Y H:i:s') }} WIB</p>
        @if(count($data) > 0)
        <p>Total {{ count($data) }} record(s) ditampilkan</p>
        @endif
    </div>
</body>
</html>
            font-weight: bold;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status-tepat-waktu {
            color: #16a085;
            font-weight: bold;
        }

        .status-terlambat {
            color: #f39c12;
            font-weight: bold;
        }

        .status-tidak-hadir {
            color: #e74c3c;
            font-weight: bold;
        }

        .tipe-wfo {
            color: #3498db;
            font-weight: bold;
        }

        .tipe-dinas-luar {
            color: #f39c12;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            color: #666;
            font-size: 10px;
        }

        .summary {
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #366092;
        }

        .summary h3 {
            margin: 0 0 10px 0;
            color: #366092;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .summary-item {
            padding: 5px 0;
        }

        .summary-label {
            font-weight: bold;
            color: #333;
        }

        .summary-value {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>{{ $period }}</h2>
    </div>

    <div class="company-info">
        <strong>PT. Sucofindo (Persero)</strong><br>
        Sistem Manajemen Absensi Karyawan
    </div>

    @if(!empty($summary))
    <div class="summary">
        <h3>Ringkasan Laporan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Data:</span>
                <span class="summary-value">{{ $summary['total_records'] }} records</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Periode:</span>
                <span class="summary-value">{{ $summary['work_days'] }} hari kerja</span>
            </div>
            @if(isset($summary['employee_name']))
            <div class="summary-item">
                <span class="summary-label">Karyawan:</span>
                <span class="summary-value">{{ $summary['employee_name'] }}</span>
            </div>
            @endif
            @if(isset($summary['total_attendance']))
            <div class="summary-item">
                <span class="summary-label">Total Kehadiran:</span>
                <span class="summary-value">{{ $summary['total_attendance'] }} hari</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @foreach($row as $key => $cell)
                        <td class="
                            @if($key == 'status_kehadiran')
                                @if($cell == 'Tepat Waktu') status-tepat-waktu
                                @elseif($cell == 'Terlambat') status-terlambat
                                @elseif($cell == 'Tidak Hadir') status-tidak-hadir
                                @endif
                            @elseif($key == 'attendance_type')
                                @if($cell == 'WFO') tipe-wfo
                                @elseif($cell == 'Dinas Luar') tipe-dinas-luar
                                @endif
                            @endif
                        ">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align: center; color: #999; font-style: italic;">
                        Tidak ada data absensi untuk periode yang dipilih
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
        <p>Halaman {{ $currentPage ?? 1 }}</p>
    </div>
</body>
</html>
