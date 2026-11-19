<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Technology;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'totalProjects' => $user->projects()->count(),
            'totalCategories' => Category::count(),
            'totalTechnologies' => Technology::count(),
            'totalSkills' => $user->skills()->count(),
            'latestProjects' => $user->projects()->with('category')->latest()->take(5)->get(),
            'publishedCount' => $user->projects()->where('status', 'published')->count(),
            'draftCount' => $user->projects()->where('status', 'draft')->count(),
            'topSkills' => $user->skills()
                ->withCount(['projects' => fn ($query) => $query->where('user_id', $user->id)])
                ->orderByDesc('projects_count')
                ->orderBy('name')
                ->take(5)
                ->get(),
        ]);
    }
}
