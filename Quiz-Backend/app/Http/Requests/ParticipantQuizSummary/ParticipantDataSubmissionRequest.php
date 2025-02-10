<?php

namespace App\Http\Requests\ParticipantQuizSummary;

use Illuminate\Foundation\Http\FormRequest;

class ParticipantDataSubmissionRequest extends FormRequest
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
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'participant_score' => 'required|integer|min:0',
            'unique_result_id' => 'required|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:quiz_questions,id',
            'answers.*.choice_id.*' => 'nullable|integer|exists:choices,id',
            'answers.*.open_ended_response' => 'nullable|string|max:1000',
        ];
    }
}
