<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guidance;
use App\Models\ChapterTask;
use App\Models\Milestone;
use App\Enums\GuidanceStatus;
use App\Enums\TaskStatus;
use App\Enums\MilestoneStatus;


class CalendarController extends Controller
{
    /**
     * Endpoint agregasi data untuk FullCalendar
     */
    public function events(Request $request)
    {
        $request->validate([
            'start' => ['nullable', 'date'],
            'end'   => ['nullable', 'date'],
        ]);

        $userId = auth()->id();
        $start = $request->query('start');
        $end = $request->query('end');

        $events = collect();

        // 1. Data Bimbingan (Guidances)
        $guidances = Guidance::where('user_id', $userId);
        if ($start && $end) {
            $guidances->whereBetween('scheduled_at', [$start, $end]);
        }
        $guidances = $guidances->get();

        foreach ($guidances as $guidance) {
            $color = match ($guidance->status) {
                GuidanceStatus::Scheduled => '#4f46e5',   // indigo-600
                GuidanceStatus::Completed => '#16a34a',   // green-600
                GuidanceStatus::Cancelled => '#dc2626',   // red-600
                GuidanceStatus::Rescheduled => '#d97706', // amber-600 (warning)
                default => '#6b7280',                     // gray-500
            };

            $events->push([
                'id' => 'guidance_' . $guidance->id,
                'title' => '[Bimbingan] ' . ($guidance->agenda ?? 'Tanpa Agenda'),
                'start' => $guidance->scheduled_at->toIso8601String(),
                'color' => $color,
                'url' => route('filament.app.resources.guidances.edit', $guidance->id),
            ]);
        }

        // 2. Data Task (ChapterTasks)
        // Note: ChapterTasks berelasi dengan Chapter yang dimiliki User
        $tasks = ChapterTask::whereHas('chapter', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereNotNull('due_date');

        if ($start && $end) {
            $tasks->whereBetween('due_date', [$start, $end]);
        }
        $tasks = $tasks->get();

        foreach ($tasks as $task) {
            $color = match ($task->status) {
                TaskStatus::NotStarted => '#d97706', // amber-600
                TaskStatus::InProgress => '#2563eb', // blue-600
                TaskStatus::Completed => '#16a34a', // green-600
                default => '#6b7280',
            };

            $events->push([
                'id' => 'task_' . $task->id,
                'title' => '[Task] ' . $task->title,
                'start' => $task->due_date->toIso8601String(),
                'allDay' => true,
                'color' => $color,
                'url' => route('filament.app.resources.chapters.edit', $task->chapter_id),
            ]);
        }

        // 3. Data Milestone (Milestones)
        $milestones = Milestone::where('user_id', $userId)
            ->whereNotNull('target_date');

        if ($start && $end) {
            $milestones->whereBetween('target_date', [$start, $end]);
        }
        $milestones = $milestones->get();

        foreach ($milestones as $milestone) {
            $color = match ($milestone->status) {
                MilestoneStatus::NotStarted => '#e11d48', // rose-600
                MilestoneStatus::InProgress => '#2563eb', // blue-600
                MilestoneStatus::Completed => '#16a34a', // green-600
                default => '#6b7280',
            };

            $events->push([
                'id' => 'milestone_' . $milestone->id,
                'title' => '[Milestone] ' . $milestone->title,
                'start' => $milestone->target_date->toIso8601String(),
                'allDay' => true,
                'color' => $color,
                'url' => route('filament.app.resources.milestones.edit', $milestone->id),
            ]);
        }

        return response()->json($events->values()->all());
    }
}
