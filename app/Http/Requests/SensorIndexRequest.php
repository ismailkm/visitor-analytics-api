<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\SensorCommonRules;


class SensorIndexRequest extends FormRequest
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
       return [
           'status' => ['sometimes', 'string', ...$this->commonRules()['status']],
       ];
    }

    /**
     * Optionally, customize messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return $this->commonMessages();
    }
}
