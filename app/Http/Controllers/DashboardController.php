<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
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
