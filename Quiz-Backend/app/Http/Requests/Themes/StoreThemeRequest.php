<?php

namespace App\Http\Requests\Themes;

use Illuminate\Foundation\Http\FormRequest;

class StoreThemeRequest extends FormRequest
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
            'name' => 'required|string',
            'main_color' => 'required|string',
            'accent_color' => 'required|string',
            'text_color' => 'required|string',
            'button_color' => 'required|string',
            'background_type' => 'required|in:color,image',
            'background_value' => 'required_if:background_type,image|nullable|max:2048',
        ];
    }
}
