<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location_id' => $this->location_id,
            'sensor_id' => $this->sensor_id,
            'date' => $this->date->format('Y-m-d'),
            'hour' => $this->hour,
            'in_count' => $this->in_count,
            'out_count' => $this->out_count,
            'passby_count' => $this->passby_count,
            'source' => $this->source,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'deleted_at' => $this->whenNotNull($this->deleted_at),

            'location' => $this->whenLoaded('location', function () {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->name,
                    'code' => $this->location->code,
                    'city' => $this->location->city,
                ];
            }),
            'sensor' => $this->whenLoaded('sensor', function () {
                return [
                    'id' => $this->sensor->id,
                    'name' => $this->sensor->name,
                    'type' => $this->sensor->type,
                    'code' => $this->sensor->code,
                ];
            }),
        ];
    }
}
