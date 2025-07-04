<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class FixLateCheckInStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:fix-late-checkin-status {--date= : Specific date to fix (Y-m-d format)} {--all : Fix all records}';

    /**
     * The console command description.
     */
    protected $description = 'Fix status for attendance records with check-in >= 17:00 to be "Tidak Absensi"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing late check-in status...');

        $query = Attendance::whereNotNull('check_in')
            ->whereTime('check_in', '>=', '17:00:00');

        // Filter by date if specified
        if ($this->option('date')) {
            $date = Carbon::parse($this->option('date'));
            $query->whereDate('created_at', $date);
            $this->info("Fixing records for date: {$date->format('Y-m-d')}");
        } elseif (!$this->option('all')) {
            // Default: only today's records
            $query->whereDate('created_at', Carbon::today());
            $this->info("Fixing records for today: " . Carbon::today()->format('Y-m-d'));
        } else {
            $this->info("Fixing all records with late check-in");
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->info('No records found with check-in >= 17:00');
            return Command::SUCCESS;
        }

        $this->info("Found {$records->count()} records to fix");

        $fixedCount = 0;
        foreach ($records as $record) {
            // Skip if already "Tidak Absensi" or is izin/sakit/cuti
            if (in_array($record->getRawOriginal('status_kehadiran'), ['Tidak Absensi', 'Izin', 'Sakit', 'Cuti'])) {
                continue;
            }

            // Update status ke "Tidak Absensi" untuk check-in >= 17:00
            $record->update(['status_kehadiran' => 'Tidak Absensi']);
            $fixedCount++;

            $this->line("Fixed attendance ID {$record->id} - Check-in: {$record->check_in->format('H:i')} - Status: Tidak Absensi");
        }

        $this->info("Fixed {$fixedCount} attendance records");
        $this->info("Late check-in (>= 17:00) records now show status 'Tidak Absensi'");

        return Command::SUCCESS;
    }
}
