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
        'duration',
        'category_id',
        'is_active'
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
