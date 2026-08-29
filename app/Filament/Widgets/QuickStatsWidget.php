<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\ProgressService;

class QuickStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $progressService = new ProgressService();
        $stats = $progressService->getQuickStats($user);

        return [
            Stat::make('Progress Pengerjaan', round($stats['overall_progress'], 2) . '%')
                ->description('Total progres keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Milestone Selesai', $stats['completed_milestones'] . ' / ' . $stats['total_milestones'])
                ->description('Milestone yang telah dicapai')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Bimbingan', $stats['total_guidances'])
                ->description('Riwayat bimbingan')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
