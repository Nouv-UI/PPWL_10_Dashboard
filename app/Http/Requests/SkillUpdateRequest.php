<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SkillUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('skill')?->user_id;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('skills', 'name')
                    ->ignore($this->route('skill')?->id)
                    ->where(fn ($query) => $query->where('user_id', $this->user()?->id)),
            ],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
