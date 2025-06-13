<?php

namespace Tests\Unit;

use Tests\TestCase; 
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_location()
    {
        $data = [
            'name' => 'Test Location',
            'code' => 'LOC12345',
            'address' => '123 Test St',
            'city' => 'Testville',
            'latitude' => 25.12345,
            'longitude' => 55.12345,
            'status' => 'active',
            'type' => 'mall',
        ];

        $location = Location::create($data);

        $this->assertDatabaseHas('locations', ['code' => 'LOC12345']);
        $this->assertEquals('Test Location', $location->name);
        $this->assertEquals('active', $location->status);
    }

    /** @test */
    public function it_has_expected_fillable_fields()
    {
        $location = new Location();

        $expectedFillable = [
            'name',
            'code',
            'address',
            'city',
            'latitude',
            'longitude',
            'status',
            'type',
        ];

        $this->assertEquals($expectedFillable, $location->getFillable());
    }

    /** @test */
    public function it_casts_latitude_and_longitude_as_float()
    {
        $location = Location::factory()->make([
            'latitude' => '25.123456',
            'longitude' => '55.654321',
        ]);

        $this->assertIsFloat($location->latitude);
        $this->assertIsFloat($location->longitude);
    }

    /** @test */
    public function it_returns_only_active_locations_using_scope()
    {
        Location::factory()->create(['status' => 'active']);
        Location::factory()->create(['status' => 'inactive']);

        $activeLocations = Location::active()->get();

        $this->assertCount(1, $activeLocations);
        $this->assertEquals('active', $activeLocations->first()->status);
    }
}
