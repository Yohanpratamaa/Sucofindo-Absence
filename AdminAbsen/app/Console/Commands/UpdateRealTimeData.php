<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UpdateRealTimeData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'realtime:update';

    /**
     * The console command description.
     */
    protected $description = 'Update real-time data cache for dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Jakarta');

        // Update real-time statistics
        $stats = [
            'current_time' => $now->format('H:i:s'),
            'current_date' => $now->format('d M Y'),
            'timestamp' => $now->timestamp,
            'is_working_day' => $now->isWeekday(),
            'working_hours_status' => $this->getWorkingHoursStatus($now),
            'updated_at' => $now->toISOString(),
        ];

        // Cache for 60 seconds
        Cache::put('realtime_stats', $stats, 60);

        $this->info('Real-time data updated at: ' . $now->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    private function getWorkingHoursStatus(Carbon $now): string
    {
        $hour = $now->hour;

        if ($hour >= 8 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 13 && $hour < 17) {
            return 'afternoon';
        } else {
            return 'non-working';
        }
    }
}
