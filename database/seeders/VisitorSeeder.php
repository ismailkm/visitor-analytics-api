<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Sensor;
use App\Models\Visitor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;

class VisitorSeeder extends Seeder
{

    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $locations = Location::all();
        $allSensors = Sensor::all(); // Get all sensors once

        // Defensive check: If no locations or sensors, create a minimal set for seeding
        if ($locations->isEmpty()) {
            $this->command->info('No locations found. Creating 5 default locations.');
            Location::factory()->count(5)->create();
            $locations = Location::all();
        }

        if ($allSensors->isEmpty()) {
            $this->command->info('No sensors found. Creating 2 sensors for each location.');
            foreach ($locations as $location) {
                Sensor::factory()->count(5)->for($location)->create();
            }
            $allSensors = Sensor::all();
        }

        $startDate = Carbon::now()->subDays(60);
        $endDate = Carbon::now();

        $numberOfVisitorsToSeed = 1000;

        for ($i = 0; $i < $numberOfVisitorsToSeed; $i++) {
            // Pick a random location
            $randomLocation = $this->faker->randomElement($locations->toArray());
            $locationId = $randomLocation['id'];

            $sensorId = null;
            // Get all sensors that belong to this specific randomLocation
            $sensorsForThisLocation = $allSensors->where('location_id', $locationId);

            // Decide if we want a sensor, and if there are any sensors for this location
            // 80% chance to link to a sensor, but only if sensors exist for this location.
            if ($this->faker->boolean(80) && $sensorsForThisLocation->isNotEmpty()) {
                $sensorId = $this->faker->randomElement($sensorsForThisLocation->pluck('id')->toArray());
            }

            $randomDate = $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d');
            $randomHour = $this->faker->boolean(70) ? $this->faker->numberBetween(0, 23) : null; // 70% chance to have an hour

            $inCount = $this->faker->numberBetween(10, 300);
            $outCount = $this->faker->numberBetween(0, $inCount); // out_count <= in_count for realism
            $passbyCount = $this->faker->numberBetween(50, 1000);

            // Create the visitor record
            Visitor::factory()->create([
                'location_id' => $locationId,
                'sensor_id' => $sensorId,
                'date' => $randomDate,
                'hour' => $randomHour,
                'in_count' => $inCount,
                'out_count' => $outCount,
                'passby_count' => $passbyCount,
                'source' => $this->faker->randomElement(['sensor', 'manual_entry', 'api_import']),
            ]);
        }

        $this->command->info('Visitor table seeded successfully with accurate sensor-location relationships!');
    }
}
