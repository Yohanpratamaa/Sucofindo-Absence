<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengajuan Lembur</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            padding: 10px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding: 10px 15px 10px 15px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
            padding-right: 15px;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 30%;
            text-align: right;
            padding-left: 15px;
        }

        .logo {
            width: 80px;
            height: auto;
            float: left;
            margin-right: 15px;
        }

        .company-info h1 {
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 3px;
        }

        .company-info h2 {
            font-size: 11px;
            font-weight: normal;
            color: #666;
            margin-bottom: 6px;
        }

        .sucofindo-logo {
            width: 80px;
            height: auto;
        }

        .document-info {
            margin: 15px 0;
            border-bottom: 1px solid #ccc;
            padding: 12px;
            background: #f8f9fa;
        }

        .doc-number {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .doc-date {
            font-size: 10px;
            color: #666;
        }

        .recipient-info {
            margin: 15px 0;
            padding: 12px;
            background: #fff;
            border-left: 4px solid #0066cc;
        }

        .recipient-info strong {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .recipient-address {
            margin-left: 0;
            font-size: 9px;
            line-height: 1.3;
            padding-left: 8px;
        }

        .subject {
            margin: 15px 0;
            font-weight: bold;
            padding: 10px 15px;
            background: #e8f4fd;
            border: 1px solid #0066cc;
            border-radius: 4px;
        }

        .content {
            margin: 15px 0;
            text-align: justify;
            line-height: 1.4;
            padding: 12px;
        }

        .overtime-details {
            margin: 15px 0;
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .details-table td {
            padding: 6px 10px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
            font-size: 9px;
        }

        .details-table td:first-child {
            width: 30%;
            font-weight: bold;
            color: #333;
            background: #f8f9fa;
        }

        .details-table td:nth-child(2) {
            width: 5%;
            text-align: center;
            background: #f8f9fa;
        }

        .details-table td:last-child {
            width: 65%;
            padding-left: 12px;
        }

        .approval-section {
            margin-top: 15px;
            text-align: left;
            padding: 12px;
        }

        .approval-text {
            margin-bottom: 15px;
            font-style: italic;
            line-height: 1.4;
        }

        .signature-area {
            margin-top: 20px;
            display: table;
            width: 100%;
            padding: 10px;
        }

        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
            padding-left: 20px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px solid #000;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            text-align: center;
            padding: 5px;
            background: #fff;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 40px;
            text-align: center;
            padding: 5px;
        }

        .signature-title {
            text-align: center;
            font-size: 9px;
            margin-top: 3px;
            padding: 3px;
        }

        .footer {
            position: fixed;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 8px 10px;
            background: #f8f9fa;
        }

        .page-number {
            text-align: center;
            font-size: 9px;
            margin-top: 15px;
            padding: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            margin-left: 10px;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .highlight {
            background-color: #e8f4fd;
            padding: 10px 12px;
            border-left: 4px solid #0066cc;
            margin: 12px 0;
            border-radius: 4px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 8px;
        }
    </style>
</head>
<body>
    <div class="container">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="company-info">
                <h1>PT SUCOFINDO</h1>
                <h2>Surveyor Indonesia</h2>
                <div style="clear: both; font-size: 8px; margin-top: 5px; color: #333; line-height: 1.3; padding: 3px 0;">
                    Jl. Raya Pasar Minggu Km 34, Jakarta Selatan 12520<br>
                    Telp: (021) 7947777 | Fax: (021) 7947888<br>
                    Email: info@sucofindo.co.id | www.sucofindo.co.id
                </div>
            </div>
        </div>
        <div class="header-right">
            <div style="width: 70px; height: 50px; border: 2px solid #0066cc; display: inline-block; text-align: center; line-height: 25px; font-size: 9px; color: #0066cc; font-weight: bold; padding: 5px; border-radius: 4px;">
                SUCOFINDO<br>LOGO
            </div>
        </div>
    </div>

    <!-- Document Info -->
    <div class="document-info">
        <div class="doc-number">No. {{ $overtime->overtime_id }}/{{ date('Y') }}</div>
        <div class="doc-date">{{ \Carbon\Carbon::parse($overtime->assigned_at)->locale('id')->translatedFormat('d F Y') }}</div>
    </div>

    <!-- Recipient -->
    <div class="recipient-info">
        <strong>Kepada Yth.</strong>
        <div>{{ $overtime->user->nama }}</div>
        <div class="recipient-address">
            NPP: {{ $overtime->user->npp ?? '-' }}<br>
            Jabatan: {{ $overtime->user->jabatan ?? 'Pegawai' }}<br>
            Divisi: {{ $overtime->user->divisi ?? 'Umum' }}<br>
            PT SUCOFINDO (Persero)
        </div>
    </div>

    <!-- Subject -->
    <div class="subject">
        <strong>Perihal: Surat Konfirmasi Pengajuan Lembur</strong>
        <span class="status-badge status-{{ strtolower($overtime->status) === 'accepted' ? 'approved' : (strtolower($overtime->status) === 'rejected' ? 'rejected' : 'pending') }}">
            {{ $overtime->status === 'Accepted' ? 'DISETUJUI' : ($overtime->status === 'Rejected' ? 'DITOLAK' : 'MENUNGGU PERSETUJUAN') }}
        </span>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Dengan hormat,</p>
        <p style="margin-top: 8px;">Berdasarkan pengajuan lembur yang telah diajukan pada tanggal {{ \Carbon\Carbon::parse($overtime->assigned_at)->locale('id')->translatedFormat('d F Y') }}, dengan ini kami sampaikan persetujuan program lembur untuk mahasiswa sebagai berikut:</p>
    </div>

    <!-- Overtime Details -->
    <div class="overtime-details">
        <h3 style="margin-bottom: 10px; color: #0066cc; font-size: 11px;">Detail Pengajuan Lembur</h3>
        <table class="details-table">
            <tr>
                <td>ID Lembur</td>
                <td>:</td>
                <td><strong>{{ $overtime->overtime_id }}</strong></td>
            </tr>
            <tr>
                <td>Nama Pegawai</td>
                <td>:</td>
                <td>{{ $overtime->user->nama }}</td>
            </tr>
            <tr>
                <td>NPP</td>
                <td>:</td>
                <td>{{ $overtime->user->npp ?? '-' }}</td>
            </tr>
            <tr>
                <td>Hari Lembur</td>
                <td>:</td>
                <td>{{ $overtime->hari_lembur }}</td>
            </tr>
            <tr>
                <td>Tanggal Lembur</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($overtime->tanggal_lembur)->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Jam Mulai</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($overtime->jam_mulai)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Jam Selesai</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($overtime->jam_selesai)->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Total Jam Lembur</td>
                <td>:</td>
                <td><strong>{{ $overtime->total_jam_formatted }}</strong></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>
                    <span class="status-badge status-{{ strtolower($overtime->status) === 'accepted' ? 'approved' : (strtolower($overtime->status) === 'rejected' ? 'rejected' : 'pending') }}">
                        {{ $overtime->status === 'Accepted' ? 'DISETUJUI' : ($overtime->status === 'Rejected' ? 'DITOLAK' : 'MENUNGGU PERSETUJUAN') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>{{ $overtime->keterangan }}</td>
            </tr>
            @if($overtime->approved_by)
            <tr>
                <td>Disetujui Oleh</td>
                <td>:</td>
                <td>{{ $overtime->approvedBy->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Persetujuan</td>
                <td>:</td>
                <td>{{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($overtime->status === 'Accepted')
    <div class="highlight">
        <strong>Catatan:</strong> Pengajuan lembur telah disetujui. Pastikan untuk melaksanakan lembur sesuai dengan jadwal yang telah ditentukan dan mencatat aktivitas yang dilakukan selama periode lembur.
    </div>
    @elseif($overtime->status === 'Rejected')
    <div class="highlight" style="border-left-color: #dc3545; background-color: #f8d7da;">
        <strong>Catatan:</strong> Pengajuan lembur ditolak. Silakan hubungi atasan langsung untuk informasi lebih lanjut atau pengajuan ulang.
    </div>
    @else
    <div class="highlight" style="border-left-color: #ffc107; background-color: #fff3cd;">
        <strong>Catatan:</strong> Pengajuan lembur sedang dalam proses review. Harap menunggu konfirmasi lebih lanjut.
    </div>
    @endif

    <!-- Approval Section -->
    <div class="approval-section">
        <p class="approval-text">Selanjutnya dimohon peserta program lembur dapat menjaga kerahasiaan data/dokumen dan mematuhi seluruh peraturan K3K yang berlaku di Perusahaan.</p>

        <p>Demikian informasi ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.</p>
    </div>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="signature-left">
            <div class="qr-code">
                @if(isset($qrCodeImage) && $qrCodeImage)
                    <img src="{{ $qrCodeImage }}" alt="QR Code" style="width: 60px; height: 60px;">
                @elseif(isset($qrCodeData))
                    <div style="text-align: center; font-size: 8px; font-weight: bold; margin-bottom: 3px;">VERIFICATION CODE</div>
                    <div style="font-size: 6px; word-break: break-all; line-height: 1.1; font-family: monospace; background: #f0f0f0; padding: 2px; border: 1px solid #ccc;">{{ $qrCodeData }}</div>
                @else
                    <div style="text-align: center; font-size: 8px;">
                        QR CODE<br>
                        VERIFICATION
                    </div>
                @endif
            </div>
        </div>
        <div class="signature-right">
            <div style="margin-bottom: 50px;">
                <div style="font-weight: bold; text-decoration: underline; font-size: 10px;">{{ $overtime->approvedBy->nama ?? 'KEPALA BIDANG' }}</div>
                <div style="font-size: 9px; margin-top: 3px;">{{ $overtime->approvedBy->jabatan ?? 'Kepala Bidang Dukungan Bisnis Cabang Bandung' }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; text-align: left;">
                <strong>PT SUPERINTENDING COMPANY OF INDONESIA (PERSERO)</strong><br>
                HEAD OFFICE<br>
                Jl. Raya Pasar Minggu Km. 34, Jakarta Selatan 12520 - Indonesia<br>
                Telepon: +62-21-7947777 | Fax: +62-21-7947888<br>
                Email: info@sucofindo.co.id | www.sucofindo.co.id
            </div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: top;">
                <div style="font-size: 7px; margin-top: 10px;">
                    Dokumen ini digenerate secara otomatis pada:<br>
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i:s') }} WIB
                </div>
            </div>
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">
        <strong>1/1</strong>
    </div>

    </div> <!-- End container -->
</body>
</html>
