<?php

namespace Database\Factories;

use App\Models\Visitor;
use App\Models\Location;
use App\Models\Sensor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitor>
 */
class VisitorFactory extends Factory
{

    protected $model = Visitor::class;

    public function definition(): array
    {
        $location = Location::factory()->create();
        $sensor = Sensor::factory()->for($location)->create();
        
        $inCount = $this->faker->numberBetween(50, 500);
        $outCount = $this->faker->numberBetween(0, $inCount);
        $passbyCount = $this->faker->numberBetween(10, 1000);

        $date = $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d');

        return [
            'location_id' => $location->id,
            'sensor_id' => $sensor ? $sensor->id : null,
            'date' => $date,
            'hour' => $this->faker->optional(0.7)->numberBetween(0, 23),
            'in_count' => $inCount,
            'out_count' => $outCount,
            'passby_count' => $passbyCount,
            'source' => $this->faker->randomElement(['sensor', 'manual_entry', 'api_import']),
        ];
    }
}
