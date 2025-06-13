<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Sensor;
use App\Models\Location;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
{
    protected $model = Sensor::class;

    public function definition(): array
    {
        $sensorTypes = ['camera', 'wifi', 'beacon','other'];
        $sensorStatuses = ['active', 'inactive', 'maintenance', 'error'];

        return [
          'name' => $this->faker->words(3, true) . ' Sensor',
          'code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{4}'),
          'type' => $this->faker->randomElement($sensorTypes),
          'status' => $this->faker->randomElement($sensorStatuses),
          'location_id' => Location::factory(),
          'installed_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }

}
