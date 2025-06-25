<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #366092;
            color: white;
            padding: 10px 5px;
            text-align: left;
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
