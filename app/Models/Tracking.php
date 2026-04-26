<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    // Allow all these columns to be saved via the controller
    protected $fillable = [
        'customer_id',
        'provider_id',
        'service_id',
        'booking_date',
        'booking_time',
        'address',
        'duration',
        'amount',           
        'payment_status',  
        'status',
        'current_lat',
        'current_lng',
        'points_earned',
        'points_used',

    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function calculatePoints()
    {
        // Example: 1 point per 10 BDT, but minimum 1 point if amount > 0
        $points = floor($this->amount / 10);
        return max(1, $points);
    }
}