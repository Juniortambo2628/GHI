<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'initiative_id' => 'nullable|exists:initiatives,id',
            'status' => 'required|in:draft,published,archived',
            'images' => 'nullable|array',
            'images.*.path' => 'required|string|max:255',
            'images.*.sort_order' => 'required|integer|min:0',
            'images.*.type' => 'nullable|string|in:image,video',
            'images.*.id' => 'nullable',
        ];
    }
}
