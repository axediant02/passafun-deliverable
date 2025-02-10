<?php

namespace App\Http\Requests\MechanicPage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMechanicPageRequest extends FormRequest
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
            'mechanicInstructions' => 'required|array',
            'mechanicInstructions.*.instruction' => 'required|string',
            'mechanicInstructions.*.instruction_id' => 'nullable|integer'
        ];
    }
}

