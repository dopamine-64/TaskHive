<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchingScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_profile_id',
        'distance_km',
        'location_score',
        'rating_score',
        'price_score',
        'skills_score',
        'history_score',
        'total_score',
        'context',
        'calculated_at',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'location_score' => 'decimal:2',
        'rating_score' => 'decimal:2',
        'price_score' => 'decimal:2',
        'skills_score' => 'decimal:2',
        'history_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'context' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}
