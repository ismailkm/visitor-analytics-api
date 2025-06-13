<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\SensorCommonRules;

class UpdateSensorRequest extends FormRequest
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

        $sensorId = $this->route('sensor')->id;

        foreach ($rules as $field => $fieldRules) {
            array_unshift($rules[$field], 'sometimes');
        }

        $rules['code'] = ['sometimes', 'string', 'max:255', Rule::unique('sensors', 'code')->ignore($sensorId)];

        return $rules;
    }

    /**
    * Custom error messages for specific rules.
    *
    * @return array
    */
    public function messages(): array
    {
       $messages = $this->commonMessages();

       $messages['code.unique'] = 'This code has been already used for another sensor';

       return $messages;
   }
}
