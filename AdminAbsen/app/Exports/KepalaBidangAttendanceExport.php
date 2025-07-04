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

class KepalaBidangAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
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
        // Get only employees (role_user = 'employee') yang aktif
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        $query = Attendance::with('user')
            ->whereIn('user_id', $teamMembers)
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
            'Nama Pegawai',
            'NPP',
            'Jabatan',
            'Tipe Absensi',
            'Check In',
            'Absen Siang',
            'Check Out',
            'Durasi Kerja',
            'Status Kehadiran',
            'Detail Keterlambatan',
            'Lembur (Menit)',
            'Kelengkapan Absensi',
            'Lokasi Check In',
            'Lokasi Absen Siang',
            'Lokasi Check Out',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->created_at->format('d M Y'),
            $attendance->user->nama ?? '-',
            $attendance->user->npp ?? '-',
            $attendance->user->jabatan_nama ?? '-',
            $attendance->attendance_type ?? '-',
            $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '-',
            $attendance->absen_siang ? Carbon::parse($attendance->absen_siang)->format('H:i') : '-',
            $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-',
            $this->getDurationWork($attendance),
            $attendance->status_kehadiran ?? '-',
            $attendance->keterlambatan_detail ?? '-',
            $attendance->overtime ?? 0,
            $this->getKelengkapanStatus($attendance),
            $this->getCheckInLocation($attendance),
            $this->getAbsenSiangLocation($attendance),
            $this->getCheckOutLocation($attendance),
        ];
    }

    private function getDurationWork($attendance)
    {
        if (!$attendance->check_in || !$attendance->check_out) {
            return '-';
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($attendance->check_out);

        // Hitung total durasi dalam menit
        $totalMinutes = $checkIn->diffInMinutes($checkOut);

        // Jika ada absen siang, kurangi 1 jam untuk istirahat
        if ($attendance->absen_siang && $attendance->attendance_type === 'Dinas Luar') {
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

    private function getKelengkapanStatus($attendance)
    {
        if (!$attendance->kelengkapan_absensi) {
            return '-';
        }

        $kelengkapan = $attendance->kelengkapan_absensi;
        return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
    }

    private function getCheckInLocation($attendance)
    {
        return $attendance->latitude_absen_masuk && $attendance->longitude_absen_masuk
            ? $attendance->latitude_absen_masuk . ', ' . $attendance->longitude_absen_masuk
            : '-';
    }

    private function getAbsenSiangLocation($attendance)
    {
        if ($attendance->attendance_type !== 'Dinas Luar') {
            return 'Tidak diperlukan';
        }

        return $attendance->latitude_absen_siang && $attendance->longitude_absen_siang
            ? $attendance->latitude_absen_siang . ', ' . $attendance->longitude_absen_siang
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
            'B' => 20, // Nama Pegawai
            'C' => 12, // NPP
            'D' => 15, // Jabatan
            'E' => 12, // Tipe Absensi
            'F' => 10, // Check In
            'G' => 12, // Absen Siang
            'H' => 10, // Check Out
            'I' => 15, // Durasi Kerja
            'J' => 15, // Status Kehadiran
            'K' => 20, // Detail Keterlambatan
            'L' => 12, // Lembur
            'M' => 18, // Kelengkapan Absensi
            'N' => 20, // Lokasi Check In
            'O' => 20, // Lokasi Absen Siang
            'P' => 20, // Lokasi Check Out
        ];
    }

    public function title(): string
    {
        $title = 'Laporan Absensi Pegawai ';
        $title .= Carbon::parse($this->startDate)->format('d M Y');
        $title .= ' - ' . Carbon::parse($this->endDate)->format('d M Y');

        if ($this->userId) {
            $pegawai = Pegawai::find($this->userId);
            $title .= ' - ' . ($pegawai->nama ?? 'Unknown');
        }

        return $title;
    }
}
