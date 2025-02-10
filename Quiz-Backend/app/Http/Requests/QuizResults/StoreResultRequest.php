<?php

namespace App\Http\Requests\QuizResults;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'header' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'financial_tips' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'min_points' => 'nullable|numeric',
            'max_points' => 'nullable|numeric',
        ];
    }
}
