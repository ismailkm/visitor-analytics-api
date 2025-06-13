<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sensor;
use App\Models\Location;


class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Location::count() === 0) {
          Location::factory()->count(5)->create();
        }

        $locationids = Location::pluck('id');

        Sensor::factory()->count(40)->create([
          'location_id' => $locationids->random(),
        ]);
    }
}
