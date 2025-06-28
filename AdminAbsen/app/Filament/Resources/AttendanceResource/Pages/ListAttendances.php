<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Exports\AttendanceExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Attendance;
use Carbon\Carbon;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada header actions untuk admin - hanya view data
        ];
    }

    private function getWorkDaysInPeriod($startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $workDays = 0;
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workDays++;
            }
        }

        return $workDays;
    }

    private function getDurationWork($attendance)
    {
        if (!$attendance->check_in || !$attendance->check_out) {
            return '-';
        }

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($attendance->check_out);

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

        $checkIn = Carbon::parse($attendance->check_in);
        $jamMasukStandar = Carbon::parse('08:00');

        if ($checkIn->greaterThan($jamMasukStandar)) {
            return 'Terlambat';
        }

        return 'Tepat Waktu';
    }
}
