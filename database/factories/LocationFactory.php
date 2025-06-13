<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{

    protected $model = Location::class; // This line should already be here

    public function definition(): array
    {
        $status_options = ['active', 'inactive'];
        $type_options = ['mall', 'retail_store', 'office_building', 'event_space', 'other'];

        return [
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'latitude' => $this->faker->latitude(20, 30),
            'longitude' => $this->faker->longitude(50, 60),
            'status' => $this->faker->randomElement($status_options),
            'type' => $this->faker->randomElement($type_options),
        ];
    }
}
