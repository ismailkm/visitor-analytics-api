<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Sensor;
use App\Http\Traits\ApiResponse;

class SummaryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $cacheKey = 'app:summary';
        $cacheTtl = 900; // 15 minutes in seconds

        $summaryData = Cache::remember($cacheKey, $cacheTtl, function () {
          $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();

          $visitorStats = Visitor::where('date', '>=', $sevenDaysAgo)
              ->selectRaw('
                  SUM(in_count) as total_in,
                  SUM(out_count) as total_out,
                  SUM(passby_count) as total_passby
              ')
              ->first();

          $totalIn = (int) $visitorStats->total_in;
          $totalOut = (int) $visitorStats->total_out;
          $totalPassby = (int) $visitorStats->total_passby;
          $totalVisitors = $totalIn + $totalOut + $totalPassby;

          // Sensor status counts
          $activeSensors = Sensor::activeOnly()->count();
          $inactiveSensors = Sensor::inactiveOnly()->count();

          return [
              'visitors_summary' => [
                  'in_count' => $totalIn,
                  'out_count' => $totalOut,
                  'passby_count' => $totalPassby,
                  'total_count' => $totalVisitors
              ],
              'sensors' => [
                  'active' => $activeSensors,
                  'inactive' => $inactiveSensors,
              ]
          ];
        });


        return $this->successResponse(
                        $summaryData,
                        'Summary retrieved successfully.'
                      );
    }

}
