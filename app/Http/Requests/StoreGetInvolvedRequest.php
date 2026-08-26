<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGetInvolvedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'initiative_id' => 'nullable|exists:initiatives,id',
            'message' => 'nullable|string|max:2000',
        ];
    }
}
