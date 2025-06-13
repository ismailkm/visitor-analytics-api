<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Http\Requests\Traits\LocationCommonRules;

class UpdateLocationRequest extends FormRequest
{
    use LocationCommonRules;
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

        $locationId = $this->route('location')->id;

        // Add 'sometimes' to all common rules, as fields are optional on update
        foreach ($rules as $field => $fieldRules) {
            array_unshift($rules[$field], 'sometimes');
        }

        $rules['code'] = ['sometimes', 'string', 'max:255', Rule::unique('locations', 'code')->ignore($locationId)];

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

       $messages['code.unique'] = 'The provided location code is already in use by another location.';

       return $messages;
   }
}
