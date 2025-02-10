<?php

namespace App\Http\Requests\QuizQuestion;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'question_type_id' => 'required|integer',
            'question_text' => 'nullable|string',
            'question_image' => 'nullable|image|max:2048',
            'question_order' => 'nullable|integer',
            'choices.*.choice_text' => 'nullable|string',
            'choices.*.choice_image' => 'nullable|image|max:2048',
            'choices.*.points' => 'nullable|numeric|min:0|max:5',
            'choices.*.is_correct' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'question_type_id.required' => 'The question type is required.',
            'question_image.image' => 'The question image must be a valid image file.',
            'choices.*.choice_text.required' => 'Each choice must have text.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->hasFile('question_image')) {
            $this->merge([
                'question_image' => $this->storeImage($this->file('question_image'), 'question-images')
            ]);
        }

        if ($this->hasFile('choices.*.choice_image')) {
            $choices = $this->input('choices', []);

            foreach ($choices as $index => $choice) {
                if ($this->hasFile('choices.$index.choice_image')) {
                    $choices[$index]['choice_image'] = $this->storeImage($this->file('choices.$index.choice_image'), 'choice-images');
                }
            }

            $this->merge([
                'choices' => $choices
            ]);
        }
    }

    private function storeImage($file, $folder)
    {
        $originalName = $file->getClientOriginalName();
        return $file->storeAs($folder, $originalName, [
            'disk' => 's3',
            'visibility' => 'private',
        ]);
    }
}



