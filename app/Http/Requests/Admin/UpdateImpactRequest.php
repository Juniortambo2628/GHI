<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImpactRequest extends FormRequest
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
            'event_id' => 'nullable|exists:events,id',
            'people_affected' => 'nullable|integer|min:0',
            'outcome_summary' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0',
            'metric_type' => 'nullable|string|max:50',
            'metric_value' => 'nullable|numeric',
            'activity_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'featured' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
        ];
    }
}
