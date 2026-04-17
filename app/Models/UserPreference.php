<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_price_min',
        'preferred_price_max',
        'preferred_radius_km',
        'preferred_categories',
        'auto_match_enabled',
    ];

    protected $casts = [
        'preferred_price_min' => 'decimal:2',
        'preferred_price_max' => 'decimal:2',
        'preferred_radius_km' => 'decimal:2',
        'preferred_categories' => 'array',
        'auto_match_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
