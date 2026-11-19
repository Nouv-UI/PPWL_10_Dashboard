<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function projects(): View
    {
        $projects = Project::query()
            ->where('status', 'published')
            ->with('category')
            ->latest()
            ->get();

        return view('projects', compact('projects'));
    }

    public function show(Project $project): View
    {
        abort_unless($project->status === 'published', 404);
        $project->load(['category', 'images']);

        return view('portfolio.project', compact('project'));
    }
}
