<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'skills',
        'experience_years',
        'hourly_rate',
        'fixed_rate',
        'service_area',
        'latitude',           // <- Added for location search
        'longitude',          // <- Added for location search
        'service_radius_km',
        'average_rating',
        'total_ratings',
        'certifications',
        'is_verified',
    ];

    protected $casts = [
        'skills' => 'array',
        'average_rating' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'provider_id', 'user_id');
    }

    public function updateRating()
    {
        $ratings = $this->ratings;
        if ($ratings->count() > 0) {
            $this->average_rating = $ratings->avg('rating');
            $this->total_ratings = $ratings->count();
            $this->save();
        }
    }

    /**
     * Scope a query to only include providers whose service radius 
     * covers the given customer coordinates (Haversine formula).
     */
    public function scopeAvailableInArea($query, $customerLat, $customerLng)
    {
        // 6371 is the radius of the Earth in kilometers
        $earthRadius = 6371;

        // Store the mathematical formula in a string to reuse it cleanly
        $haversine = "({$earthRadius} * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query->selectRaw(
            "provider_profiles.*, {$haversine} AS distance",
            [$customerLat, $customerLng, $customerLat] // Bindings for the SELECT
        )
        // Ensure latitude and longitude actually exist in the DB for this row to prevent math errors
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        // Use whereRaw instead of havingRaw to avoid strict mode SQL grouping errors
        // COALESCE ensures if radius is NULL, it safely treats it as 0 to prevent crashes
        ->whereRaw(
            "{$haversine} <= CAST(COALESCE(service_radius_km, '0') AS DECIMAL(10,2))",
            [$customerLat, $customerLng, $customerLat] // Bindings for the WHERE
        )
        ->orderBy('distance', 'asc');
    }
}