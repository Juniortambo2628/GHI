<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInitiativeRequest extends FormRequest
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
            'category' => 'required|string|max:50',
            'cause_ids' => 'nullable|array',
            'cause_ids.*' => 'exists:causes,id',
            'status' => 'required|in:draft,published,archived',
        ];
    }
}
