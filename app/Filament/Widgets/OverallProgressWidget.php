<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\ProgressService;

class OverallProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.overall-progress-widget';

    // Atur grid column span agar lebar (misal span full atau 2 kolom)
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = auth()->user();
        $progressService = new ProgressService();
        $overallProgress = $progressService->calculateOverallProgress($user);

        // Dapatkan data quick stats untuk info pendukung
        $stats = $progressService->getQuickStats($user);

        return [
            'overallProgress' => $overallProgress,
            'completedTasks' => $stats['completed_tasks'],
            'totalTasks' => $stats['total_tasks'],
            'completedMilestones' => $stats['completed_milestones'],
            'totalMilestones' => $stats['total_milestones'],
        ];
    }
}
