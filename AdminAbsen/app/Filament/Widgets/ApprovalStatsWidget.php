<?php

namespace App\Filament\Widgets;

use App\Models\Izin;
use App\Models\OvertimeAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ApprovalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $currentUser = Auth::user();
        
        // Statistik Izin yang sudah disetujui oleh user saat ini
        $approvedIzinCount = Izin::where('approved_by', $currentUser->id)
            ->whereNotNull('approved_at')
            ->count();
            
        $rejectedIzinCount = Izin::where('approved_by', $currentUser->id)
            ->whereNull('approved_at')
            ->count();
            
        // Statistik Lembur yang sudah disetujui oleh user saat ini
        $approvedOvertimeCount = OvertimeAssignment::where('approved_by', $currentUser->id)
            ->where('status', 'Accepted')
            ->count();
            
        $rejectedOvertimeCount = OvertimeAssignment::where('approved_by', $currentUser->id)
            ->where('status', 'Rejected')
            ->count();
            
        // Statistik pending yang perlu persetujuan
        $pendingIzinCount = Izin::pending()->count();
        $pendingOvertimeCount = OvertimeAssignment::where('status', 'Assigned')->count();

        return [
            Stat::make('Total Izin Disetujui', $approvedIzinCount)
                ->description("Oleh {$currentUser->nama}")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Izin Ditolak', $rejectedIzinCount)
                ->description("Oleh {$currentUser->nama}")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
                
            Stat::make('Total Lembur Disetujui', $approvedOvertimeCount)
                ->description("Oleh {$currentUser->nama}")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Lembur Ditolak', $rejectedOvertimeCount)
                ->description("Oleh {$currentUser->nama}")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
                
            Stat::make('Izin Menunggu Persetujuan', $pendingIzinCount)
                ->description('Perlu segera diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Lembur Menunggu Persetujuan', $pendingOvertimeCount)
                ->description('Perlu segera diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
