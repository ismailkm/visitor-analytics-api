<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\SensorCommonRules;

class StoreSensorRequest extends FormRequest
{
    use SensorCommonRules;
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
        $rules = $this->commonRules();
        $rules['name'] = ['required', ...$rules['name']];
        $rules['code'] = ['required', 'string', 'max:255', 'unique:sensors,code'];
        $rules['status'] = ['required', ...$rules['status']];
        $rules['type'] = ['required', ...$rules['type']];

        return $rules;
    }

    /**
     * Optionally, customize messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        $messages = $this->commonMessages();
        $messages['code.unique'] = 'A sensor with this code already exists.';
        $messages['name.required'] = 'The Sensor name is required.';
        $messages['status.required'] = 'The status is required.';
        $messages['type.required'] = 'The type of sensor is required.';

        return $messages;
    }
}
