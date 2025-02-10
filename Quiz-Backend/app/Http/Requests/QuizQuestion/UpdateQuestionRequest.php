<?php

namespace App\Http\Requests\QuizQuestion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
        $questionId = $this->route('id');
        $existingImage = \DB::table('quiz_questions')->where('id', $questionId)->value('question_image');

        return [
            'question_type_id' => 'required|integer',
           'question_text' => [
            'nullable',
            'string',
            function ($attribute, $value, $fail) use ($existingImage) {
                if (empty($value) && !$this->hasFile('question_image') && !$existingImage) {
                    $fail('The question text or image is required.');
                }
            },
        ],
            'question_image' => 'nullable|image|max:2048',
            'question_image_removed' => 'boolean|nullable',
            'choices' => 'array|nullable',
            'choices.*.choice_text' => 'string|nullable',
            'choices.*.choice_image' => 'nullable|image|max:2048',
            'choices.*.choice_image_removed' => 'boolean|nullable',
            'choices.*.points' => 'numeric|min:0|nullable',
            'choices.*.is_correct' => 'boolean|nullable',
        ];

        if ($this->hasFile('question_image') && !$this->boolean('question_image_removed')) {
            $rules['question_image'] = 'image|max:2048';
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->has('question_type_id')) {
            $this->merge([
                'question_type_id' => (int)$this->input('question_type_id')
            ]);
        }

        if ($this->boolean('question_image_removed')) {
            $this->merge(['question_image' => null]);
        }

        if ($this->has('choices')) {
            $choices = $this->input('choices');
            foreach ($choices as $index => $choice) {
                if (isset($choice['choice_image_removed']) && $choice['choice_image_removed']) {
                    $choices[$index]['choice_image'] = null;
                }
            }
            $this->merge(['choices' => $choices]);
        }
    }

    public function messages()
    {
        return [
            'question_type_id.required' => 'The question type is required.',
            'question_text.required_without' => 'The question text is required when there is no image.',
            'question_image.image' => 'The question image must be a valid image file.',
            'question_image.max' => 'The question image must not be larger than 2MB.',
            'choices.*.choice_text.string' => 'The choice text must be a string.',
            // 'choices.*.points.integer' => 'The points must be a number.',
            'choices.*.points.min' => 'The points must be at least 0.',
        ];
    }
}
