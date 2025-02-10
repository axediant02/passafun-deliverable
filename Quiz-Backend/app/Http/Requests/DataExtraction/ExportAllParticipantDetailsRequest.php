<?php

namespace App\Http\Requests\DataExtraction;

use Illuminate\Foundation\Http\FormRequest;

class ExportAllParticipantDetailsRequest extends FormRequest
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
            'selectedFields' => 'required | array',
            'dateRange' => 'nullable|array',
            'dateRange.from' => 'nullable|date_format:Y-m-d\TH:i:s.\0\0\0\Z',
            'dateRange.to' => 'nullable|date_format:Y-m-d\TH:i:s.\0\0\0\Z|after_or_equal:dateRange.from',
            'dateRange.display' => 'nullable|string',
        ];
    }
}
