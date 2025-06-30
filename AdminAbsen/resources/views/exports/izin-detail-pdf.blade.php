<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Izin - {{ $izin->user->nama }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 25px;
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #0066cc;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        .signature-title {
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding: 5px 0;
        }
        .document-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT. SUCOFINDO</div>
        <div class="document-title">FORMULIR PERMOHONAN IZIN</div>
        <div class="document-info">
            <strong>No. Dokumen:</strong> IZN-{{ str_pad($izin->id, 4, '0', STR_PAD_LEFT) }}/{{ $izin->created_at->format('m/Y') }}
            <br>
            <strong>Tanggal Cetak:</strong> {{ $generated_at }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informasi Pegawai</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Lengkap:</div>
                <div class="info-value">{{ $izin->user->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NPP:</div>
                <div class="info-value">{{ $izin->user->npp }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jabatan:</div>
                <div class="info-value">{{ $izin->user->jabatan_nama ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Posisi:</div>
                <div class="info-value">{{ $izin->user->posisi_nama ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detail Permohonan Izin</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Tanggal Pengajuan:</div>
                <div class="info-value">{{ $izin->created_at->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Izin:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $izin->jenis_izin === 'sakit' ? 'rejected' : ($izin->jenis_izin === 'cuti' ? 'approved' : 'pending') }}">
                        {{ ucfirst($izin->jenis_izin) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Mulai:</div>
                <div class="info-value">{{ $izin->tanggal_mulai->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Akhir:</div>
                <div class="info-value">{{ $izin->tanggal_akhir->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Durasi:</div>
                <div class="info-value">{{ $izin->tanggal_mulai->diffInDays($izin->tanggal_akhir) + 1 }} hari</div>
            </div>
            <div class="info-row">
                <div class="info-label">Keterangan/Alasan:</div>
                <div class="info-value">{{ $izin->keterangan }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Dokumen Pendukung:</div>
                <div class="info-value">
                    @if($izin->dokumen_pendukung)
                        {{ basename($izin->dokumen_pendukung) }}
                    @else
                        Tidak ada dokumen pendukung
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Status Persetujuan</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @php
                        $statusClass = match($izin->status) {
                            'approved' => 'approved',
                            'rejected' => 'rejected',
                            default => 'pending'
                        };
                        $statusText = match($izin->status) {
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            default => 'Menunggu Persetujuan'
                        };
                    @endphp
                    <span class="status-badge status-{{ $statusClass }}">{{ $statusText }}</span>
                </div>
            </div>
            @if($izin->approved_by)
            <div class="info-row">
                <div class="info-label">Diproses Oleh:</div>
                <div class="info-value">{{ $izin->approvedBy->nama ?? 'Unknown' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Diproses:</div>
                <div class="info-value">
                    {{ $izin->approved_at ? $izin->approved_at->format('d F Y, H:i') : $izin->updated_at->format('d F Y, H:i') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Keterangan:</div>
                <div class="info-value">{{ $izin->approval_info }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($izin->approved_by)
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Pemohon</div>
            <div class="signature-name">{{ $izin->user->nama }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">{{ $izin->approved_at ? 'Menyetujui' : 'Menolak' }}</div>
            <div class="signature-name">{{ $izin->approvedBy->nama ?? 'Unknown' }}</div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Absensi PT. Sucofindo</p>
        <p>Tanggal cetak: {{ $generated_at }}</p>
    </div>
</body>
</html>
