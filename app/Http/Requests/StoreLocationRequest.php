<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Http\Requests\Traits\LocationCommonRules;

class StoreLocationRequest extends FormRequest
{
    use LocationCommonRules;

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
        $rules['code'] = ['required', 'string', 'max:255', 'unique:locations,code'];
        $rules['status'] = ['required', ...$rules['status']];
        return $rules;
    }

    /**
     * Custom error messages for specific rules.
     * @return array
     */
    public function messages(): array
    {
        $messages = $this->commonMessages();
        $messages['code.unique'] = 'The provided location code is already in use. Please choose a different one.';
        $messages['name.required'] = 'The location name is required.';
        $messages['status.required'] = 'The status is required.';

        return $messages;
    }
}
