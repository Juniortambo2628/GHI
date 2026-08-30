<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'featured_image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
            'status' => 'required|in:draft,published,archived',
            'event_id' => 'nullable|exists:events,id',
        ];
    }
}
