<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $userId;

    public function __construct($startDate = null, $endDate = null, $userId = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
        $this->userId = $userId;
    }

    public function collection()
    {
        $query = Attendance::with('user')
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Karyawan',
            'NPP',
            'Jabatan',
            'Check In',
            'Absen Siang',
            'Check Out',
            'Durasi Kerja',
            'Lembur (Menit)',
            'Status Kehadiran',
            'Tipe Absensi',
            'Lokasi Check In (Lat, Lng)',
            'Lokasi Check Out (Lat, Lng)',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->created_at->format('d M Y'),
            $attendance->user->nama ?? '-',
            $attendance->user->npp ?? '-',
            $attendance->user->jabatan_nama ?? '-',
            $attendance->check_in ? $attendance->check_in->format('H:i') : '-',
            $attendance->absen_siang ? $attendance->absen_siang->format('H:i') : '-',
            $attendance->check_out ? $attendance->check_out->format('H:i') : '-',
            $this->getDurationWork($attendance),
            $attendance->overtime ?? 0,
            $this->getAttendanceStatus($attendance),
            $attendance->attendance_type ?? '-',
            $this->getCheckInLocation($attendance),
            $this->getCheckOutLocation($attendance),
        ];
    }

    private function getDurationWork($attendance)
    {
        if (!$attendance->check_in || !$attendance->check_out) {
            return '-';
        }

        $checkIn = \Carbon\Carbon::parse($attendance->check_in);
        $checkOut = \Carbon\Carbon::parse($attendance->check_out);

        // Hitung total durasi dalam menit (pastikan urutan parameter benar)
        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        
        // Jika ada absen siang, kurangi 1 jam untuk istirahat
        if ($attendance->absen_siang) {
            $totalMinutes = max(0, $totalMinutes - 60);
        }

        if ($totalMinutes <= 0) {
            return '0 jam 0 menit';
        }

        $hours = intval($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            return $hours . ' jam';
        } else {
            return $minutes . ' menit';
        }
    }

    private function getAttendanceStatus($attendance)
    {
        if (!$attendance->check_in) {
            return 'Tidak Hadir';
        }

        $checkIn = \Carbon\Carbon::parse($attendance->check_in);
        $jamMasukStandar = \Carbon\Carbon::parse('08:00');

        if ($checkIn->greaterThan($jamMasukStandar)) {
            return 'Terlambat';
        }

        return 'Tepat Waktu';
    }

    private function getCheckInLocation($attendance)
    {
        return $attendance->latitude_absen_masuk && $attendance->longitude_absen_masuk
            ? $attendance->latitude_absen_masuk . ', ' . $attendance->longitude_absen_masuk
            : '-';
    }

    private function getCheckOutLocation($attendance)
    {
        return $attendance->latitude_absen_pulang && $attendance->longitude_absen_pulang
            ? $attendance->latitude_absen_pulang . ', ' . $attendance->longitude_absen_pulang
            : '-';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '366092'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12, // Tanggal
            'B' => 20, // Nama Karyawan
            'C' => 12, // NPP
            'D' => 15, // Jabatan
            'E' => 10, // Check In
            'F' => 12, // Absen Siang
            'G' => 10, // Check Out
            'H' => 15, // Durasi Kerja
            'I' => 12, // Lembur
            'J' => 15, // Status Kehadiran
            'K' => 12, // Tipe Absensi
            'L' => 20, // Lokasi Check In
            'M' => 20, // Lokasi Check Out
        ];
    }

    public function title(): string
    {
        $title = 'Laporan Absensi ';
        $title .= Carbon::parse($this->startDate)->format('d M Y');
        $title .= ' - ' . Carbon::parse($this->endDate)->format('d M Y');

        if ($this->userId) {
            $pegawai = Pegawai::find($this->userId);
            $title .= ' - ' . ($pegawai->nama ?? 'Unknown');
        }

        return $title;
    }
}
