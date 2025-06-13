<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Location;

class LocationApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_can_list_all_locations_with_correct_structure_and_pagination(): void
    {
        Location::factory()->count(15)->create(); // Create enough to test pagination
        $response = $this->getJson('/api/v1/locations');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'code',
                             'address',
                             'city',
                             'latitude',
                             'longitude',
                             'status',
                             'type',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'Locations retrieved successfully.'
                 ])
                 ->assertJsonCount(15, 'data');
    }

    /** @test */
    public function it_can_create_a_new_location(): void
    {
       // Arrange: Define new location data, including the new fields
       $locationData = [
           'name' => 'New Test Building',
           'code' => 'TEST001',
           'address' => '456 Example Ave',
           'city' => 'Sampleton',
           'latitude' => 40.1234,
           'longitude' => -74.5678,
           'status' => 'active',
           'type' => 'office_building',
       ];

       // Act: Make a POST request to the store endpoint
       $response = $this->postJson('/api/v1/locations', $locationData);

       // Assert: Check the response status, data, and database record
       $response->assertStatus(201) // Expect HTTP 201 Created
                ->assertJson([
                    'success' => true,
                    'message' => 'Location created successfully.',
                    'data' => $locationData // Asserts that response data matches input (or subset)
                ])
                ->assertJsonStructure([
                    'success', 'message', 'data' => [
                        'id', 'name', 'code', 'address', 'city', 'latitude', 'longitude', 'status', 'type', 'created_at', 'updated_at'
                    ]
                ]);

       // Assert the location exists in the database
       $this->assertDatabaseHas('locations', [
           'name' => 'New Test Building',
           'code' => 'TEST001',
           'address' => '456 Example Ave',
           'city' => 'Sampleton',
           'latitude' => 40.1234,
           'longitude' => -74.5678,
           'status' => 'active',
           'type' => 'office_building',
       ]);
    }

    /** @test */
    public function it_returns_validation_errors_when_creating_with_invalid_data(): void
    {
        Location::factory()->create(['code' => 'TEST001']);

        $response = $this->postJson('/api/v1/locations', [
            'name' => '',               // Required, will trigger validation error
            'code' => 'TEST001',        // Duplicate, will trigger unique error
            'status' => 'active',
        ]);

        // Assert: Check HTTP status
        $response->assertStatus(422);

        // Assert structure of the JSON response
        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                '*' => ['field', 'message'],
            ],
        ]);

        // Assert static values
        $response->assertJson(['success' => false]);

        // Assert dynamic message (contains "error" keyword, case-insensitive)
        $response->assertJsonPath('message', fn ($msg) => str_contains(strtolower($msg), 'error'));

        // Assert specific validation error fragments (more flexible than full array match)
        $response->assertJsonFragment([
            'field' => 'name',
            'message' => 'The location name is required.',
        ]);

        $response->assertJsonFragment([
            'field' => 'code',
            'message' => 'The provided location code is already in use. Please choose a different one.',
        ]);

        // Optionally assert number of validation errors (if you want strict checks)
        $this->assertCount(2, $response->json('errors'));
    }

    /** @test */
    public function it_can_show_a_specific_location(): void
    {
        // Arrange: Create a specific location
        $location = Location::factory()->create([
            'name' => 'Specific Test Location',
            'code' => 'SPEC001',
            'address' => '789 Central Blvd',
            'city' => 'Central City',
            'status' => 'active',
            'type' => 'office_building',
            'latitude' => 40.1234,
            'longitude' => -74.5678,
        ]);

        // Act: Make a GET request to the show endpoint
        $response = $this->getJson('/api/v1/locations/' . $location->id);

        // Assert: Check status, structure, and data
        $response->assertStatus(200) // Expect HTTP 200 OK
                 ->assertJson([
                     'success' => true,
                     'message' => 'Location retrieved successfully.',
                     'data' => [
                         'id' => $location->id,
                         'name' => 'Specific Test Location',
                         'code' => 'SPEC001',
                         'address' => '789 Central Blvd',
                         'city' => 'Central City',
                         'latitude' => (float)$location->latitude, // Ensure type consistency
                         'longitude' => (float)$location->longitude, // Ensure type consistency
                         'status' => 'active',
                         'type' => 'office_building',
                     ]
                 ])
                 ->assertJsonStructure([
                     'success', 'message', 'data' => [
                         'id', 'name', 'code', 'address', 'city', 'latitude', 'longitude', 'status', 'type', 'created_at', 'updated_at'
                     ]
                 ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_location_when_showing(): void
    {
        $nonExistentId = 999;

        $response = $this->getJson("/api/v1/locations/{$nonExistentId}"); // Use the defined ID

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => "The requested endpoint '/api/v1/locations/{$nonExistentId}' was not found."
                 ])
                 ->assertJsonStructure([
                     'success', 'message'
                 ]);
    }

    /** @test */
    public function it_can_update_an_existing_location(): void
    {
       $originalLocation = Location::factory()->create([
           'name' => 'Old Name',
           'code' => 'OLDCODE',
           'address' => '123 Original St',
           'city' => 'Original City',
           'latitude' => 10.0,
           'longitude' => 20.0,
           'status' => 'active',
           'type' => 'mall',
       ]);

       $newData = [
           'name' => 'Updated Location Name',
           'code' => 'NEWCODE',
           'address' => '456 New Blvd',
           'city' => 'New City',
           'latitude' => 30.5,
           'longitude' => 40.5,
           'status' => 'inactive',
           'type' => 'event_space',
       ];


       $response = $this->putJson("/api/v1/locations/{$originalLocation->id}", $newData);

       $response->assertStatus(200);

       $response->assertJsonStructure([
           'success',
           'message',
           'data' => [
               'id', 'name', 'code', 'address', 'city',
               'latitude', 'longitude', 'status', 'type',
               'created_at', 'updated_at'
           ]
       ]);

       $response->assertJson([
           'success' => true,
           'message' => 'Location updated successfully.',
           'data' => array_merge(['id' => $originalLocation->id], $newData)
       ]);

       $this->assertDatabaseHas('locations', array_merge(['id' => $originalLocation->id], $newData));

       $this->assertDatabaseMissing('locations', [
           'id' => $originalLocation->id,
           'name' => 'Old Name',
           'code' => 'OLDCODE',
       ]);
   }

    /** @test */
    public function it_returns_404_if_location_not_found_when_updating(): void
    {
        $nonExistentId = 9999;

        $updateData = [
            'name' => 'Attempted Update Name',
            'code' => 'ATTEMPT',
            'address' => 'Some address',
            'city' => 'Some city',
            'latitude' => 10.0,
            'longitude' => 20.0,
            'status' => 'active',
            'type' => 'office',
        ];

        $response = $this->putJson("/api/v1/locations/{$nonExistentId}", $updateData);

        $response->assertStatus(404);

        $response->assertJsonStructure([
            'success',
            'message'
        ]);

        $response->assertJson([
            'success' => false,
            'message' => "The requested endpoint '/api/v1/locations/{$nonExistentId}' was not found."
        ]);

        $this->assertDatabaseMissing('locations', $updateData);
    }

    /** @test */
    public function it_returns_validation_errors_when_updating_with_invalid_data(): void
    {
        Location::factory()->create(['code' => 'CONFLICT']);

        $existingLocation = Location::factory()->create([
            'name' => 'Original Name',
            'code' => 'ORIGINAL',
            'address' => 'Original Address',
            'city' => 'Original City',
            'latitude' => 40.0,
            'longitude' => -70.0,
            'status' => 'active',
            'type' => 'mall',
        ]);

        // Define invalid data for the update attempt
        $invalidData = [
            'name' => ' ',                   // Invalid: Empty string (required)
            'code' => 'CONFLICT',            // Invalid: Duplicate code
            'address' => 'New Address',
            'city' => 'New City',
            'latitude' => 100.00,           // Invalid: Outside -90 to 90 range
            'longitude' => -200.00,         // Invalid: Outside -180 to 180 range
            'status' => 'pending',          // Invalid: Assuming 'status' is 'in:active,inactive'
            'type' => 'event_space',
        ];


        $response = $this->putJson("/api/v1/locations/{$existingLocation->id}", $invalidData);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'success',
            'message',
            'errors' => [
                '*' => ['field', 'message'],
            ],
        ]);

        $response->assertJson(['success' => false]);

        $response->assertJsonPath('message', fn ($msg) => str_contains(strtolower($msg), 'error'));

        $response->assertJsonFragment([
            'field' => 'name',
            'message' => 'The name field must be a string.',
        ]);
        $response->assertJsonFragment([
            'field' => 'code',
            'message' => 'The provided location code is already in use by another location.',
        ]);
        $response->assertJsonFragment([
            'field' => 'latitude',
            'message' => 'Latitude must be between -90 and 90.',
        ]);
        $response->assertJsonFragment([
            'field' => 'longitude',
            'message' => 'Longitude must be between -180 and 180.',
        ]);
        $response->assertJsonFragment([
            'field' => 'status',
            'message' => 'The selected status is invalid. Please choose from: active, inactive.',
        ]);

        $this->assertCount(5, $response->json('errors'));

        $this->assertDatabaseHas('locations', [
            'id' => $existingLocation->id,
            'name' => 'Original Name',
            'code' => 'ORIGINAL',
            'address' => 'Original Address',
            'city' => 'Original City',
            'latitude' => 40.0,
            'longitude' => -70.0,
            'status' => 'active',
            'type' => 'mall',
        ]);
    }
}
