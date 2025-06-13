<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Http\Requests\SensorIndexRequest;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\SensorResource;

class SensorController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(SensorIndexRequest $request): JsonResponse
    {
      try {

          $cacheKey = 'sensors:' . md5($request->fullUrlWithQuery($request->all()));
          $cacheTtl = 900; // 15 minutes in seconds

          $sensors = Cache::tags(['sensors'])->remember($cacheKey, $cacheTtl, function () use ($request) {
            $query = Sensor::with('location');

            if ($request->has('status')) {
                $query->byStatus($request->query('status'));
            }

            return $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 25));
          });

          return $this->successPaginatedResponse(
                          SensorResource::collection($sensors),
                          'Sensors retrieved successfully.'
                        );
      } catch (\Exception $e) {
          return $this->errorResponse('Error retrieving Sensors', 500, $e->getMessage());
      }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSensorRequest $request): JsonResponse
    {
      try{
        $sensor = Sensor::create($request->validated());
        $sensor->load('location');
        return $this->successResponse(
                        new SensorResource($sensor),
                        'Sensor created successfully.',
                        201
                      );
      } catch(\Exception $e) {
        return $this->errorResponse('Error creating sensor', 500, $e->getMessage());
      }
    }

    /**
     * Display the specified resource.
     * GET /api/v1/sensors/{id}
     */
    public function show(Sensor $sensor): JsonResponse
    {
      try {
          $sensor->load('location');
          return $this->successResponse(
                          new SensorResource($sensor),
                          'Sensor retrieved successfully.'
                        );
      } catch (\Exception $e) {
          return $this->errorResponse('Error retrieving sensor', 500, $e->getMessage());
      }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSensorRequest $request, Sensor $sensor): JsonResponse
    {
      try {
          $sensor->update($request->validated());
          $sensor->load('location');
          return $this->successResponse(
                          new SensorResource($sensor),
                          'Sensor updated successfully.'
                        );
      } catch (\Exception $e) {
          return $this->errorResponse('Error updating sensor', 500, $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sensor $sensor): JsonResponse
    {
      try {
          $sensor->delete();
          return $this->successResponse([], 'Sensor deleted successfully (soft deleted).', 204);
      } catch (\Exception $e) {
          return $this->errorResponse('Error deleting sensor', 500, $e->getMessage());
      }
    }
}
