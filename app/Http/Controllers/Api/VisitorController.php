<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Http\Requests\GetVisitorsRequest;
use App\Http\Requests\StoreVisitorRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\VisitorResource;


class VisitorController extends Controller
{
    use ApiResponse;
    /**
    * Display a listing of visitor records.
    * Allows filtering by date, location and sensor with eager loading location/sensor details.
    */
    public function index(GetVisitorsRequest $request): JsonResponse
    {
        try{

            $cacheKey = 'visitors:' . md5($request->fullUrlWithQuery($request->all()));
            $cacheTtl = 900; // 15 minutes in seconds

            $visitors = Cache::tags(['visitors'])->remember($cacheKey, $cacheTtl, function () use ($request) {
              $query = Visitor::query();

              if ($request->has('date')) {
                  $query->byDate($request->input('date'));
              }

              if ($request->has('location_id')) {
                  $query->byLocation($request->input('location_id'));
              }

              if ($request->has('sensor_id')) {
                  $query->bySensor($request->input('sensor_id'));
              }

              $query->withLocationAndSensor();

              return $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 25));
            });

            return $this->successPaginatedResponse(
                             VisitorResource::collection($visitors),
                            'Visitors logs retrieved successfully.'
                          );
        }catch (\Exception $e) {
            return $this->errorResponse('Error retrieving vistors log', 500, $e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisitorRequest $request): JsonResponse
    {
        try{
          $visitor = Visitor::create($request->validated());
          $visitor->load(['location', 'sensor']);
          return $this->successResponse(
                          new VisitorResource($visitor),
                          'Visitors log has been created successfully.',
                          201
                        );
        } catch(\Exception $e) {
          return $this->errorResponse('Error creating visitors log', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor): JsonResponse
    {
        try {
            $visitor->load(['location', 'sensor']);
            return $this->successResponse(
                            new VisitorResource($visitor),
                            'Visitors log retrieved successfully.'
                          );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving visitors log', 500, $e->getMessage());
        }
    }
}
