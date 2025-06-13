<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetVisitorsRequest extends FormRequest
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
            'date'          => ['nullable', 'date_format:Y-m-d'],
            'location_id'   => ['nullable', 'exists:locations,id'],
            'sensor_id'     => ['nullable', 'exists:sensors,id']
        ];
    }

    /**
     * Optionally, customize validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.date_format' => 'The date must be in YYYY-MM-DD format.',
            'location_id.exists' => 'The selected location does not exist.',
            'sensor_id.exists' => 'The selected sensor does not exist.'
        ];
    }
}
