<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryCaptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $gallery = $this->route('gallery');

        return $this->user()?->id === $gallery?->project?->user_id;
    }

    public function rules(): array
    {
        return [
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
