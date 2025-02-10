<?php

namespace App\Http\Requests\Themes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeRequest extends FormRequest
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
            'name' => 'nullable|string',
            'main_color' => 'nullable|string',
            'accent_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'button_color' => 'nullable|string',
            'background_type' => 'nullable|in:color,image',
            'background_value' => 'required_if:background_type,image|nullable|max:2048',
        ];
    }
}
