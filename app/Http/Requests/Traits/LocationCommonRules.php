<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait LocationCommonRules
{

  /**
    * Get the common validation rules for location data.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */
  protected function commonRules(): array
  {
    return[
      'name' => ['string', 'max:255'],
      'address' => ['nullable', 'string', 'max:255'],
      'city' => ['nullable', 'string', 'max:255'],
      'latitude' => ['nullable', 'numeric', 'between:-90,90'],
      'longitude' => ['nullable', 'numeric', 'between:-180,180'],
      'status' => [Rule::in(['active', 'inactive'])],
      'type' => ['nullable', Rule::in(['mall', 'retail_store', 'office_building', 'event_space', 'other'])],
    ];
  }

  /**
   * Get the custom validation messages for common location data.
   *
   * @return array
   */
  protected function commonMessages(): array
  {
    return[
      'status.in' => 'The selected status is invalid. Please choose from: active, inactive.',
      'type.in' => 'The selected type is invalid. Please choose from: mall, retail_store, office_building, event_space, other.',
      'latitude.between' => 'Latitude must be between -90 and 90.',
      'longitude.between' => 'Longitude must be between -180 and 180.',
    ];
  }
}
