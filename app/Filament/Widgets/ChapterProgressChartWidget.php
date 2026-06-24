<?php

namespace App\Filament\Widgets;

use App\Models\Chapter;
use App\Services\ProgressService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChapterProgressChartWidget extends ChartWidget
{
    protected ?string $heading = 'Progres per Bab';
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $chapters = Chapter::with('tasks')
            ->where('user_id', Auth::id())
            ->orderBy('order')
            ->get();
        $labels = [];
        $data = [];
        $progressService = app(ProgressService::class);

        foreach ($chapters as $chapter) {
            $labels[] = "Bab " . $chapter->order;
            $data[] = $progressService->calculateChapterProgress($chapter);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Progres (%)',
                    'data' => $data,
                    'backgroundColor' => '#6366f1', // Warna primer (Indigo)
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
        ];
    }
}
