<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryCaptionRequest;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function updateCaption(GalleryCaptionRequest $request, Gallery $gallery): RedirectResponse
    {
        $gallery->update($request->validated());

        return back()->with('success', 'Caption gallery berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        abort_unless($gallery->project->user_id === request()->user()->id, 403);

        Storage::disk('public')->delete($gallery->image_path);
        $gallery->delete();

        return back()->with('success', 'Gambar gallery berhasil dihapus.');
    }
}
