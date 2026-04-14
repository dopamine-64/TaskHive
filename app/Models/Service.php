<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider_profile_id',
        'title',
        'description',
        'category',
        'subcategory',
        'price',
        'location',        // Ayesha - for search by location
        'rating',          // Ayesha - for search by rating
        'duration',        // Priyo - service duration
        'category_id',     // Priyo - category management
        'is_active',       // Priyo - active/inactive status
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}