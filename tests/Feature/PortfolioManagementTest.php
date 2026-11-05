<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_project_status_cards_and_top_skills(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $skill = Skill::factory()->create(['user_id' => $user->id, 'name' => 'Laravel']);
        $published = Project::factory()->create(['user_id' => $user->id, 'status' => 'published']);
        $draft = Project::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        $published->skills()->attach($skill);
        $draft->skills()->attach($skill);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Published')
            ->assertSee('Draft')
            ->assertSee('Laravel')
            ->assertSee('2 project');
    }

    public function test_project_update_replaces_and_deletes_old_thumbnail(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Storage::disk('public')->put('projects/thumbnails/old.jpg', 'old');
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'thumbnail' => 'projects/thumbnails/old.jpg',
        ]);

        $this->actingAs($user)->put(route('post.projects.update', $project), [
            'title' => 'Updated project',
            'description' => 'Updated description',
            'status' => 'published',
            'thumbnail' => $this->pngUpload('new.png', 1200, 800),
        ])->assertRedirect(route('post.projects.edit', $project));

        Storage::disk('public')->assertMissing('projects/thumbnails/old.jpg');
        $this->assertNotSame('projects/thumbnails/old.jpg', $project->fresh()->thumbnail);
        Storage::disk('public')->assertExists($project->fresh()->thumbnail);
    }

    public function test_project_index_and_edit_pages_render_for_owner(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('post.projects.index'))->assertOk()->assertSee($project->title);
        $this->actingAs($user)->get(route('post.projects.edit', $project))->assertOk()->assertSee('Edit Project');
    }

    public function test_gallery_requires_minimum_width_and_stops_at_ten_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('post.projects.gallery.store', $project), [
            'images' => [$this->pngUpload('small.png', 799, 600)],
        ])->assertSessionHasErrors('images.0');

        Gallery::factory()->count(10)->create(['project_id' => $project->id]);
        $this->actingAs($user)->post(route('post.projects.gallery.store', $project), [
            'images' => [$this->pngUpload('eleventh.png', 800, 600)],
        ])->assertSessionHasErrors('images');
    }

    public function test_gallery_caption_is_updated_with_put_and_is_owner_scoped(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $gallery = Gallery::factory()->create(['project_id' => $project->id, 'caption' => 'Old']);

        $this->actingAs($user)->put(route('post.gallery.caption.update', $gallery), [
            'caption' => 'New caption',
        ])->assertRedirect();
        $this->assertSame('New caption', $gallery->fresh()->caption);

        $this->actingAs($other)->put(route('post.gallery.caption.update', $gallery), [
            'caption' => 'Not allowed',
        ])->assertForbidden();
    }

    public function test_skill_update_is_validated_and_owner_scoped(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $skill = Skill::factory()->create(['user_id' => $user->id, 'name' => 'PHP']);

        $this->actingAs($user)->put(route('post.skills.update', $skill), [
            'name' => '',
            'category' => 'Backend',
        ])->assertSessionHasErrors('name');

        $this->actingAs($other)->get(route('post.skills.edit', $skill))->assertForbidden();
    }

    private function pngUpload(string $name, int $width, int $height): UploadedFile
    {
        $rows = str_repeat("\0" . str_repeat("\xff\0\0", $width), $height);
        $ihdr = pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0);
        $png = "\x89PNG\r\n\x1a\n"
            . $this->pngChunk('IHDR', $ihdr)
            . $this->pngChunk('IDAT', gzcompress($rows))
            . $this->pngChunk('IEND', '');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function pngChunk(string $type, string $data): string
    {
        return pack('N', strlen($data)) . $type . $data . pack('N', crc32($type . $data));
    }
}
