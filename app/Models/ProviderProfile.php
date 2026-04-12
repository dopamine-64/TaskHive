<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <- Added this import

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
        'latitude',           
        'longitude',          
        'service_radius_km',
        'average_rating',
        'total_ratings',
        'certifications',
        'is_verified',
    ];

    protected $casts = [
        // 'skills' => 'array', <- Removed this! We are using the custom accessor below instead.
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
        $earthRadius = 6371;

        $haversine = "({$earthRadius} * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query->selectRaw(
            "provider_profiles.*, {$haversine} AS distance",
            [$customerLat, $customerLng, $customerLat] 
        )
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->whereRaw(
            "{$haversine} <= CAST(COALESCE(service_radius_km, '0') AS DECIMAL(10,2))",
            [$customerLat, $customerLng, $customerLat] 
        )
        ->orderBy('distance', 'asc');
    }

    /**
     * CUSTOM ACCESSOR: Safely handle skills whether they are stored as JSON or a comma-separated string.
     */
    protected function skills(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];

                // If it's already an array, return it
                if (is_array($value)) return $value;

                // 1. Try to decode it assuming it's valid JSON
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                // 2. If JSON fails, assume it's a comma-separated string (e.g. "Plumbing, Electrical")
                if (is_string($value)) {
                    // Split by commas, trim whitespace from each skill, and return as array
                    return array_map('trim', explode(',', $value));
                }

                return [];
            },
            // Automatically encode as JSON when saving back to the database
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}