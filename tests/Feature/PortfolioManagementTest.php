<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Skill;
use App\Models\Technology;
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
        Category::create(['name' => 'Web']);
        Technology::create(['name' => 'Laravel']);
        $skill = Skill::factory()->create(['user_id' => $user->id, 'name' => 'Laravel']);
        $published = Project::factory()->create(['user_id' => $user->id, 'status' => 'published']);
        $draft = Project::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        $published->skills()->attach($skill);
        $draft->skills()->attach($skill);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Projects')
            ->assertSee('Categories')
            ->assertSee('Technologies')
            ->assertSee('Published')
            ->assertSee('Draft')
            ->assertSee('Laravel')
            ->assertSee('2 project');
    }

    public function test_project_update_replaces_and_deletes_old_thumbnail(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Web']);
        Storage::disk('public')->put('projects/thumbnails/old.jpg', 'old');
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'thumbnail' => 'projects/thumbnails/old.jpg',
        ]);

        $this->actingAs($user)->put(route('post.projects.update', $project), [
            'name' => 'Updated project',
            'category_id' => $category->id,
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

    public function test_gallery_accepts_images_without_extra_dimension_rules_and_stops_at_ten(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('post.projects.gallery.store', $project), [
            'images' => [$this->pngUpload('small.png', 799, 600)],
        ])->assertRedirect();
        $this->assertSame(1, ProjectImage::query()->where('project_id', $project->id)->count());

        Gallery::factory()->count(9)->create(['project_id' => $project->id]);
        $this->actingAs($user)->post(route('post.projects.gallery.store', $project), [
            'images' => [$this->pngUpload('eleventh.png', 800, 600)],
        ])->assertSessionHasErrors('images');
    }

    public function test_gallery_accepts_multiple_images_in_one_upload(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('post.projects.gallery.store', $project), [
            'images' => [
                $this->pngUpload('one.png', 400, 300),
                $this->pngUpload('two.png', 400, 300),
                $this->pngUpload('three.png', 400, 300),
            ],
        ])->assertRedirect();

        $this->assertSame(3, ProjectImage::query()->where('project_id', $project->id)->count());
    }

    public function test_project_create_requires_valid_thumbnail_dimensions(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Web']);

        $this->actingAs($user)->post(route('post.projects.store'), [
            'name' => 'Too small',
            'category_id' => $category->id,
            'status' => 'draft',
            'thumbnail' => $this->pngUpload('small.png', 399, 299),
        ])->assertSessionHasErrors('thumbnail');

        $this->assertSame(0, Project::query()->count());

        $this->actingAs($user)->post(route('post.projects.store'), [
            'name' => 'PDF upload',
            'category_id' => $category->id,
            'status' => 'draft',
            'thumbnail' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('thumbnail');
    }

    public function test_project_create_stores_thumbnail_and_category(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Web']);

        $this->actingAs($user)->post(route('post.projects.store'), [
            'name' => 'New portfolio',
            'category_id' => $category->id,
            'status' => 'published',
            'thumbnail' => $this->pngUpload('thumbnail.png', 800, 600),
        ])->assertRedirect(route('post.projects.index'));

        $project = Project::query()->where('name', 'New portfolio')->firstOrFail();
        $this->assertSame('New portfolio', $project->title);
        $this->assertSame($category->id, $project->category_id);
        Storage::disk('public')->assertExists($project->thumbnail);
    }

    public function test_gallery_image_can_be_deleted_with_its_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        Storage::disk('public')->put('projects/gallery/remove.jpg', 'image');
        $image = ProjectImage::factory()->create([
            'project_id' => $project->id,
            'image' => 'projects/gallery/remove.jpg',
        ]);

        $this->actingAs($user)->delete(route('post.gallery.destroy', [
            'project' => $project,
            'gallery' => $image,
        ]))->assertRedirect();

        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('projects/gallery/remove.jpg');
    }

    public function test_project_delete_removes_thumbnail_and_gallery_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Storage::disk('public')->put('projects/thumbnails/project.jpg', 'thumbnail');
        Storage::disk('public')->put('projects/gallery/image.jpg', 'gallery');
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'thumbnail' => 'projects/thumbnails/project.jpg',
        ]);
        ProjectImage::factory()->create([
            'project_id' => $project->id,
            'image' => 'projects/gallery/image.jpg',
        ]);

        $this->actingAs($user)->delete(route('post.projects.destroy', $project))
            ->assertRedirect(route('post.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['project_id' => $project->id]);
        Storage::disk('public')->assertMissing('projects/thumbnails/project.jpg');
        Storage::disk('public')->assertMissing('projects/gallery/image.jpg');
    }

    public function test_public_portfolio_shows_published_project_and_images(): void
    {
        $project = Project::factory()->create(['status' => 'published']);
        $image = ProjectImage::factory()->create([
            'project_id' => $project->id,
            'image' => 'projects/gallery/public.jpg',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee($project->name)
            ->assertSee('projects/gallery/public.jpg');

        $draft = Project::factory()->create(['status' => 'draft']);
        $this->get(route('projects.show', $draft))->assertNotFound();
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
