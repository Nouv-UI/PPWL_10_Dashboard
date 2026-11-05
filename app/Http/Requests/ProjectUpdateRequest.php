<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('project')?->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => [
                'integer',
                Rule::exists('skills', 'id')->where(fn ($query) => $query->where('user_id', $this->user()?->id)),
            ],
        ];
    }
}
