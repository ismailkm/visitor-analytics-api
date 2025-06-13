<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Visitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location_id',
        'sensor_id',
        'date',
        'hour',
        'in_count',
        'out_count',
        'passby_count',
        'source',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'hour' => 'integer',
        'in_count' => 'integer',
        'out_count' => 'integer',
        'passby_count' => 'integer',
    ];

    /**
     * Get the location that owns the visitor record.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the sensor that recorded the visitor data.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }

    /**
     * Scope a query to include the related location and sensor.
     */
    public function scopeWithLocationAndSensor(Builder $query): void
    {
        $query->with(['location', 'sensor']);
    }

    /**
     * Scope a query to filter records by location.
     */
    public function scopeByLocation(Builder $query, int $locationId): void
    {
        $query->where('location_id', $locationId);
    }

    /**
     * Scope a query to filter records by sensor.
     */
    public function scopeBySensor(Builder $query, int $sensorId): void
    {
        $query->where('sensor_id', $sensorId);
    }

    /**
     * Scope a query to filter records by a specific date.
     */
    public function scopeByDate(Builder $query, string $date): void
    {
        $query->whereDate('date', $date);
    }

}
