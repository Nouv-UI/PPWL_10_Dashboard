<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_published_projects_with_category_and_technologies(): void
    {
        $category = Category::create(['name' => 'Web']);
        $technology = Technology::create(['name' => 'Laravel']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'status' => 'published',
        ]);
        $project->technologies()->attach($technology);
        Project::factory()->create(['status' => 'draft']);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', $project->name)
            ->assertJsonPath('data.0.category', 'Web')
            ->assertJsonPath('data.0.technologies.0', 'Laravel');
    }

    public function test_show_returns_a_published_project_by_slug_and_hides_drafts(): void
    {
        $project = Project::factory()->create(['status' => 'published']);
        $project->slug = 'public-project';
        $project->save();

        $this->getJson('/api/projects/public-project')
            ->assertOk()
            ->assertJsonPath('data.slug', 'public-project')
            ->assertJsonPath('data.title', $project->name);

        $draft = Project::factory()->create(['status' => 'draft']);
        $draft->slug = 'draft-project';
        $draft->save();

        $this->getJson('/api/projects/draft-project')->assertNotFound();
    }
}
