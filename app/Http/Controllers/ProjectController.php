<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryUploadRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('post.projects.create', compact('categories'));
    }

    public function index(Request $request): View
    {
        $projects = $request->user()->projects()
            ->with(['skills', 'galleries', 'category'])
            ->latest()
            ->paginate(9);

        return view('post.projects.index', compact('projects'));
    }

    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['title'] = $data['name'];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('projects/thumbnails', 'public');
        }

        $request->user()->projects()->create($data);

        return to_route('post.projects.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function edit(Project $project): View
    {
        $this->ensureOwner($project);

        $project->load(['skills', 'galleries', 'category']);
        $skills = request()->user()->skills()->orderBy('category')->orderBy('name')->get();
        $categories = Category::query()->orderBy('name')->get();

        return view('post.projects.edit', compact('project', 'skills', 'categories'));
    }

    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        $oldThumbnail = $project->thumbnail;
        $data = $request->safe()->except(['thumbnail', 'skill_ids']);
        $data['title'] = $data['name'];

        $newThumbnail = DB::transaction(function () use ($request, $project, $data): ?string {
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')->store('projects/thumbnails', 'public');
            }

            $project->update($data);
            $project->skills()->sync($request->input('skill_ids', []));

            return $data['thumbnail'] ?? null;
        });

        if ($newThumbnail && $oldThumbnail) {
            Storage::disk('public')->delete($oldThumbnail);
        }

        return to_route('post.projects.edit', $project)->with('success', 'Project berhasil diperbarui.');
    }

    public function storeGallery(GalleryUploadRequest $request, Project $project): RedirectResponse
    {
        $this->ensureOwner($project);

        foreach ($request->file('images', []) as $image) {
            $project->images()->create([
                'image' => $image->store('projects/gallery', 'public'),
            ]);
        }

        return back()->with('success', 'Gallery berhasil ditambahkan.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->ensureOwner($project);

        if ($project->thumbnail) {
            Storage::disk('public')->delete($project->thumbnail);
        }

        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $project->delete();

        return to_route('post.projects.index')->with('success', 'Project dihapus.');
    }

    private function ensureOwner(Project $project): void
    {
        abort_unless($project->user_id === request()->user()->id, 403);
    }
}
