<?php

namespace App\Http\Requests\GetResultPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGetResultPageRequest extends FormRequest
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
            'header' => 'sometimes|string|nullable',
            'buttonText' => 'sometimes|string|nullable',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048|nullable',
            'jsonFile' => 'sometimes|file|nullable',
            'imageType' => 'sometimes|string',
            'backgroundImage' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'inputForms' => 'sometimes|array',
            'inputForms.*.id' => 'required|integer',
            'inputForms.*.get_result_page_id' => 'required|integer',
            'inputForms.*.type' => 'required|string',
            'inputForms.*.label' => 'required|string',
            'inputForms.*.is_required' => 'required|boolean',
        ];
    }
}
