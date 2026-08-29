<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Models\Chapter;
use App\Models\ChapterTask;
use App\Models\Milestone;
use App\Models\MilestoneDocument;
use App\Models\User;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProgressService $progressService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->progressService = new ProgressService();
    }

    public function test_calculate_chapter_progress_returns_zero_if_no_tasks()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create(['user_id' => $user->id]);

        $progress = $this->progressService->calculateChapterProgress($chapter);

        $this->assertEquals(0.0, $progress);
    }

    public function test_calculate_chapter_progress_returns_correct_percentage()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create(['user_id' => $user->id]);

        // Create 4 tasks: 1 completed, 1 in_progress, 2 not_started
        ChapterTask::factory()->create(['chapter_id' => $chapter->id, 'status' => TaskStatus::Completed]);
        ChapterTask::factory()->create(['chapter_id' => $chapter->id, 'status' => TaskStatus::InProgress]);
        ChapterTask::factory()->create(['chapter_id' => $chapter->id, 'status' => TaskStatus::NotStarted]);
        ChapterTask::factory()->create(['chapter_id' => $chapter->id, 'status' => TaskStatus::NotStarted]);

        $progress = $this->progressService->calculateChapterProgress($chapter);

        // 1 completed / 4 total = 25%
        $this->assertEquals(25.0, $progress);
    }

    public function test_calculate_overall_progress_with_normalized_weights()
    {
        $user = User::factory()->create();

        // Chapter 1: weight 2.0, 100% progress
        $chapter1 = Chapter::factory()->create(['user_id' => $user->id, 'weight' => 2.0]);
        ChapterTask::factory()->create(['chapter_id' => $chapter1->id, 'status' => TaskStatus::Completed]);
        
        // Chapter 2: weight 3.0, 50% progress
        $chapter2 = Chapter::factory()->create(['user_id' => $user->id, 'weight' => 3.0]);
        ChapterTask::factory()->create(['chapter_id' => $chapter2->id, 'status' => TaskStatus::Completed]);
        ChapterTask::factory()->create(['chapter_id' => $chapter2->id, 'status' => TaskStatus::NotStarted]);

        // Total weight = 5.0
        // Ch1 progress = 100 * (2/5) = 40%
        // Ch2 progress = 50 * (3/5) = 30%
        // Overall = 70%

        $overall = $this->progressService->calculateOverallProgress($user);

        $this->assertEquals(70.0, $overall);
    }

    public function test_calculate_milestone_completion_returns_correct_percentage()
    {
        $user = User::factory()->create();
        $milestone = Milestone::factory()->create(['user_id' => $user->id]);

        // 3 documents: 2 completed, 1 incomplete
        MilestoneDocument::factory()->create(['milestone_id' => $milestone->id, 'is_completed' => true]);
        MilestoneDocument::factory()->create(['milestone_id' => $milestone->id, 'is_completed' => true]);
        MilestoneDocument::factory()->create(['milestone_id' => $milestone->id, 'is_completed' => false]);

        $completion = $this->progressService->calculateMilestoneCompletion($milestone);

        // 2 / 3 = 66.67%
        $this->assertEquals(66.67, $completion);
    }
}
