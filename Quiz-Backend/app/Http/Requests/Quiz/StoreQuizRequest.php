<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->has('getResultInputForms')) {
            $forms = $this->get('getResultInputForms');
            $forms = array_map(function ($item) {
                if (isset ($item['is_required'])) {
                    $item['is_required'] = filter_var($item['is_required'], FILTER_VALIDATE_BOOLEAN);
                }
                return $item;
            }, $forms);
            $this->merge(['getResultInputForms' => $forms]);
        }
    }

     
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:400',
            'selectedTheme' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'coverImage' => 'nullable|image|max:2048',
            'shareThumbnailImage' => 'nullable|image|max:2048',
            'landingSubheader' => 'nullable|string|max:255',
            'mechanicsInstruction' => 'nullable|array',
            'mechanicsInstruction.*' => 'nullable|string|max:1000',
            'getResultHeader' => 'nullable|string',
            'getResultButtonText' => 'nullable|string',
            'getResultInputForms.*.label' => 'nullable|string',
            'getResultInputForms.*.is_required' => 'nullable|boolean',
            'getResultInputForms.*.type' => 'nullable|string',
            'getResultFileType' => 'nullable|string',
            'getResultImage' => 'nullable|image|max:2048',
            'getResultLottieJson' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    try {
                        $content = file_get_contents($value->getRealPath());
                        $json = json_decode($content, true);
                        if (!is_array($json) || !isset($json['v']) || !isset($json['fr']) || !isset($json['layers'])) {
                            $fail("The LottieJson must be a valid Lottie JSON file.");
                        }
                    } catch (\Exception $e) {
                        $fail("The LottieJson must be a valid Lottie JSON file.");
                    }
                },
            ],
            'getResultBackgroundImage' => 'nullable|image|max:2048'

        ];
    }
}
