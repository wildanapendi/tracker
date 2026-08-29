<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Guidance;
use App\Models\Milestone;
use App\Filament\Resources\ChapterResource;
use App\Filament\Resources\GuidanceResource;
use App\Filament\Resources\MilestoneResource;
use Filament\Pages\Dashboard;

class FilamentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(Dashboard::getUrl());
        $response->assertStatus(200);
    }

    /* ChapterResource Tests */

    public function test_chapters_index_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(ChapterResource::getUrl('index'));
        $response->assertStatus(200);
    }

    public function test_chapters_create_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(ChapterResource::getUrl('create'));
        $response->assertStatus(200);
    }

    public function test_chapters_edit_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(ChapterResource::getUrl('edit', ['record' => $chapter]));
        $response->assertStatus(200);
    }

    /* GuidanceResource Tests */

    public function test_guidances_index_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(GuidanceResource::getUrl('index'));
        $response->assertStatus(200);
    }

    public function test_guidances_create_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(GuidanceResource::getUrl('create'));
        $response->assertStatus(200);
    }

    public function test_guidances_edit_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $guidance = Guidance::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(GuidanceResource::getUrl('edit', ['record' => $guidance]));
        $response->assertStatus(200);
    }

    /* MilestoneResource Tests */

    public function test_milestones_index_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(MilestoneResource::getUrl('index'));
        $response->assertStatus(200);
    }

    public function test_milestones_create_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(MilestoneResource::getUrl('create'));
        $response->assertStatus(200);
    }

    public function test_milestones_edit_page_renders_for_authenticated_user()
    {
        $user = User::factory()->create();
        $milestone = Milestone::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(MilestoneResource::getUrl('edit', ['record' => $milestone]));
        $response->assertStatus(200);
    }
}
