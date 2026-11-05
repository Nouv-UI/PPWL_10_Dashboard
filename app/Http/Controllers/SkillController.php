<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillUpdateRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function edit(Skill $skill): View
    {
        $this->ensureOwner($skill);

        return view('post.skills.edit', compact('skill'));
    }

    public function update(SkillUpdateRequest $request, Skill $skill): RedirectResponse
    {
        $skill->update($request->validated());

        return to_route('post.skills.edit', $skill)->with('success', 'Skill berhasil diperbarui.');
    }

    private function ensureOwner(Skill $skill): void
    {
        abort_unless($skill->user_id === request()->user()->id, 403);
    }
}
