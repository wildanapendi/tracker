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
     */
    public function calculateChapterProgress(Chapter $chapter): float
    {
        $totalTasks = $chapter->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0.0;
        }

        $completedTasks = $chapter->tasks()
            ->where('status', TaskStatus::Completed)
            ->count();

        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Menghitung progres keseluruhan skripsi.
     * Formula: sum(chapter_progress * normalized_weight)
     */
    public function calculateOverallProgress(User $user): float
    {
        $chapters = $user->chapters;
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
        $totalDocs = $milestone->documents()->count();

        if ($totalDocs === 0) {
            return 0.0;
        }

        $completedDocs = $milestone->documents()
            ->where('is_completed', true)
            ->count();

        return round(($completedDocs / $totalDocs) * 100, 2);
    }

    /**
     * Mengambil statistik ringkas untuk ditampilkan di dashboard.
     */
    public function getQuickStats(User $user): array
    {
        // Total bimbingan selesai
        $completedGuidances = $user->guidances()
            ->where('status', GuidanceStatus::Completed)
            ->count();

        // Bab selesai (progress 100%)
        $totalChapters = $user->chapters()->count();
        $completedChapters = 0;
        foreach ($user->chapters as $chapter) {
            if ($this->calculateChapterProgress($chapter) == 100) {
                $completedChapters++;
            }
        }

        // Berkas lengkap vs Total berkas
        $totalDocuments = MilestoneDocument::whereHas('milestone', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $completedDocuments = MilestoneDocument::whereHas('milestone', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('is_completed', true)->count();

        // Metrik tambahan untuk widget dashboard
        $totalMilestones = $user->milestones()->count();
        $completedMilestones = $user->milestones()
            ->where('status', \App\Enums\MilestoneStatus::Completed)
            ->count();

        $chapterIds = $user->chapters()->pluck('id');
        $totalTasks = \App\Models\ChapterTask::whereIn('chapter_id', $chapterIds)->count();
        $completedTasks = \App\Models\ChapterTask::whereIn('chapter_id', $chapterIds)
            ->where('status', TaskStatus::Completed)
            ->count();

        $totalGuidances = $user->guidances()->count();

        return [
            // Keys original
            'completed_guidances' => $completedGuidances,
            'chapters_completed' => $completedChapters,
            'total_chapters' => $totalChapters,
            'documents_completed' => $completedDocuments,
            'total_documents' => $totalDocuments,

            // Keys baru yang dibutuhkan widget
            'overall_progress' => $this->calculateOverallProgress($user),
            'completed_tasks' => $completedTasks,
            'total_tasks' => $totalTasks,
            'completed_milestones' => $completedMilestones,
            'total_milestones' => $totalMilestones,
            'total_guidances' => $totalGuidances,
        ];
    }
}
