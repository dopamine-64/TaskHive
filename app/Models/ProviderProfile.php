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
}
