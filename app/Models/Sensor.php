<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Sensor extends Model
{
     use HasFactory;

     protected $fillable = [
         'name',
         'code',
         'type',
         'status',
         'location_id',
         'installed_at',
     ];

     protected $casts = [
         'installed_at' => 'datetime',
     ];

     /**
      * Relationship: A sensor belongs to a location.
      */
     public function location(): BelongsTo
     {
         return $this->belongsTo(Location::class);
     }

     /**
      * Scope a query to filter records by location.
      */
     public function scopeByLocation(Builder $query, int $locationId): void
     {
         $query->where('location_id', $locationId);
     }
     
     /**
      * Scope a query to return only active .
      */
     public function scopeActiveOnly(Builder $query): void
     {
         $query->where('status', 'active');
     }

     /**
      * Scope a query to return only inactive .
      */
     public function scopeInactiveOnly(Builder $query): void
     {
         $query->where('status', 'inactive');
     }

     /**
      * Scope a query to return get based on status.
      */
     public function scopeByStatus(Builder $query, string $status): void
     {
         $query->where('status', $status);
     }

}
