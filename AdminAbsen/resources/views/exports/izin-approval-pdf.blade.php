<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Persetujuan Izin Tim</title>
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
            border-bottom: 2px solid #2563EB;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #2563EB;
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
            color: #2563EB;
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
            background-color: #2563EB;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #1e40af;
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

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .jenis-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }

        .jenis-sakit {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .jenis-cuti {
            background-color: #d1fae5;
            color: #065f46;
        }

        .jenis-izin {
            background-color: #fef3c7;
            color: #92400e;
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
        .col-npp { width: 8%; }
        .col-jabatan { width: 10%; }
        .col-jenis { width: 8%; }
        .col-tanggal { width: 8%; }
        .col-durasi { width: 6%; }
        .col-keterangan { width: 20%; word-wrap: break-word; word-break: break-word; }
        .col-status { width: 10%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PT. SUCOFINDO</h1>
        <h2>Laporan Persetujuan Izin Tim</h2>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Izin</div>
            <div class="value">{{ $total_izin }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Menunggu</div>
            <div class="value">{{ $pending }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Disetujui</div>
            <div class="value">{{ $approved }}</div>
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
                        <th class="col-date">Tgl Pengajuan</th>
                        <th class="col-name">Nama Pegawai</th>
                        <th class="col-npp">NPP</th>
                        <th class="col-jabatan">Jabatan</th>
                        <th class="col-jenis">Jenis</th>
                        <th class="col-tanggal">Tgl Mulai</th>
                        <th class="col-tanggal">Tgl Akhir</th>
                        <th class="col-durasi">Durasi</th>
                        <th class="col-keterangan">Keterangan</th>
                        <th class="col-status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $izin)
                        <tr>
                            <td class="text-center">
                                {{ $izin->created_at->format('d/m/Y') }}<br>
                                <small style="color: #666;">{{ $izin->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $izin->user->nama ?? '-' }}</strong>
                            </td>
                            <td class="text-center">{{ $izin->user->npp ?? '-' }}</td>
                            <td>{{ $izin->user->jabatan_nama ?? '-' }}</td>
                            <td class="text-center">
                                @php
                                    $jenisClass = match($izin->jenis_izin) {
                                        'sakit' => 'jenis-sakit',
                                        'cuti' => 'jenis-cuti',
                                        'izin' => 'jenis-izin',
                                        default => 'jenis-izin'
                                    };
                                @endphp
                                <span class="jenis-badge {{ $jenisClass }}">
                                    {{ ucfirst($izin->jenis_izin) }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ $izin->tanggal_mulai ? \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                {{ $izin->tanggal_akhir ? \Carbon\Carbon::parse($izin->tanggal_akhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($izin->tanggal_mulai && $izin->tanggal_akhir)
                                    @php
                                        $start = \Carbon\Carbon::parse($izin->tanggal_mulai);
                                        $end = \Carbon\Carbon::parse($izin->tanggal_akhir);
                                        $durasi = $start->diffInDays($end) + 1;
                                    @endphp
                                    {{ $durasi }} hari
                                @else
                                    -
                                @endif
                            </td>
                            <td style="word-wrap: break-word; word-break: break-word; max-width: 150px;">{{ $izin->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                @php
                                    $status = match (true) {
                                        is_null($izin->approved_by) => ['label' => 'Menunggu', 'class' => 'status-pending'],
                                        !is_null($izin->approved_at) => ['label' => 'Disetujui', 'class' => 'status-approved'],
                                        default => ['label' => 'Ditolak', 'class' => 'status-rejected'],
                                    };
                                @endphp
                                <span class="status-badge {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                                @if($izin->approvedBy)
                                    <br><small style="color: #666; font-size: 7px;">
                                        oleh {{ $izin->approvedBy->nama }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <p>Tidak ada data izin yang ditemukan untuk periode yang dipilih.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh Sistem Manajemen Absensi PT. Sucofindo</p>
        <p>Tanggal cetak: {{ $generated_at }} | Total data: {{ $data->count() }} izin</p>
    </div>
</body>
</html>
