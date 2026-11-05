<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryUploadRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = $request->user()->projects()
            ->with(['skills', 'galleries'])
            ->latest()
            ->paginate(9);

        return view('post.projects.index', compact('projects'));
    }

    public function edit(Project $project): View
    {
        $this->ensureOwner($project);

        $project->load(['skills', 'galleries']);
        $skills = request()->user()->skills()->orderBy('category')->orderBy('name')->get();

        return view('post.projects.edit', compact('project', 'skills'));
    }

    public function update(ProjectUpdateRequest $request, Project $project): RedirectResponse
    {
        $oldThumbnail = $project->thumbnail;
        $data = $request->safe()->except(['thumbnail', 'skill_ids']);

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
            $project->galleries()->create([
                'image_path' => $image->store('projects/gallery', 'public'),
            ]);
        }

        return back()->with('success', 'Gallery berhasil ditambahkan.');
    }

    private function ensureOwner(Project $project): void
    {
        abort_unless($project->user_id === request()->user()->id, 403);
    }
}
