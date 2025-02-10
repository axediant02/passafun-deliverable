<?php

namespace App\Http\Requests\Choices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChoiceRequest extends FormRequest
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
            'question_id' => 'required|exists:quiz_questions,id',
            'choice_text' => 'nullable|string|max:255',
            'choice_image' => 'nullable|string',
            'points' => 'required|numeric|min:0|max:5',
            'is_correct' => 'required|boolean',
        ];
    }
}
