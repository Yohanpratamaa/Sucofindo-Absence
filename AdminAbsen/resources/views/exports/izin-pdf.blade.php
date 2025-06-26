<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Izin</title>
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
        .status-pending {
            color: #f59e0b;
            font-weight: bold;
        }
        .status-approved {
            color: #10b981;
            font-weight: bold;
        }
        .status-rejected {
            color: #ef4444;
            font-weight: bold;
        }
        .jenis-sakit {
            color: #f59e0b;
        }
        .jenis-cuti {
            color: #3b82f6;
        }
        .jenis-izin {
            color: #06b6d4;
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
        <h2>Laporan Data Izin Karyawan</h2>
        <div class="period">Periode: {{ $startDate }} - {{ $endDate }}</div>
        <div class="period">Total Data: {{ $izins->count() }} izin</div>
    </div>

    @if($izins->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Tgl Pengajuan</th>
                    <th style="width: 15%">Nama Karyawan</th>
                    <th style="width: 8%">NPP</th>
                    <th style="width: 10%">Jenis Izin</th>
                    <th style="width: 8%">Tgl Mulai</th>
                    <th style="width: 8%">Tgl Akhir</th>
                    <th style="width: 6%">Durasi</th>
                    <th style="width: 20%">Keterangan</th>
                    <th style="width: 8%">Status</th>
                    <th style="width: 9%">Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($izins as $izin)
                <tr>
                    <td>{{ $izin->created_at ? $izin->created_at->format('d/m/Y') : '-' }}</td>
                    <td>{{ $izin->user->nama ?? '-' }}</td>
                    <td>{{ $izin->user->npp ?? '-' }}</td>
                    <td class="jenis-{{ $izin->jenis_izin }}">{{ ucfirst($izin->jenis_izin) }}</td>
                    <td>{{ $izin->tanggal_mulai ? \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $izin->tanggal_akhir ? \Carbon\Carbon::parse($izin->tanggal_akhir)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $izin->durasi_hari }} hari</td>
                    <td>{{ $izin->keterangan ?? '-' }}</td>
                    <td class="status-{{ $izin->status }}">{{ $izin->status_badge['label'] }}</td>
                    <td>{{ $izin->approvedBy->nama ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Ringkasan Data</h3>
            <div class="summary-item">
                <strong>Total Sakit:</strong> {{ $izins->where('jenis_izin', 'sakit')->count() }}
            </div>
            <div class="summary-item">
                <strong>Total Cuti:</strong> {{ $izins->where('jenis_izin', 'cuti')->count() }}
            </div>
            <div class="summary-item">
                <strong>Total Izin Khusus:</strong> {{ $izins->where('jenis_izin', 'izin')->count() }}
            </div>
            <br><br>
            <div class="summary-item">
                <strong>Menunggu:</strong> {{ $izins->where('status', 'pending')->count() }}
            </div>
            <div class="summary-item">
                <strong>Disetujui:</strong> {{ $izins->where('status', 'approved')->count() }}
            </div>
            <div class="summary-item">
                <strong>Ditolak:</strong> {{ $izins->where('status', 'rejected')->count() }}
            </div>
        </div>
    @else
        <p style="text-align: center; margin-top: 50px; font-size: 14px; color: #666;">
            Tidak ada data izin dalam periode yang dipilih.
        </p>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>PT. Sucofindo - Sistem Manajemen Absensi</p>
    </div>
</body>
</html>
