<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait SensorCommonRules
{
    /**
     * Get the common validation rules for sensor data.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function commonRules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'type' => [Rule::in(['camera', 'wifi', 'beacon', 'other'])],
            'status' => [Rule::in(['active', 'inactive', 'maintenance', 'error'])],
            'location_id' => ['required', 'exists:locations,id'],
            'installed_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the custom validation messages for common sensor data.
     *
     * @return array
     */
    protected function commonMessages(): array
    {
        return [
            'type.in' => 'The sensor type must be one of: camera, wifi, beacon, or other.',
            'status.in' => 'The sensor status must be one of: active, inactive, maintenance, or error.',
            'location_id.exists' => 'The selected location does not exist.',
            'installed_at.date' => 'The installed at field must be a valid date.',
        ];
    }
}
