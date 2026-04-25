<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'latitude',
        'longitude',
        
        'wallet_balance', 

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, ProviderProfile::class);
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'reviewer_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'provider_id');
    }

    public function userPreference()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function providerRecommendations()
    {
        return $this->hasMany(ProviderRecommendation::class);
    }

    public function matchingScores()
    {
        return $this->hasMany(MatchingScore::class);
    }

    public function isProvider()
    {
        return $this->role === 'provider';
    }
}
