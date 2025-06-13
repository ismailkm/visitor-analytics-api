<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'name',
      'code',
      'address',
      'city',
      'latitude',
      'longitude',
      'status',
      'type'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    /**
     * Scope a query to only include active locations.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }
}
