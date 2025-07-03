<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajuan Lembur</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 landscape;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #8B5A2B;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #8B5A2B;
            font-size: 18px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: normal;
        }

        .header .period {
            color: #888;
            font-size: 11px;
            margin: 5px 0 0 0;
        }

        .summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #8B5A2B;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }

        th {
            background-color: #8B5A2B;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #5D4037;
        }

        td {
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #d1d5db;
            font-size: 8px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 60px;
        }

        .status-assigned {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .col-date { width: 10%; }
        .col-name { width: 12%; }
        .col-npp { width: 6%; }
        .col-jabatan { width: 10%; }
        .col-overtime-id { width: 10%; }
        .col-keterangan { width: 15%; word-wrap: break-word; word-break: break-word; }
        .col-assigned-by { width: 10%; }
        .col-status { width: 8%; }
        .col-approval { width: 19%; word-wrap: break-word; word-break: break-word; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT. SUCOFINDO</h1>
        <h2>Laporan Pengajuan Lembur</h2>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Lembur</div>
            <div class="value">{{ $total_overtime }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Ditugaskan</div>
            <div class="value">{{ $assigned }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Diterima</div>
            <div class="value">{{ $accepted }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Ditolak</div>
            <div class="value">{{ $rejected }}</div>
        </div>
    </div>

    @if($data->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="col-date">Tgl Penugasan</th>
                        <th class="col-name">Nama Pegawai</th>
                        <th class="col-npp">NPP</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th class="col-overtime-id">ID Lembur</th>
                        <th class="col-keterangan">Keterangan</th>
                        <th class="col-assigned-by">Ditugaskan Oleh</th>
                        <th class="col-status">Status</th>
                        <th class="col-approval">Info Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $overtime)
                        <tr>
                            <td class="text-center">
                                {{ $overtime->assigned_at->format('d/m/Y') }}<br>
                                <small style="color: #666;">{{ $overtime->assigned_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $overtime->user->nama ?? '-' }}</strong>
                            </td>
                            <td class="text-center">{{ $overtime->user->npp ?? '-' }}</td>
                            <td>{{ $overtime->user->jabatan_nama ?? '-' }}</td>
                            <td>{{ $overtime->overtime_id ?? '-' }}</td>
                            <td style="word-wrap: break-word; word-break: break-word; max-width: 120px;">{{ $overtime->keterangan ?? '-' }}</td>
                            <td>{{ $overtime->assignedBy->nama ?? '-' }}</td>
                            <td class="text-center">
                                @php
                                    $status = match ($overtime->status) {
                                        'Assigned' => ['label' => 'Ditugaskan', 'class' => 'status-assigned'],
                                        'Accepted' => ['label' => 'Diterima', 'class' => 'status-accepted'],
                                        'Rejected' => ['label' => 'Ditolak', 'class' => 'status-rejected'],
                                        default => ['label' => ucfirst($overtime->status), 'class' => 'status-assigned'],
                                    };
                                @endphp
                                <span class="status-badge {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td style="word-wrap: break-word; word-break: break-word; max-width: 150px;">
                                @if($overtime->approved_at)
                                    <strong>Tanggal:</strong> {{ $overtime->approved_at->format('d/m/Y H:i') }}<br>
                                @endif
                                @if($overtime->approvedBy)
                                    <strong>Oleh:</strong> {{ $overtime->approvedBy->nama }}<br>
                                @endif
                                @if($overtime->approval_info)
                                    <strong>Info:</strong> {{ $overtime->approval_info }}
                                @endif
                                @if(!$overtime->approved_at && !$overtime->approvedBy && !$overtime->approval_info)
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <p>Tidak ada data lembur yang ditemukan untuk periode yang dipilih.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh Sistem Manajemen Absensi PT. Sucofindo</p>
        <p>Tanggal cetak: {{ $generated_at }} | Total data: {{ $data->count() }} lembur</p>
    </div>
</body>
</html>
