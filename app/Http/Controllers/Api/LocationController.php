<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
      try {
          $locations = Location::orderBy('created_at', 'desc')->paginate($request->get('per_page', 25));
          return $this->successPaginatedResponse(
                           LocationResource::collection($locations),
                          'Locations retrieved successfully.'
                        );
      } catch (\Exception $e) {
          return $this->errorResponse('Error retrieving locations', 500, $e->getMessage());
      }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        try{
          $location = Location::create($request->validated());
          return $this->successResponse(
                          new LocationResource($location),
                          'Location created successfully.',
                          201
                        );
        } catch(\Exception $e) {
          return $this->errorResponse('Error creating location', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * GET /api/v1/locations/{location}
     */
    public function show(Location $location): JsonResponse
    {
        try {
            return $this->successResponse(
                            new LocationResource($location),
                            'Location retrieved successfully.'
                          );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving location', 500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
      try {
          $location->update($request->validated());
          return $this->successResponse(
                          new LocationResource($location),
                          'Location updated successfully.'
                        );
      } catch (\Exception $e) {
          return $this->errorResponse('Error updating location', 500, $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/v1/locations/{location}
     */
    public function destroy(Location $location): JsonResponse
    {
        try {
            $location->delete();
            return $this->successResponse([], 'Location deleted successfully (soft deleted).', 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting location', 500, $e->getMessage());
        }
    }
}
