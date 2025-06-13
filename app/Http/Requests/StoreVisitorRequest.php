<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Visitor;
class StoreVisitorRequest extends FormRequest
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
            'location_id'  => ['required', 'exists:locations,id'],
            'sensor_id'    => ['nullable', 'exists:sensors,id'],
            'date'         => ['required', 'date_format:Y-m-d'],
            'hour'         => ['nullable', 'integer', 'between:0,23'],
            'in_count'     => ['nullable', 'integer', 'min:0'],
            'out_count'    => ['nullable', 'integer', 'min:0'],
            'passby_count' => ['nullable', 'integer', 'min:0'],
            'source'       => ['nullable', 'string', Rule::in(['sensor', 'manual_entry', 'api_import'])],
        ];
    }

    /**
     * Configure the validator instance.
     * This is where we add the custom unique validation for the composite key.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if ($validator->errors()->any()) {
                return;
            }

            $locationId = $this->input('location_id');
            $sensorId = $this->input('sensor_id'); // This can be null
            $date = $this->input('date');
            $hour = $this->input('hour'); // This can be null

            $exists = Visitor::where('location_id', $locationId)
                             ->where('date', $date)
                             ->when(is_null($hour), function ($query) {
                                 $query->whereNull('hour');
                             }, function ($query) use ($hour) {
                                 $query->where('hour', $hour);
                             })
                             ->when(is_null($sensorId), function ($query) {
                                 $query->whereNull('sensor_id');
                             }, function ($query) use ($sensorId) {
                                 $query->where('sensor_id', $sensorId);
                             })
                             ->exists();

            if ($exists) {
                $validator->errors()->add('unique_combination', 'A visitors record with this exact combination of location, sensor, date, and hour already exists.');
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'location_id.required'   => 'A location is required.',
            'location_id.exists'     => 'The selected location does not exist.',
            'sensor_id.exists'       => 'The selected sensor does not exist.',
            'date.required'          => 'A date is required.',
            'date.date_format'       => 'The date must be in YYYY-MM-DD format.',
            'hour.between'           => 'The hour must be between 0 and 23.',
            'in_count.min'           => 'In count cannot be negative.',
            'out_count.min'          => 'Out count cannot be negative.',
            'passby_count.min'       => 'Pass-by count cannot be negative.',
            'source.in'              => 'The source must be one of: sensor, manual_entry, api_import.',
        ];
    }
}
