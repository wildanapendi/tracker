<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Milestone;
use App\Models\User;
use App\Enums\TaskStatus;
use App\Enums\GuidanceStatus;
use App\Models\MilestoneDocument;

class ProgressService
{
    /**
     * Menghitung progres satu bab.
     * Formula: (completed_tasks / total_tasks) * 100
     *
     * Jika tasks sudah eager-loaded, gunakan collection counting
     * untuk menghindari N+1 query.
     */
    public function calculateChapterProgress(Chapter $chapter): float
    {
        // Gunakan loaded relation jika tersedia, hindari N+1
        if ($chapter->relationLoaded('tasks')) {
            $tasks = $chapter->tasks;
            $totalTasks = $tasks->count();
            if ($totalTasks === 0) {
                return 0.0;
            }
            $completedTasks = $tasks->where('status', TaskStatus::Completed)->count();
        } else {
            $totalTasks = $chapter->tasks()->count();
            if ($totalTasks === 0) {
                return 0.0;
            }
            $completedTasks = $chapter->tasks()
                ->where('status', TaskStatus::Completed)
                ->count();
        }

        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Menghitung progres keseluruhan skripsi.
     * Formula: sum(chapter_progress * normalized_weight)
     *
     * Eager-load tasks sekali untuk menghindari 2N queries.
     */
    public function calculateOverallProgress(User $user): float
    {
        // Eager load chapters + tasks dalam 2 query total (bukan 2N+1)
        $chapters = $user->chapters()->with('tasks')->get();
        $totalWeight = $chapters->sum('weight');

        if ($totalWeight == 0) {
            return 0.0;
        }

        $overallProgress = 0.0;

        foreach ($chapters as $chapter) {
            $chapterProgress = $this->calculateChapterProgress($chapter);
            $normalizedWeight = $chapter->weight / $totalWeight;
            $overallProgress += ($chapterProgress * $normalizedWeight);
        }

        return round($overallProgress, 2);
    }

    /**
     * Menghitung persentase kelengkapan berkas pada satu milestone.
     * Formula: (completed_documents / total_documents) * 100
     */
    public function calculateMilestoneCompletion(Milestone $milestone): float
    {
        // Gunakan loaded relation jika tersedia
        if ($milestone->relationLoaded('documents')) {
            $docs = $milestone->documents;
            $totalDocs = $docs->count();
            if ($totalDocs === 0) {
                return 0.0;
            }
            $completedDocs = $docs->where('is_completed', true)->count();
        } else {
            $totalDocs = $milestone->documents()->count();
            if ($totalDocs === 0) {
                return 0.0;
            }
            $completedDocs = $milestone->documents()
                ->where('is_completed', true)
                ->count();
        }

        return round(($completedDocs / $totalDocs) * 100, 2);
    }

    /**
     * Mengambil statistik ringkas untuk ditampilkan di dashboard.
     *
     * Dioptimasi: eager load sekali, hitung di PHP — total ~5 queries
     * (sebelumnya bisa 2N+10 queries).
     */
    public function getQuickStats(User $user): array
    {
        // Eager load semua relasi yang dibutuhkan dalam batch
        $user->loadMissing([
            'chapters.tasks',
            'milestones',
            'guidances',
        ]);

        $chapters = $user->chapters;
        $totalChapters = $chapters->count();
        $completedChapters = 0;
        foreach ($chapters as $chapter) {
            if ($this->calculateChapterProgress($chapter) == 100) {
                $completedChapters++;
            }
        }

        // Tasks: hitung dari eager-loaded chapters→tasks
        $allTasks = $chapters->flatMap->tasks;
        $totalTasks = $allTasks->count();
        $completedTasks = $allTasks->where('status', TaskStatus::Completed)->count();

        // Milestones
        $milestones = $user->milestones;
        $totalMilestones = $milestones->count();
        $completedMilestones = $milestones
            ->where('status', \App\Enums\MilestoneStatus::Completed)
            ->count();

        // Documents: satu query karena relasi nested tidak di-eager-load
        $milestoneIds = $milestones->pluck('id');
        $totalDocuments = MilestoneDocument::whereIn('milestone_id', $milestoneIds)->count();
        $completedDocuments = MilestoneDocument::whereIn('milestone_id', $milestoneIds)
            ->where('is_completed', true)
            ->count();

        // Guidances
        $guidances = $user->guidances;
        $totalGuidances = $guidances->count();
        $completedGuidances = $guidances
            ->where('status', GuidanceStatus::Completed)
            ->count();

        // Overall progress: chapters+tasks sudah loaded, tanpa query tambahan
        $totalWeight = $chapters->sum('weight');
        $overallProgress = 0.0;
        if ($totalWeight > 0) {
            foreach ($chapters as $chapter) {
                $chapterProgress = $this->calculateChapterProgress($chapter);
                $normalizedWeight = $chapter->weight / $totalWeight;
                $overallProgress += ($chapterProgress * $normalizedWeight);
            }
            $overallProgress = round($overallProgress, 2);
        }

        return [
            // Keys original
            'completed_guidances' => $completedGuidances,
            'chapters_completed' => $completedChapters,
            'total_chapters' => $totalChapters,
            'documents_completed' => $completedDocuments,
            'total_documents' => $totalDocuments,

            // Keys baru yang dibutuhkan widget
            'overall_progress' => $overallProgress,
            'completed_tasks' => $completedTasks,
            'total_tasks' => $totalTasks,
            'completed_milestones' => $completedMilestones,
            'total_milestones' => $totalMilestones,
            'total_guidances' => $totalGuidances,
        ];
    }
}
