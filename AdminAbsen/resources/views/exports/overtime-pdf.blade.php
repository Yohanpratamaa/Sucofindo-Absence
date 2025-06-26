<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Lembur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        .period {
            font-size: 12px;
            color: #888;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 9px;
            color: #333;
        }
        td {
            font-size: 8px;
        }
        .status-assigned {
            color: #f59e0b;
            font-weight: bold;
        }
        .status-accepted {
            color: #10b981;
            font-weight: bold;
        }
        .status-rejected {
            color: #ef4444;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT. Sucofindo</h1>
        <h2>Laporan Data Penugasan Lembur</h2>
        <div class="period">Periode: {{ $startDate }} - {{ $endDate }}</div>
        <div class="period">Total Data: {{ $overtimes->count() }} penugasan</div>
    </div>

    @if($overtimes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">Tgl Penugasan</th>
                    <th style="width: 15%">Nama Karyawan</th>
                    <th style="width: 8%">NPP</th>
                    <th style="width: 10%">ID Lembur</th>
                    <th style="width: 15%">Ditugaskan Oleh</th>
                    <th style="width: 8%">Status</th>
                    <th style="width: 10%">Durasi</th>
                    <th style="width: 12%">Disetujui Oleh</th>
                    <th style="width: 12%">Tgl Disetujui</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overtimes as $overtime)
                <tr>
                    <td>{{ $overtime->assigned_at ? $overtime->assigned_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $overtime->user->nama ?? '-' }}</td>
                    <td>{{ $overtime->user->npp ?? '-' }}</td>
                    <td>{{ $overtime->overtime_id ?? '-' }}</td>
                    <td>{{ $overtime->assignedBy->nama ?? '-' }}</td>
                    <td class="status-{{ strtolower(str_replace(' ', '-', $overtime->status)) }}">{{ $overtime->status_badge['label'] }}</td>
                    <td>{{ $overtime->durasi_assignment ?? '-' }}</td>
                    <td>{{ $overtime->approvedBy->nama ?? '-' }}</td>
                    <td>{{ $overtime->approved_at ? $overtime->approved_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Ringkasan Data</h3>
            <div class="summary-item">
                <strong>Ditugaskan:</strong> {{ $overtimes->where('status', 'Assigned')->count() }}
            </div>
            <div class="summary-item">
                <strong>Diterima:</strong> {{ $overtimes->where('status', 'Accepted')->count() }}
            </div>
            <div class="summary-item">
                <strong>Ditolak:</strong> {{ $overtimes->where('status', 'Rejected')->count() }}
            </div>
            <br><br>
            <div class="summary-item">
                <strong>Total Penugasan:</strong> {{ $overtimes->count() }}
            </div>
            <div class="summary-item">
                <strong>Unique Karyawan:</strong> {{ $overtimes->pluck('user_id')->unique()->count() }}
            </div>
            <div class="summary-item">
                <strong>Unique Assigner:</strong> {{ $overtimes->pluck('assigned_by')->unique()->count() }}
            </div>
        </div>
    @else
        <p style="text-align: center; margin-top: 50px; font-size: 14px; color: #666;">
            Tidak ada data penugasan lembur dalam periode yang dipilih.
        </p>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>PT. Sucofindo - Sistem Manajemen Absensi</p>
    </div>
</body>
</html>
