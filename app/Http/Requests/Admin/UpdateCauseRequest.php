<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCauseRequest extends FormRequest
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
            'quote' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'status' => 'required|in:draft,published,archived',
        ];
    }
}
