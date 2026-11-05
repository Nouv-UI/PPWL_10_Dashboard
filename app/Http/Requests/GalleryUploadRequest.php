<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('project')?->user_id;
    }

    public function rules(): array
    {
        $remaining = max(0, 10 - $this->route('project')->galleries()->count());

        return [
            'images' => ['required', 'array', 'min:1', 'max:' . $remaining],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6800', 'dimensions:min_width=800'],
        ];
    }
}
