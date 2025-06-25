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
            font-size: 10px;
        }

        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
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
            grid-template-columns: 1fr 1fr 1fr;
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

        .tingkat-kehadiran-tinggi {
            color: #16a085;
            font-weight: bold;
        }

        .tingkat-kehadiran-sedang {
            color: #f39c12;
            font-weight: bold;
        }

        .tingkat-kehadiran-rendah {
            color: #e74c3c;
            font-weight: bold;
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
        Rekap Absensi Karyawan
    </div>

    @if(!empty($summary))
    <div class="summary">
        <h3>Ringkasan Rekap</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <span class="summary-label">Total Karyawan:</span>
                <span class="summary-value">{{ $summary['total_employees'] }} orang</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Periode:</span>
                <span class="summary-value">{{ $summary['work_days'] }} hari kerja</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Rata-rata Kehadiran:</span>
                <span class="summary-value">{{ $summary['avg_attendance'] }}%</span>
            </div>
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
                            @if($key == 'tingkat_kehadiran')
                                @php
                                    $percentage = (float) str_replace('%', '', $cell);
                                @endphp
                                @if($percentage >= 90) tingkat-kehadiran-tinggi
                                @elseif($percentage >= 75) tingkat-kehadiran-sedang
                                @else tingkat-kehadiran-rendah
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
                        Tidak ada data karyawan untuk periode yang dipilih
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
