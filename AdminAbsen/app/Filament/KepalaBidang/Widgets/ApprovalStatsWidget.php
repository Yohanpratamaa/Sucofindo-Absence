<?php

namespace App\Filament\KepalaBidang\Widgets;

use App\Models\Izin;
use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Get team members (for demo - all employees)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        // Get pending approvals
        $pendingApprovals = Izin::whereIn('user_id', $teamMembers)
            ->whereNull('approved_by')
            ->count();

        // Get approved by me this month
        $currentMonth = Carbon::now()->format('Y-m');
        $approvedByMe = Izin::where('approved_by', $user->id)
            ->whereNotNull('approved_at')
            ->whereRaw("DATE_FORMAT(approved_at, '%Y-%m') = ?", [$currentMonth])
            ->count();

        // Get rejected by me this month
        $rejectedByMe = Izin::where('approved_by', $user->id)
            ->whereNull('approved_at')
            ->whereRaw("DATE_FORMAT(updated_at, '%Y-%m') = ?", [$currentMonth])
            ->count();

        // Get total processed this month
        $totalProcessed = $approvedByMe + $rejectedByMe;

        return [
            Stat::make('Menunggu Persetujuan', $pendingApprovals)
                ->description('Izin yang belum diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Disetujui Bulan Ini', $approvedByMe)
                ->description('Izin yang saya setujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Ditolak Bulan Ini', $rejectedByMe)
                ->description('Izin yang saya tolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Diproses', $totalProcessed)
                ->description('Semua keputusan bulan ini')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),
        ];
    }
}
